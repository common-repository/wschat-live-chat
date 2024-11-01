<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WP_User;
use WSChat\Integrations\Dialogflow\Settings as DialogflowSettings;
use WSChat\Models\Conversation;
use WSChat\Models\Message;
use WSChat\Models\Participant;
use WSChat\Models\Settings;
use WSChat\Models\User;

class WSMessage {


	public static function init() {
		 $self = new self();

		// Get messages
		add_action( 'wp_ajax_nopriv_wschat_get_messages', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_get_messages', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_admin_get_messages', array( $self, 'admin_router' ) );

		// Send a message
		add_action( 'wp_ajax_nopriv_wschat_send_message', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_send_message', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_admin_send_message', array( $self, 'admin_router' ) );

		// Read all messages
		add_action( 'wp_ajax_nopriv_wschat_read_all', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_read_all', array( $self, 'user_router' ) );
		add_action( 'wp_ajax_wschat_admin_read_all', array( $self, 'admin_router' ) );
		add_action( 'wschat_on_start_conversation', array( self::class, 'read_messages_on_start_conversation' ) );

		add_action( 'wschat_conversation_session_ended', array( self::class, 'add_info_on_chat_has_been_ended' ), 10, 2 );

		add_filter( 'wschat_participant_has_joined_the_chat', array( self::class, 'add_info_on_participant_has_joined_the_channel' ), 10, 2 );
		add_filter( 'wschat_agent_is_designed_from_the_chat', array( self::class, 'add_info_on_agent_is_deassigned_from_the_channel' ), 10, 3 );
		add_action( 'wp_ajax_wschat_get_agent_unread_count', array( self::class, 'get_agent_unread_count' ) );
	}

	public function user_router() {
		 $valid = check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce', false );

		Utils::abort_unless(
			$valid,
			array(
				'message' => __( 'It seems the session was expired. Please try refresh the page', 'wschat' ),
			),
			403
		);

		$user = ( new WSUser() )->get_user( false );

		Utils::abort_unless(
			$user,
			array(
				'message' => __( 'It seems the session was expired. Please try refresh the page', 'wschat' ),
			),
			403
		);

		$conversation = ( new WSConversation() )->get_conversation( $user );

		if ( ! $user instanceof User ) {
			return wp_send_json_error( array(), 404 );
		}

		$method = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : Utils::abort();

		$method = str_replace( 'wschat_', '', $method );

		return call_user_func_array( array( $this, $method ), array( $user, $conversation ) );
	}

	public function admin_router() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		Utils::abort_unless_agent();

		$user = wp_get_current_user();

		if ( isset( $_POST['conversation_id'] ) === false ) {
			return wp_send_json_error( array(), 404 );
		}

		$conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( $_POST['conversation_id'] ) : Utils::abort();

		$conversation = ( new Conversation() )->findById( $conversation_id );

		$method = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : Utils::abort();

		$method = str_replace( 'wschat_admin_', '', $method );

		if ( false === $conversation || ! method_exists( $this, $method ) ) {
			Utils::abort();
		}

		return call_user_func_array(
			array( $this, $method ),
			array( $user, $conversation )
		);
	}

	/**
	 * Get messages
	 *
	 * @param User|WP_User $user
	 * @param Conversation $conversation
	 */
	public function get_messages( $user, $conversation ) {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		$participant  = $conversation->participants()->findByUser( $user );
		$participants = collect(
			$conversation->participants()->get(
				array(
					'conversation_id' => $conversation->id,
				)
			)
		);

		Utils::abort_unless( $participant );

		$users = collect(
			( new User() )->get(
				array(
					'id' => $participants->pluck( 'user_id' )->toArray(),
				)
			)
		)->keyBy(
			function ( $user ) {
			return  $user->id;
			}
		);

		$participant_users = array();

		foreach ( $participants as $user_participant ) {
			if ( Participant::TYPE_AGENT === $user_participant['type'] ) {
				$agent = get_user_by( 'ID', $user_participant['user_id'] );
				if ( $agent ) {
					$participant_users[ $user_participant['id'] ] = [
						'id'   => $agent->ID,
						'name' => $agent->display_name,
					];
				}
			} else {
				$participant_users[ $user_participant['id'] ] = $users->get( $user_participant['user_id'] );
			}
		}

		$participant->online(); // Just to make sure that this takes more time compared to read

		if ( $participant->isAgent() === false ) {
			$conversation->touch( $conversation->id );
		}

		if ( $user instanceof User ) {
			$chat_user = $participant;
		} else {
			$chat_user = $conversation->participants()->findByUser( $conversation->chat_user() );
		}

		$message = $conversation->messages();

		$is_online = $chat_user ? $chat_user->isOnline() : false;

		if ( $participant->isAgent() === false ) {
			$is_online = $conversation->are_agents_online();
		}

		// TODO: sanitize post params
		$messages = collect( $message->get( $_POST ) );

		$message_notifications = array();

		if ( $messages->count() ) {
			$participants = $participants->filter(
				function ( $participant_item ) use ( $participant ) {
					if ( $participant->isAgent() ) {
						return  Participant::TYPE_AGENT !== $participant_item['type'];
					}
					return  Participant::TYPE_AGENT === $participant_item['type'];
				}
			)->pluck( 'id' );

			$message_notifications = $conversation->messages()->get_unread_notifications(
				array(
					'messages'     => $messages->pluck( 'id' )->toArray(),
					'no_sender'    => true,
					'participants' => $participants->toArray(),
				)
			);
		}

		$messages = $messages->map(
			function ( $message ) use ( $participant, $chat_user, $message_notifications, $participant_users ) {
				$message                 = ( new Message() )->parseData( $message )->toArray();
				$message['body']['text'] = nl2br( $message['body']['text'] );
				$message['is_me']        = Arr::get( $message, 'participant_id' ) === $participant->id ? true : false;
				$message['is_agent']     = Arr::get( $message, 'participant_id' ) !== $chat_user->id ? true : false;
				$message['is_read']      = true;

				$user = '';
				if ( array_key_exists( Arr::get( $message, 'participant_id' ), $participant_users ) ) {
					$user = $participant_users[ Arr::get( $message, 'participant_id' ) ];
				}
				
				if ( $user ) {
					$message['user'] = ( is_array( $user ) ) ? $user : $user->meta;
				} else {
					$message['user'] = array(
						'name' => __( 'Deleted User', 'wschat' ),
					);
				}

				if ( $participant->isAgent() ) {
					$message['created_at'] = get_date_from_gmt(
						$message['created_at'],
						get_option( 'date_format' ) . ' ' . get_option( 'time_format' )
					);
				}

				if ( $message['is_me'] ) {
					$message['is_read'] = $message_notifications->filter(
						function ( $notification ) use ( $message ) {
							return (int) $message['id'] === (int) $notification->message_id;
						}
					)->count() === 0;
				}

				return $message;
			}
		);

		$settings = Settings::load();
		wp_send_json_success(
			array(
				'messages'        => $messages,
				'unread_count'    => $message->unreadCount( $participant ),
				'status'          => $is_online ? __( $settings->header_online_text, 'wschat' ) : __( $settings->header_offline_text, 'wschat' ),
				'is_online'       => $is_online,
				'conversation_id' => $conversation->id,
				'participants'    => $participant_users,
				'users'           => $users,
			)
		);
	}

	/**
	 * Get messages
	 *
	 * @param User|WP_User $user
	 * @param Conversation $conversation
	 */
	public function send_message( $user, $conversation ) {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		$participant = $conversation->participants()->findByUser( $user );

		/**
		 * Fire a filter hook for adding an agent as participant on new message
		 *
		 * @since 2.0.0
		 */
		if ( is_admin() && ! $participant && apply_filters( 'wschat_add_agent_as_participant_on_new_message', true ) ) {
			$participant = $conversation->participants()->add( $user );
		}

		Utils::abort_unless( $participant );

		$data['type'] = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'text';
		$data['body'] = array();
		$text         = isset( $_POST['content']['text'] ) ? wp_kses_post( $_POST['content']['text'] ) : '';

		Utils::abort_if( '' === $text && count( $_FILES ) === 0 , array( 'message' => 'Either text or an attachment must be there' ), 403 );

		$data['body'] = array(
			'text' => $text,
		);

		if ( isset( $_FILES['attachments']['name'] ) ) {
			$data['body']['attachments'] = [];

			$count = count( $_FILES['attachments']['name'] );

			for ( $index = 0; $index < $count; $index++ ) {
				if ( ! isset( $_FILES['attachments']['tmp_name'][ $index ] ) || ! isset( $_FILES['attachments']['name'][ $index ] ) ) {
					continue;
				}

				$name = sanitize_text_field( $_FILES['attachments']['name'][ $index ] );

				$res = wp_upload_bits(
					$name,
					null,
					file_get_contents( sanitize_text_field( $_FILES['attachments']['tmp_name'][ $index ] ) )
				);

				if ( 'video.mp4' === $name ) {
					$next_index = $index + 1;
					if ( isset( $_FILES['attachments']['name'][ $next_index ], $_FILES['attachments']['tmp_name'][ $next_index ] ) ) {
						$thumb_name = sanitize_text_field( $_FILES['attachments']['name'][ $next_index ] );
						if ( 'thumbnail.png' === $thumb_name ) {
							$index++;
							$thumbnail = wp_upload_bits(
								$thumb_name,
								null,
								file_get_contents( sanitize_text_field( $_FILES['attachments']['tmp_name'][ $next_index ] ) )
							);
							if ( false === $res['error'] && false === $thumbnail['error'] ) {
								$res['thumbnail'] = $thumbnail['url'];
							}
						}
					}
				}
				// TODO: Need to check allowed mime types or add custom mime types
				if ( false === $res['error'] ) {
					$res['name']                   = $name;
					$data['body']['attachments'][] = Arr::except( $res, 'file' );
				}
			}
		}

		$message = self::add_message( $conversation, $participant, $data );

		$response = array(
			'id'      => $message->id,
			'message' => __( 'Message has been sent', 'wschat' ),
		);

		$participants = $conversation->participants()->applyFilters(
			array(
				'conversation_id' => $conversation->id,
				'type'            => Participant::TYPE_AGENT,
			)
		)->select( 'user_id' )->get();

		$participants = array_map(
			function( $participant ) {
					return $participant->user_id;
			},
			$participants
		);


		$dialogflow_settings = DialogflowSettings::load();

		if ( $conversation->are_agents_online() === false && $dialogflow_settings->enabled() === false ) {
			$settings                  = WSSettings::get_widget_settings();
			$response['offline_reply'] = __( $settings->offline_auto_reply_text, 'wschat' );
		}

		wp_send_json_success( $response );
	}

	public static function add_message( $conversation, $participant, $data ) {

		/**
		 * Fire a filter hook for processing the data before sending
		 *
		 * @since 2.0.0
		 * @param $data
		 */
		$data = apply_filters( 'wschat_process_message_data_before_send', $data );

		$message = $conversation->messages()->add( $participant, $data );

		/**
		 * Fire an action hook after sending a message
		 *
		 * @since 2.0.0
		 * @param $message
		 * @param $participant
		 * @param $conversation
		 */
		do_action( 'wschat_on_send_message', $message, $participant, $conversation );

		/**
		 * Fire an action hook for by whom the message is being send
		 *
		 * @since 2.0.0
		 * @param $message
		 * @param $participant
		 * @param $conversation
		 */
		do_action( 'wschat_on_send_message_' . $participant->type, $message, $participant, $conversation );

		return $message;
	}

	/**
	 * Read all the unred messages on the logged in user
	 *No messages were tagged
	 *
	 * @param User|WP_User $user
	 * @param Conversation $conversation
	 */
	public function read_all( $user, $conversation ) {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		$participant = $conversation->participants()->findByUser( $user );

		Utils::abort_unless( $participant );

		$conversation->messages()->readAll( $participant );

		wp_send_json_success(
			array(
				'message' => __( 'Messages has been marked as read', 'wschat' ),
			)
		);
	}

	public static function read_messages_on_start_conversation( $conversation ) {
		$participant = null;
		if ( Utils::isAgent() ) {
			$participant = ( new Participant( $conversation['id'] ) )->findByUser( wp_get_current_user() );
		} else {
			$user = ( new WSUser() )->get_user( false );
			if ( $user ) {
				$participant = ( new Participant( $conversation['id'] ) )->findByUser( $user );
			}
		}

		if ( $participant ) {
			$participant && ( new Message( $conversation['id'] ) )->readAll( $participant );
		}
	}

	public static function add_info_on_chat_has_been_ended( Conversation $conversation, Participant $participant ) {
		$data['type'] = 'info';
		$data['body'] = array();

		$agent        = new WP_User( $participant->user_id );
		$data['body'] = array(
			'text' => __( 'Chat has been ended by ' . $agent->display_name , 'wschat' ),
			// translators: Agent name
			'text' => __( sprintf( '%s has ended the chat', $agent->display_name ) , 'wschat' ),
		);
		self::add_message( $conversation, $participant, $data );
	}

	public static function add_info_on_participant_has_joined_the_channel( Conversation $conversation, Participant $participant ) {
		$data['type'] = 'info';

		$agent = new WP_User( $participant->user_id );

		$data['body'] = array(
			'text' => __( $agent->display_name . ' have joined the chat', 'wschat' ),
		);

		self::add_message( $conversation, $participant, $data );
	}

	public static function add_info_on_agent_is_deassigned_from_the_channel( Conversation $conversation, Participant $participant, $agent_id ) {
		$data['type'] = 'info';

		$agent = new WP_User( $agent_id );

		$data['body'] = array(
			'text' => __( $agent->display_name . ' has left the chat', 'wschat' ),
		);

		self::add_message( $conversation, $participant, $data );
	}

	public static function get_agent_unread_count() {
		$unread_count = Message::filter_unread_notifications(
			array(
				'agent_id' => array( get_current_user_id() ),
			)
		)->count();
		wp_send_json_success(
			array(
				'unread_count' => $unread_count,
			)
		);
	}
}
