<?php
namespace WSChat\Integrations;

use Twilio\Rest\Client;
use WSChat\Integrations\Dialogflow\Settings as DialogflowSettings;
use WSChat\Models\Conversation;
use WSChat\Models\Message;
use WSChat\Models\Participant;
use WSChat\Models\Settings;
use WSChat\Models\User;
use WSChat\Utils;
use WSChat\WSSettings;

class Integration {
	public static function init() {
		add_action( 'wschat_settings_tab_integrations', array( self::class, 'load_integrations_settings' ) );
		add_filter( 'wschat_settings_saving_integrations', array( self::class, 'save_settings' ) );

		add_filter( 'wschat_settings_saving_integrations_wsdesk', array( self::class, 'save_wsdesk_options' ) );
		add_filter( 'wschat_settings_saving_integrations_chatgpt', array( self::class, 'save_chatgpt_options' ) );
		add_filter( 'wschat_settings_saving_integrations_dialogflow', array( self::class, 'save_dialogflow_options' ) );

		add_filter( 'wschat_get_settings', array( self::class, 'update_wsdesk_status' ) );

		add_action( 'wschat_settings_tab_integrations_dialogflow', array( self::class, 'load_dialogflow_settings' ) );
		add_action( 'wschat_settings_tab_integrations_wsdesk', array( self::class, 'load_wsdesk_settings' ) );
		add_action( 'wschat_settings_tab_integrations_chatgpt', array( self::class, 'load_chatgpt_settings' ) );

		add_action( 'wschat_on_send_message_' . Participant::TYPE_USER, array( __CLASS__, 'send_whatsapp_message_to_agents_on_new_message' ), 10, 3 );
		add_action( 'wschat_on_send_message_' . Participant::TYPE_GUEST, array( __CLASS__, 'send_whatsapp_message_to_agents_on_new_message' ), 10, 3 );

		add_action( 'wp_ajax_wschat_admin_create_wsdesk_ticket', array( self::class, 'create_wsdesk_ticket' ) );
	}

	public static function get_menus() {
		$menus = array(
			array(
				'title' => __( 'Dialogflow' ),
				'slug'  => 'dialogflow',
				'icon'  => Utils::get_resource_url( '/resources/img/dialogflow.png' ),
			),
			array(
				'title' => __( 'WSDesk' ),
				'slug'  => 'wsdesk',
				'icon'  => Utils::get_resource_url( '/resources/img/wsdesk.png' ),
			),
			array(
				'title' => __( 'OpenAI - ChatGPT' ),
				'slug'  => 'chatgpt',
				'icon'  => Utils::get_resource_url( '/resources/img/openai_icon.svg' ),
			),
		);


		/**
		 * Fire a filter hook for admin settings integration menu
		 *
		 * @param $menu
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wschat_admin_settings_integrations_menu', $menus );
	}

	public static function get_current_tab() {
		$subtab = Utils::get_param( 'subtab' );

		return ! empty( $subtab ) ? $subtab : self::get_default_tab();
	}

	public static function get_default_tab() {

		/**
		 * Apply a filter hook for integration settings default sub tab
		 *
		 * @since 2.0.0
		 *
		 */
		 return apply_filters( 'wschat_settings_integrations_default_subtab', 'dialogflow' );
	}

	public static function save_settings( Settings $settings ) {
		$subtab   = self::get_current_tab();

		/**
		 * Fire a filter hook for saving settings integration
		 *
		 * @param $settings
		 *
		 * @since 2.0.0
		 */
		$settings = apply_filters( 'wschat_settings_saving_integrations_' . $subtab , $settings );

		return $settings;
	}

	public static function load_integrations_settings() {
		$data['menus']      = self::get_menus();
		$data['active_tab'] = self::get_current_tab();

		$active_tab = array_filter(
			$data['menus'],
			function ( $menu ) use ( $data ) {
			return $menu['slug'] === $data['active_tab'];
			}
		);

		if ( count( $active_tab ) === 0 ) {
			return;
		}

		Utils::include_file( '/resources/views/admin/settings/integrations.php', $data );
	}

	public static function load_dialogflow_settings() {
		$data['wschat_options']      = WSSettings::get_widget_settings();
		$data['dialogflow_settings'] = DialogflowSettings::load();
		$data['language_codes']      = DialogflowSettings::get_language_codes();

		Utils::include_file( '/resources/views/admin/settings/integrations/dialogflow.php', $data );
	}

	public static function load_wsdesk_settings() {
		$data['wschat_options'] = WSSettings::get_widget_settings();
		$data['integration']    = self::class;

		Utils::include_file( '/resources/views/admin/settings/integrations/wsdesk.php', $data );
	}

	public static function load_chatgpt_settings() {
		$data['wschat_options'] = WSSettings::get_widget_settings();
		$data['integration']    = self::class;

		Utils::include_file( '/resources/views/admin/settings/integrations/chatgpt.php', $data );
	}

	public static function load_whatsapp_settings() {
		$data['wschat_options'] = WSSettings::get_widget_settings();

		Utils::include_file( '/resources/views/admin/settings/integrations/whatsapp.php', $data );
	}

	public static function save_dialogflow_options( Settings $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$dialogflow = DialogflowSettings::load();

		$dialogflow->enabled = isset( $_POST['dialogflow_enable'] ) ? true : false;

		if ( isset( $_POST['dialogflow_project_id'] ) ) {
			$dialogflow->project_id = sanitize_text_field( $_POST['dialogflow_project_id'] );
		}

		if ( isset( $_POST['dialogflow_credentials'] ) ) {
			$cleaned_json_key = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', sanitize_textarea_field( $_POST['dialogflow_credentials'] ) );
			$dialogflow->credentials = json_decode( stripslashes( $cleaned_json_key ), true );
		}

		if ( isset( $_POST['dialogflow_language_code'] ) ) {
			$dialogflow->language_code = sanitize_text_field( $_POST['dialogflow_language_code'] );
		}

		$dialogflow->save();

		return $settings;
	}

	public static function update_wsdesk_status( Settings $settings ) {
		if ( false === self::is_wsdesk_installed() ) {
			$settings->wsdesk_enabled = false;
		}

		return $settings;
	}

	public static function save_wsdesk_options( Settings $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings->wsdesk_enabled = self::is_wsdesk_installed() && isset( $_POST['wsdesk_enabled'] ) ? true : false;

		return $settings;
	}
	public static function save_chatgpt_options( Settings $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings->chatgpt_enabled = isset( $_POST['chatgpt_enabled'] ) ? true : false;
		$settings->chatgpt_api_key = isset( $_POST['chatgpt_api_key'] ) ? sanitize_text_field( $_POST['chatgpt_api_key'] ) : null;

		return $settings;
	}

	public static function is_wsdesk_installed() {
		$wsdesk_plugins = array(
			'wsdesk-premium/wsdesk.php',
			'wsdesk-basic/wsdesk.php',
			'wsdesk/wsdesk.php',
			'elex-helpdesk-customer-support-ticket-system/elex-wordpress-helpdesk.php',
			'elex-wordpress-helpdesk/elex-wordpress-helpdesk.php',
			'wsdesk-helpdesk-customer-support-ticket-system/wsdesk.php',
		);

		return count( array_intersect( $wsdesk_plugins, (array) get_option( 'active_plugins' ) ) ) > 0;
	}

	public static function save_whatsapp_options( Settings $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings->whatsapp_enable = isset( $_POST['whatsapp_enable'] ) ? true : false;

		if ( isset( $_POST['whatsapp_twilio_number'] ) ) {
			$settings['whatsapp_twilio_number'] = sanitize_text_field( $_POST['whatsapp_twilio_number'] );
		}

		if ( isset( $_POST['whatsapp_twilio_sid'] ) ) {
			$settings['whatsapp_twilio_sid'] = sanitize_text_field( $_POST['whatsapp_twilio_sid'] );
		}

		if ( isset( $_POST['whatsapp_twilio_auth_token'] ) ) {
			$settings['whatsapp_twilio_auth_token'] = sanitize_text_field( $_POST['whatsapp_twilio_auth_token'] );
		}

		return $settings;
	}

	public static function send_whatsapp_message_to_agents_on_new_message( Message $message, Participant $participant, Conversation $conversation ) {
		$settings = WSSettings::get_widget_settings();

		if ( true !== $settings->whatsapp_enable ) {
			return;
		}

		$filters = array(
			'type'            => Participant::TYPE_AGENT,
			'conversation_id' => $conversation->id,
		);

		$agents = $conversation->participants()->get( $filters );

		$body     = $message->get_text();
		$template = 'new_message';

		/**
		 * Apply a filter to modify whatsapp message body
		 *
		 * @param string $body
		 * @param Message $message
		 * @param Participant $participant
		 * @param Conversation $conversation
		 *
		 * @since 2.0.0
		 *
		 */
		$body = apply_filters( 'wschat_whatsapp_message_body_' . $template, $body, $message, $participant, $conversation );

		if ( empty( $body ) ) {
			return;
		}

		self::send_whatsapp_message_to_agents( $agents, $body, $conversation );
	}

	public static function send_whatsapp_message_to_agents( $agents, $body, $conversation ) {
		$settings      = WSSettings::get_widget_settings();
		$twilio_client = self::get_twilio_client();

		/**
		 * Apply a filter to get the meta key of a user
		 *
		 * Different plugins can use different kind of meta key to store user mobile_no
		 * Use this filter to change meta_key
		 *
		 * @param string $mobile_no meta_key
		 *
		 * @since 2.0.0
		 *
		 */
		$phone_number_meta_key = apply_filters( 'wschat_agent_phone_number_meta_key', 'mobile_no' );

		foreach ( $agents as $agent ) {
			$agent_phone_number = get_user_meta( $agent['user_id'], $phone_number_meta_key, true );

			/**
			 * Apply a filter to change agent mobile_no
			 *
			 * Allow to extent or change agent mobile_no,
			 * return null to ignore
			 *
			 * @param string $agent_phone_number
			 * @param User $agent
			 * @param Conversation $conversation
			 *
			 * @since 2.0.0
			 *
			 */
			$agent_phone_number = apply_filters( 'wschat_agent_phone_number', $agent_phone_number, $agent, $conversation );

			if ( ! $agent_phone_number ) {
				continue;
			}

			$twilio_client->messages->create(
				'whatsapp:' . $agent_phone_number,
				array(
					'from' => 'whatsapp:' . $settings->whatsapp_twilio_number,
					/**
					 * Fire a filter hook for whatsapp message body
					 *
					 * @param $body
					 *
					 * @since 2.0.0
					 */
					'body' => apply_filters( 'wschat_whatsapp_message_body', $body ),
				)
			);
		}
	}

	public static function get_twilio_client() {
		$settings = WSSettings::get_widget_settings();

		return new Client( $settings->whatsapp_twilio_sid, $settings->whatsapp_twilio_auth_token );
	}

	public static function create_wsdesk_ticket() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , true );
		$email      = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$subject    = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
		$desc       = isset( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$message_id = isset( $_POST['message_id'] ) ? sanitize_text_field( $_POST['message_id'] ) : '';

		$desc     = str_replace( "\n", '<br/>', $desc );
		$submit   = 'label_LL01';
		$assignee = array();
		$tags     = array();
		$vendor   = '';

		if ( EH_CRM_WOO_VENDOR ) {
			$vendor = EH_CRM_WOO_VENDOR;
		}
		$id                      = email_exists( $email );
		$args                    = array(
			'ticket_author'   => ( ( $id ) ? $id : 0 ),
			'ticket_email'    => $email,
			'ticket_title'    => $subject,
			'ticket_content'  => $desc,
			'ticket_category' => 'raiser_reply',
			'ticket_vendor'   => $vendor,
		);
		$meta                    = array();
		$meta['ticket_assignee'] = $assignee;
		$meta['ticket_tags']     = $tags;
		$meta['ticket_label']    = $submit;
		$meta['ticket_source']   = 'Agent';
		$id                      = eh_crm_insert_ticket( $args, $meta );
		$send                    = eh_crm_get_settingsmeta( '0', 'auto_send_creation_email' );
		$response                = \CRM_Ajax::eh_crm_fire_email( 'new_ticket', $id );

		if ( $message_id ) {
			$message = Message::find( $message_id );

			if ( $message ) {
				$body = $message->body;

				$body['ticket_id'] = $id;

				$message->update(
					$message_id,
					array(
						'body' => $body,
					)
				);
			}
		}

		wp_send_json_success(
			array(
				'message' => __( 'Ticket created successfully. Ticket ID is #', 'wschat' ) . $id,
			)
		);
		die();
	}
}
