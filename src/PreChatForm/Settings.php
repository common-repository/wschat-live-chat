<?php

namespace WSChat\PreChatForm;

use WSChat\Models\Settings as SettingsModel;
use WSChat\PreChatForm\Fields\FormField;
use WSChat\Utils;
use WSChat\WSConversation;
use WSChat\WSUser;
use Illuminate\Support\Str;
use WSChat\WSMessage;
use WSChat\WSSettings;

/**
 * The pre chat form settings
 */
class Settings {

	const SETTINGS_KEY = 'wschat_prechat_form_settings';

	/* @var PreChatForm form instance */
	protected $form;

	public static function init() {

		add_action( 'wp_ajax_wschat_pre_chat_frm_add_field', array( self::class, 'add_field' ) );
		add_action( 'wp_ajax_wschat_pre_chat_frm_toggle_field_status', array( self::class, 'toggle_field' ) );
		add_action( 'wp_ajax_wschat_pre_chat_frm_toggle_field_mandatory', array( self::class, 'toggle_mandatory' ) );
		add_action( 'wp_ajax_wschat_pre_chat_frm_delete_field', array( self::class, 'delete_field' ) );
		add_action( 'wp_ajax_reset_settings', array( self::class, 'reset_settings' ) );

		add_action( 'wp_ajax_wschat_pre_chat_frm_rearrange_fields', array( self::class, 'rearrange' ) );

		add_action( 'wp_ajax_wschat_pre_chat_frm_submit', array( self::class, 'form_submit' ) );
		add_action( 'wp_ajax_nopriv_wschat_pre_chat_frm_submit', array( self::class, 'form_submit' ) );

		add_action( 'wschat_settings_saved_general', array( self::class, 'save_basic_settings' ) );

		add_filter( 'wschat_get_settings', array( self::class, 'append_settings' ) );
		// add_filter( 'wschat_can_start_a_conversation', array( self::class, 'can_user_start_a_conversation' ) );
		add_filter( 'wschat_user_conversation', array( self::class, 'can_user_start_a_conversation' ) );
		add_filter( 'wschat_start_conversation_failed_response', array( self::class, 'apppend_start_conversation_failed_response' ) );

		add_action( 'wschat_after_submit_pre_chat_form', array( self::class, 'on_prechat_submission' ), 10, 2 );

		add_action( 'wschat_conversation_session_ended', array( self::class, 'on_conversation_ended' ) );
	}

	public static function can_user_start_a_conversation( $conversation ) {
		$form = ( new self() )->get_form();

		if ( isset( $conversation['prechat_form'] ) ) {
			$conversation['prechat_form'] = array();
		}

		$conversation['prechat_form']['must_show_form'] = $form->needs_to_show_form( WSSettings::get_widget_settings() );

		return $conversation;
	}

	public static function apppend_start_conversation_failed_response( $data ) {
		$data['reason']             = 'pre_chat_form';
		$data['reason_description'] = __( 'You need to submit the prechat form details to start conversation' );

		return $data;
	}

	public static function save_basic_settings() {
		check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings = new self();

		$form = $settings->get_form();

		if ( isset( $_POST['pre_chat_form']['enable'] ) ) {
			$form->enable();
		} else {
			$form->disable();
		}

		$form->label = isset( $_POST['pre_chat_form']['label'] ) ? sanitize_text_field( $_POST['pre_chat_form']['label'] ) : $form->label;

		$form->mode = isset( $_POST['pre_chat_form']['mode'] ) ? PreChatForm::MODE_OFFLINE : PreChatForm::MODE_ALL;

		$settings->save_form( $form );
	}

	public static function add_field() {
		Utils::abort_unless( check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce', false ), array( 'message' => __( 'Invalid request' ) ) );

		$data['name']      = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$data['slug']      = Str::slug( $data['name'], '_' );
		$data['type']      = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$data['mandatory'] = isset( $_POST['mandatory'] ) ? ( sanitize_text_field( $_POST['mandatory'] ) === 'yes' ) : false;
		$data['status']    = FormField::STATUS_ACTIVE;
		$data['options']   = [];

		if ( isset( $_POST['options'] ) && is_array( $_POST['options'] ) && count( $_POST['options'] ) ) {
			$no_of_options = count( $_POST['options'] );

			for ( $i = 0; $i < $no_of_options; $i++ ) {
				$option = isset( $_POST['options'][ $i ] ) ? sanitize_text_field( $_POST['options'][ $i ] ) : false;
				if ( $option ) {
					$data['options'][] = $option;
				}
			}
		}

		if ( empty( $data['name'] ) || empty( $data['type'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Name cannot be empty', 'wschat' ),
				),
				422
			);
		}

		$field = FormField::build( $data );

		if ( $field->must_have_options() && $field->has_options() === false ) {
			Utils::abort(
				array(
					'message' => __( 'Atleast one option is required', 'wschat' ),
				),
				422
			);
		}

		$self = new self();

		$form = $self->get_form();

		if ( $form->has_field( $field ) ) {
			Utils::abort(
				array(
					'message' => __( 'The field name exists already', 'wschat' ),
				),
				422
			);
		}

		$form->add_field( $field );

		$self->save_form( $form );

		Utils::abort(
			array(
				'message' => __( 'Field has been added successfully', 'wschat' ),
			),
			200
		);
	}

	/**
	 * Get pre chat form instance
	 *
	 * @return PreChatForm
	 */
	public function get_form() {
		if ( $this->form && false ) {
			return $this->form;
		}

		$conf = get_option( self::SETTINGS_KEY, $this->get_default_data() );

		$this->form = PreChatForm::build( $conf );

		return $this->form;
	}

	public function get_default_data() {
		$defaults = array(
			'enable' => true,
			'label'  => 'Tell us about you',
			'mode'   => PreChatForm::MODE_ALL,
			'fields' => array(
				array(
					'name'      => 'Name',
					'slug'      => 'name',
					'type'      => 'text',
					'mandatory' => true,
					'deletable' => false,
					'status'    => 1,
				),
				array(
					'name'      => 'Email',
					'slug'      => 'email',
					'type'      => 'email',
					'mandatory' => true,
					'deletable' => false,
					'status'    => 1,
				),
			),
		);

		return $defaults;
	}

	public function save_form( PreChatForm $form ) {
		update_option( self::SETTINGS_KEY, $form->toArray() );
	}

	public static function toggle_mandatory() {
		Utils::abort_unless( check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce', false ), array( 'message' => __( 'Invalid request' ) ) );
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

		$settings = new self();
		$form     = $settings->get_form();

		$field = $form->find_field( $name );

		if ( false === $field ) {
			Utils::abort(
				array(
					'message' => __( 'Invalid field', 'wschat' ),
				),
				422
			);
		}

		if ( $field->mandatory() ) {
			$field->mandatory = false;
		} else {
			$field->mandatory = true;
		}

		$settings->save_form( $form );

		Utils::abort(
			array(
				'message' => __( 'Field has been updated' ),
			),
			200
		);
	}

	public static function toggle_field() {
		Utils::abort_unless( check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce', false ), array( 'message' => __( 'Invalid request' ) ) );
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

		$settings = new self();
		$form     = $settings->get_form();

		$field = $form->find_field( $name );

		if ( false === $field ) {
			Utils::abort(
				array(
					'message' => __( 'Invalid field', 'wschat' ),
				),
				422
			);
		}

		if ( $field->active() ) {
			$field->deactivate();
		} else {
			$field->activate();
		}

		$settings->save_form( $form );

		Utils::abort(
			array(
				'message' => __( 'Field has been ' . ( $field->active() ? 'activated' : 'deactivated' ), 'wschat' ),
				'status'  => $field->status,
				'active'  => $field->active(),
			),
			200
		);
	}

	public static function delete_field() {
		Utils::abort_unless( check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce', false ), array( 'message' => __( 'Invalid request' ) ) );

		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

		$settings = new self();
		$form     = $settings->get_form();

		$field = $form->find_field( $name );

		if ( false === $field ) {
			Utils::abort(
				array(
					'message' => __( 'Invalid field', 'wschat' ),
				),
				422
			);
		}

		$form->remove_field( $field );
		$settings->save_form( $form );

		Utils::abort(
			array(
				'message' => __( 'Field has been deleted', 'wschat' ),
			),
			200
		);
	}

	public static function reset_settings() {
		global $wpdb;
		$query = 'DELETE FROM ' . $wpdb->prefix . "options WHERE option_name IN ('wschat_settings','wschat_prechat_form_settings','wschat_dialogflow_settings','wschat_site_settings')";
		wpFluent()->statement( $query );
		wp_send_json_success(
			array(
				'message' => __( 'Settings Restored to default', 'wschat' ),
			)
		);
	}

	public static function form_submit() {
		Utils::abort_unless( check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , false ), array( 'message' => __( 'Invalid request' ) ) );
		$self = new self();

		$form = $self->get_form();

		$rules = array();
		$data  = array();

		foreach ( $form->get_fields() as $field ) {
			if ( $field->active() === false ) {
				continue;
			}
			$field_rules = array();

			$data[ $field->slug ] = array(
				'name' => $field->name,
			);

			if ( FormField::TYPE_CHECKBOX === $field->type ) {
				// TODO: Need to sanitize since it is an array
				$data[ $field->slug ]['value'] = isset( $_POST[ $field->slug ] ) ? array() : array();
				$count                         = count( $_POST[ $field->slug ] );

				for ( $i = 0; $i < $count; $i++ ) {
					$data[ $field->slug ]['value'][] = isset( $_POST[ $field->slug ][ $i ] ) ? sanitize_text_field( $_POST[ $field->slug ][ $i ] ) : '';
				}
			} elseif ( FormField::TYPE_EMAIL === $field->type ) {
				$data[ $field->slug ]['value'] = isset( $_POST[ $field->slug ] ) ? sanitize_text_field( $_POST[ $field->slug ] ) : '';

				if ( ! empty( $data[ $field->slug ]['value'] ) ) {
					if (
						! filter_var( $data[ $field->slug ]['value'], FILTER_VALIDATE_EMAIL ) ||
						! preg_match( '/@.+\./', $data[ $field->slug ]['value'] )
					) {
						$field_rules['required'] = __( 'The ' . $field->name . ' is not a valid email' );
					}
				}
			} elseif ( FormField::TYPE_NUMBER === $field->type ) {
				$data[ $field->slug ]['value'] = isset( $_POST[ $field->slug ] ) ? sanitize_text_field( $_POST[ $field->slug ] ) : '';

				if ( ! empty( $data[ $field->slug ]['value'] ) && ! is_numeric( $data[ $field->slug ]['value'] ) ) {
					$field_rules['required'] = __( 'The ' . $field->name . ' is not a valid number' );
				}
			} else {
				$data[ $field->slug ]['value'] = isset( $_POST[ $field->slug ] ) ? sanitize_text_field( $_POST[ $field->slug ] ) : '';
			}

			if ( $field->mandatory() && empty( $data[ $field->slug ]['value'] ) ) {
				$field_rules['required'] = __( 'The ' . $field->name . ' is requried' );
			}

			if ( count( $field_rules ) ) {
				$rules[ $field->slug ] = $field_rules;
			}
		}

		Utils::abort_if( count( $rules ), $rules , 422 );

		/**
		 * Fire an action hook before submitting pre-chat form
		 *
		 * @since 2.0.0
		 * @param $rules
		 * @param $data
		 */
		do_action( 'wschat_before_submit_pre_chat_form', $rules, $data );

		$user = ( new WSUser() )->get_user();

		$conversation = ( new WSConversation() )->get_conversation( $user );

		$conversation->add_meta( 'pre_chat_form', $data );

		/**
		 * Fire an action  hook after submitting pre-chat form
		 *
		 * @since 2.0.0
		 * @param $conversation
		 * @param $user
		 */
		do_action( 'wschat_after_submit_pre_chat_form', $conversation, $user );

		wp_send_json_success(
			array(
				'message' => __( 'Prechat form has been submitted successfully' ),
			)
		);
	}

	public static function append_settings( SettingsModel $settings ) {
		$prechat_form = ( new self() )->get_form();

		$settings['prechatform']                   = $prechat_form;
		$settings['prechatform']['must_show_form'] = $prechat_form->needs_to_show_form( $settings );
		$settings['prechatform']['action']         = 'wschat_pre_chat_frm_submit';
		/* var_dump($settings['prechatform']['must_show_form']);
		die; */

		return $settings;
	}

	public static function rearrange() {
		Utils::abort_unless( check_ajax_referer( 'wschat_save_settings', 'wschat_settings_nonce', false ), array( 'message' => __( 'Invalid request' ) ) );

		$form = ( new self() )->get_form();

		$fields = $form->get_fields();

		$index = isset( $_POST['index'] ) ? sanitize_text_field( $_POST['index'] ) : '';
		$dir   = isset( $_POST['dir'] ) ? sanitize_text_field( $_POST['dir'] ) : '';

		if ( ! $index || ! $dir ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request' ),
				),
				422
			);
		}

		$new_index            = $index + $dir;
		$tmp                  = $fields[ $new_index ];
		$fields[ $new_index ] = isset( $fields[ $index ] ) ? $fields[ $index ] : array();
		$fields[ $index ]     = $tmp;

		$form->set_fields( $fields );

		( new self() )->save_form( $form );

		wp_send_json_success(
			array(
				'message' => __( 'Fields have been rearranged' ),
			)
		);
	}

	public static function on_prechat_submission( $conversation, $user ) {
		// Hide the prechatform
		$conversation->add_meta( 'show_prechat_form', 'hide' );

		$participant = $conversation->participants()->findByUser( $user );

		$meta = $conversation->get_meta();
		$text = '';

		$user_meta = array();

		foreach ( $meta['pre_chat_form'] as $key => $form_field ) {
			$text .= $form_field['name'] . ': ';
			if ( is_array( $form_field['value'] ) ) {
				$text .= implode( ', ', $form_field['value'] );
			} else {
				$text .= $form_field['value'];
			}

			if ( is_user_logged_in() === false ) {
				if ( in_array( $key, array( 'name', 'email' ) ) ) {
					$user_meta[ $key ] = $form_field['value'];
				}
			}

			$text .= PHP_EOL;
		}

		if ( count( $user_meta ) > 0 ) {
			$user->update(
				array(
					'meta' => wp_parse_args( $user_meta, $user->meta ),
				)
			);
		}

		$data = array(
			'type' => 'text',
			'body' => array(
				'text' => $text,
			),
		);

		WSMessage::add_message( $conversation, $participant, $data );
	}

	public static function on_conversation_ended( $conversation ) {
		$conversation->add_meta( 'show_prechat_form', 'show' );
	}
}

