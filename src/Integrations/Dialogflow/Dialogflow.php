<?php

namespace WSChat\Integrations\Dialogflow;

use Illuminate\Support\Arr;
use Throwable;
use WP_User;
use WSChat\Models\Conversation;
use WSChat\Models\Message;
use WSChat\Models\Participant;
use WSChat\Utils;

class Dialogflow {
	/**
	 * Settings object
	 *
	 * @var Settings $settings
	 * */
	private static $settings;

	public static function init() {
		$self = new self();

		if ( $self->get_settings()->enabled() === false ) {
			return;
		}

		add_action( 'wschat_on_send_message', array( $self, 'on_send_message' ), 10, 3 );
		add_action( 'wschat_settings_tab_integrations_dialogflow', array( $self, 'settings_view' ), 10, 3 );
	}

	public function settings_view() {
	   $data['dialogflow_settings'] = $this->get_settings();

		Utils::include_file( '/resources/views/admin/settings/integrations/dialogflow.php', $data );
	}

	public function on_send_message( Message $message, Participant $participant, Conversation $conversation ) {
		if ( Participant::TYPE_AGENT === $participant->type ) {
			return false;
		}

		// TODO: To prevent intereption between agent messages and dialogflow messages
		if ( $conversation->are_agents_online() ) {
			return;
		}

		try {
			$client = new Client( Settings::load() );

			$dialogflow_response = $client->detect_intent( $message );
			$reply               = $client->get_fulfillment_text( $dialogflow_response );

		} catch ( Throwable $th ) {
			return;
		}

		/**
		 * Fire a filter hook for dialog flow agent id
		 *
		 * @param $this->get_settings()->get_agent_id()
		 *
		 * @since 2.0.0
		 */
		$agent_id = apply_filters( 'wschat_dialog_flow_agent_id', $this->get_settings()->get_agent_id() );

		$user = new WP_User( $agent_id );

		$participant = $conversation->participants()->add( $user );

		$data['type'] = 'text';
		$data['body'] = array(
			'text'       => $reply,
			'dialogflow' => array(
				'status'   => Arr::has( $dialogflow_response, 'fulfillmentMessages' ),
				'messages' => $dialogflow_response,
			),
		);

		/**
		 * Fire a filter hook for processing message data before sending
		 *
		 * @param $data
		 *
		 * @since 2.0.0
		 */
		$data = apply_filters( 'wschat_process_message_data_before_send', $data );

		$message = $conversation->messages()->add( $participant, $data );

		/**
		 * Fire an action hook on message send
		 *
		 * @param $message
		 *
		 * @param $participant
		 *
		 * @param $conversation
		 *
		 * @since 2.0.0
		 */
		do_action( 'wschat_on_send_message', $message, $participant, $conversation );
	}

	public function get_settings() {
		if ( ! self::$settings ) {
			self::$settings = Settings::load();
		}

		return self::$settings;
	}
}
