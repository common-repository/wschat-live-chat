<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WSChat\Models\Conversation;
use WSChat\Models\Message;
use WSChat\Models\Participant;
use WSChat\Models\Settings;
use WSChat\Transformers\ConversationTransformer;

class WSPusher {

	public static $pusher = null;

	public static function init() {
		$self = new self();
		add_filter( 'wschat_settings_saving_general', array( $self, 'save_config' ), 100 );
		add_filter( 'wschat_get_settings', array( $self, 'get_default_settings' ), 100 );

		add_action( 'wschat_settings_saved_general', array( $self, 'validate_pusher_config' ) );
		add_action( 'wschat_on_send_message', array( $self, 'send_message' ), 10, 3 );

		add_action( 'wp_ajax_nopriv_wschat_pusher_auth', array( $self, 'pusher_auth' ) );
		add_action( 'wp_ajax_wschat_pusher_auth', array( $self, 'pusher_auth' ) );

		add_action( 'wp_ajax_wschat_admin_pusher_auth', array( $self, 'admin_pusher_auth' ) , 10, 3 );

		add_action( 'wp_ajax_general_pusher_verify', array( $self, 'general_pusher_verify_and_save' ) );
	}

	public function get_default_settings( $settings ) {
		if ( $settings->has( 'pusher' ) === false ) {
			$settings->pusher = $this->get_default_config();
		}

		return $settings;
	}

	public function get_default_config() {
		return array(
			'app_id'     => '',
			'app_key'    => '',
			'secret_key' => '',
			'cluster'    => '',
		);
	}

	public function save_config( Settings $wschat_settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );
		$config = $wschat_settings->get( 'pusher', $this->get_default_config() );

		if ( isset( $_POST['pusher']['app_id'] ) ) {
			$config['app_id'] = sanitize_text_field( $_POST['pusher']['app_id'] );
		}

		if ( isset( $_POST['pusher']['app_key'] ) ) {
			$config['app_key'] = sanitize_text_field( $_POST['pusher']['app_key'] );
		}

		if ( isset( $_POST['pusher']['cluster'] ) ) {
			$config['cluster'] = sanitize_text_field( $_POST['pusher']['cluster'] );
		}

		if ( isset( $_POST['pusher']['secret_key'] ) ) {
			$config['secret_key'] = sanitize_text_field( $_POST['pusher']['secret_key'] );
		}

		$wschat_settings->pusher = $config;

		return $wschat_settings;
	}

	public function send_message( Message $message, Participant $participant, Conversation $conversation ) {
		if ( false === $this->is_pusher_enabled() ) {
			return false;
		}

		$pusher = self::get_pusher();

		$message_payload             = $message->toArray();
		$message_payload['is_agent'] = $participant->isAgent();

		if ( $message->participant_id === $participant->id ) {
			$agent = get_user_by( 'ID', $participant->user_id );
			if ( $agent ) {
				$message_payload['user'] = [
					'id'   => $agent->ID,
					'name' => $agent->display_name,
				];
			}
		}

		$data['messages'][] = $message_payload;

		$pusher->trigger( 'presence-conversation_' . $message->conversation_id, 'message', $data );

		$conversations = collect( array( $conversation->toArray() ) );

		$pusher->trigger(
			'presence-conversation_' . $message->conversation_id,
			'conversation',
			( new ConversationTransformer( $conversations, wp_get_current_user() ) )->transform()
		);

		$participants = $conversation->participants()->get(
			array(
				'type'            => Participant::TYPE_AGENT,
				'conversation_id' => $conversation->id,
			)
		);

		if ( $participant->isAgent() ) {
			return;
		}

		foreach ( $participants as $agent_participant ) {
			$pusher->trigger( 'presence-agent_' . $agent_participant['user_id'], 'new_message', $message_payload );
		}
	}

	/**
	 * Get Pusher server instance
	 *
	 * @return \Pusher\Pusher
	 */
	public static function get_pusher() {
		if ( self::$pusher ) {
			return self::$pusher;
		}

		$settings = WSSettings::get_widget_settings();

		$options = array(
			'cluster' => Arr::get( $settings, 'pusher.cluster' ),
			'useTLS'  => true,
		);

		self::$pusher = new \Pusher\Pusher(
			Arr::get( $settings, 'pusher.app_key' ),
			Arr::get( $settings, 'pusher.secret_key' ),
			Arr::get( $settings, 'pusher.app_id' ),
			$options
		);

		return self::$pusher;
	}

	public static function is_pusher_enabled() {
		$settings = WSSettings::get_widget_settings();

		return 'pusher' === Arr::get( $settings, 'communication_protocol' );
	}

	public static function can_connect_to_pusher() {
		$pusher = self::get_pusher();

		$response = $pusher->get( '/channels' );

		return is_array( $response ) && 200 === $response['status'];
	}

	public function validate_pusher_config( $wschat_settings ) {
		if ( 'pusher' !== Arr::get( $wschat_settings, 'communication_protocol' ) ) {
			return true;
		}

		if ( true === self::can_connect_to_pusher() ) {
			return true;
		}

		add_action( 'wschat_admin_settings_notices', array( $this, 'invalid_config_notice' ) );
	}

	public function invalid_config_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_attr__( 'Error: WSchat - Unable to connect to the pusher. Please validate the credentials and try again', 'wschat' ); ?></p>
		</div>
<?php
	}

	/**
	 * Pusher auth response
	 */
	public function admin_pusher_auth() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		if ( ! isset( $_POST['channel_name'] ) || ! isset( $_POST['socket_id'] ) || false === Utils::isAgent() ) {
			Utils::abort( array(), 403 );
		}

		$pusher = self::get_pusher();

		$channel_name = sanitize_text_field( $_POST['channel_name'] );
		$socket_id    = sanitize_text_field( $_POST['socket_id'] );

		list($channel_prefix, $conversation_id) = explode( '_', $channel_name );

		// Authorize a global agent channel
		if ( 'presence-agent' === $channel_prefix ) {
			if ( Utils::isAgent() && intval( $conversation_id ) === get_current_user_id() ) {
				$res = $pusher->presence_auth(
					$channel_name,
					$socket_id,
					$conversation_id
				);
				wp_send_json( json_decode( $res ) );
				die;
			}

			Utils::abort(
				array(
					Utils::isAgent(),
					intval( $conversation_id ),
					get_current_user_id(),
				),
				403
			);
		}

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $conversation_id,
			)
		);

		if ( false === $conversation ) {
			Utils::abort( array(), 403 );
		}

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );

		if ( is_object( $participant ) && $participant->id && $participant->type ) {
			$res = $pusher->presence_auth(
				$channel_name,
				$socket_id,
				$participant->id,
				array(
					'type' => $participant->type,
				)
			);
		} else {
			Utils::abort( array(), 403 );
		}

		header( 'Content-type:application/json;charset=utf-8' );
		die( wp_json_encode( json_decode( $res ) ) );
	}

	public function pusher_auth() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		if ( ! isset( $_POST['channel_name'] ) || ! isset( $_POST['socket_id'] ) ) {
			Utils::abort( array(), 403 );
		}

		$pusher = self::get_pusher();

		$channel_name = sanitize_text_field( $_POST['channel_name'] );
		$socket_id    = sanitize_text_field( $_POST['socket_id'] );

		$user = ( new WSUser() )->get_user( false );

		if ( false === $user ) {
			Utils::abort( array(), 403 );
		}

		list($channel_prefix, $conversation_id) = explode( '_', $channel_name );

		$conversation = ( new Conversation() )->find(
			array(
				'chat_user_id' => $user->id,
				'id'           => $conversation_id,
			)
		);

		if ( false === $conversation ) {
			Utils::abort( array(), 403 );
		}

		$participant = $conversation->participants()->findByUser( $user );

		$res = $pusher->presence_auth(
			$channel_name,
			$socket_id,
			$participant->id,
			array(
				'type' => $participant->type,
			)
		);

		header( 'Content-type:application/json;charset=utf-8' );
		die( wp_json_encode( json_decode( $res ) ) );
	}

	public function general_pusher_verify_and_save() {
		if ( isset( $_REQUEST['wschat_settings_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_REQUEST['wschat_settings_nonce'] ), 'wschat_save_settings' ) ) {
			wp_die();
		}

		$status = false;

		if ( isset( $_POST['p_data'] ) && ! empty( $_POST['p_data'] ) ) {

			$pusher_data = array();
			foreach ( array_map( 'sanitize_text_field', $_POST['p_data'] ) as $key => $value ) {
				$pusher_data[ $key ] = sanitize_text_field( $value );
			}
			
			//get pusher server instance
			if ( self::$pusher ) {
				$pusher = self::$pusher;
			} else {
				$options = array(
					'cluster' => $pusher_data['cluster_key'],
					'useTLS'  => true,
				);
		
				self::$pusher = new \Pusher\Pusher(
					$pusher_data['app_key'],
					$pusher_data['secret_key'],
					$pusher_data['app_id'],
					$options
				);
		
				$pusher = self::$pusher;
			}

			$response = $pusher->get( '/channels' );
			$status   = ( ( is_array( $response ) ) && ( 200 === $response['status'] ) );
			if ( true === $status ) {
				$wschat_settings = WSSettings::get_widget_settings();
				$config          = $wschat_settings->get( 'pusher', $this->get_default_config() );
				if ( isset( $pusher_data['app_id'] ) ) {
					$config['app_id'] = sanitize_text_field( $pusher_data['app_id'] );
				}
		
				if ( isset( $pusher_data['app_key'] ) ) {
					$config['app_key'] = sanitize_text_field( $pusher_data['app_key'] );
				}
		
				if ( isset( $pusher_data['cluster_key'] ) ) {
					$config['cluster'] = sanitize_text_field( $pusher_data['cluster_key'] );
				}
		
				if ( isset( $pusher_data['secret_key'] ) ) {
					$config['secret_key'] = sanitize_text_field( $pusher_data['secret_key'] );
				}
		
				$wschat_settings->pusher = $config;
				$wschat_settings->save();
			}
		}
		echo esc_html( $status ); 
		exit;
	}
}
