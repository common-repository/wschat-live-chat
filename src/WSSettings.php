<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WSChat\Integrations\Integration;
use WSChat\PreChatForm\Settings;
use WSChat\Models\Settings as SettingsModel;

class WSSettings {

	public static $settings = null;

	public static function init() {
		add_action( 'wschat_settings_tab_general', array( self::class, 'load_general_settings' ) );
		add_action( 'wschat_settings_tab_restrictions', array( self::class, 'load_restrictions_settings' ) );
		add_action( 'wschat_settings_tab_customization', array( self::class, 'load_customization_settings' ) );
		add_action( 'wschat_settings_tab_email', array( self::class, 'load_email_settings' ) );

		add_action( 'wschat_settings_tab_licence', array( self::class, 'load_license_settings' ) );

		add_filter( 'wschat_settings_saving_general', array( self::class, 'save_general_settings' ) );
		add_filter( 'wschat_settings_saving_restrictions', array( self::class, 'save_restrictions_settings' ) );
		add_filter( 'wschat_settings_saving_customization', array( self::class, 'save_customization_settings' ) );
		add_filter( 'wschat_settings_saving_email', array( self::class, 'save_email_settings' ) );

		Integration::init();
	}

	public static function load_market_page() {
		$data['plugin_name'] = 'wschat';

		Utils::include_file( '/resources/views/admin/settings/market.php', $data );
	}

	public static function load_help_and_support() {
		$data['plugin_name'] = 'wschat';

		Utils::include_file( '/resources/views/admin/settings/help_support.php', $data );
	}

	public static function load_email_settings() {
		$data['wschat_options'] = self::get_widget_settings();

		Utils::include_file( '/resources/views/admin/settings/email.php', $data );
	}

	public static function load_license_settings() {
		$data['plugin_name'] = 'wschat';

		Utils::include_file( '/resources/views/admin/settings/license.php', $data );
	}

	public static function load_general_settings() {
		$data['wschat_options'] = self::get_widget_settings();
		$data['pre_chat_form']  = ( new Settings() )->get_form();

		Utils::include_file( '/resources/views/admin/settings/general.php', $data );
	}

	public static function load_restrictions_settings() {
		$data['wschat_options'] = self::get_widget_settings();

		Utils::include_file( '/resources/views/admin/settings/restrictions.php', $data );
	}

	public static function load_customization_settings() {

		/**
		 * Fire a filter hook for widget fonts
		 *
		 * @since 2.0.0
		 */
		$data['fonts']          = apply_filters( 'wschat_widget_fonts', array( 'sans-serif', 'monospace', 'fantasy', 'Roboto', 'cursive' ) );
		$data['tones']          = ( new self() )->get_tones();
		$data['wschat_options'] = self::get_widget_settings();

		Utils::include_file( '/resources/views/admin/settings/customization.php', $data );
	}

	/**
	 * Get settings model
	 *
	 * @param bool $reload
	 * @return SettingsModel
	 */
	public static function get_widget_settings( $reload = false ) {
		if ( self::$settings && false === $reload ) {
			return self::$settings;
		}

		$settings = SettingsModel::load();

		/**
		 * Fire a filter hook for settings
		 *
		 * @since 2.0.0
		 * @param $settings
		 */
		self::$settings = apply_filters( 'wschat_get_settings', $settings );

		return  self::$settings;
	}

	public function admin_settings() {
		if ( isset( $_REQUEST['wschat_settings_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_REQUEST['wschat_settings_nonce'] ), 'wschat_save_settings' ) ) {
			wp_die();
		}

		if ( isset( $_POST['submit'] ) ) {
			$wschat_options = self::get_widget_settings();

			/**
			 * Fires before the settings has been saved to update the settings object
			 *
			 * @param SettingsModel $wschat_options Settings Model Object
			 *
			 * @since 2.0.0
			 */
			$wschat_options = apply_filters( 'wschat_settings_saving', $wschat_options );

			/**
			 * Fires before the settings has been saved to update the settings object along with the current tab suffix
			 *
			 * @param SettingsModel $wschat_options Settings Model Object
			 *
			 * @since 2.0.0
			 */
			$wschat_options = apply_filters( 'wschat_settings_saving_' . $this->get_current_tab(), $wschat_options );

			$wschat_options->save();

			/**
			 * Fires after the settings has been saved
			 *
			 * @param SettingsModel $wschat_options Settings Model Object
			 *
			 * @since 2.0.0
			 */
			do_action( 'wschat_settings_saved', $wschat_options );

			/**
			 * Fires after the settings has been saved with the current tab
			 *
			 * @since 2.0.0
			 *
			 * @param SettingsModel $wschat_options Settings Model Object
			 */
			do_action( 'wschat_settings_saved_' . $this->get_current_tab(), $wschat_options );
		}

		$data['menus']      = $this->get_settings_menu();
		$data['active_tab'] = $this->get_current_tab();

		Utils::include_file( '/resources/views/admin/settings.php', $data );
	}

	public function get_current_tab() {
		$tab = Utils::get_param( 'tab' );

		return ! empty( $tab ) ? $tab : $this->get_default_tab();
	}

	public function get_default_tab() {

		/**
		 * Fire a filter hook for default settings tab
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wschat_settings_default_tab', 'general' );
	}

	public function get_settings_menu() {
		$menus = array(
			array(
				'title' => __( 'General Settings' ),
				'slug'  => 'general',
			),
			array(
				'title' => __( 'Restrictions' ),
				'slug'  => 'restrictions',
			),
			array(
				'title' => __( 'Customization Settings' ),
				'slug'  => 'customization',
			),
			array(
				'title' => __( 'Email Settings' ),
				'slug'  => 'email',
			),
			array(
				'title' => __( 'Integrations' ),
				'slug'  => 'integrations',
			),
			array(
				'title' => __( 'Agent Settings' ),
				'slug'  => 'agent',
			),
			// array(
			// 	'title' => __( 'Licence' ),
			// 	'slug'  => 'licence',
			// ),
		);

		/**
		 * Fire a filter hook for admin settings menu
		 *
		 * @since 2.0.0
		 * @param $menus
		 */
		$menus = apply_filters( 'wschat_admin_settings_menu', $menus );

		return $menus;
	}

	public function get_tones() {
		$files = glob( dirname( __DIR__ ) . '/resources/tones/*' );

		$files = array_map(
			function ( $file ) {
				return Arr::only(
					pathinfo( $file ),
					array(
						'basename',
						'filename',
					)
				);
			},
			$files
		);

		/**
		 * Fire a filter hook for alert tones
		 *
		 * @since 2.0.0
		 * @param $files
		 */
		return apply_filters( 'wschat_alert_tones', $files );
	}

	public static function save_general_settings( SettingsModel $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );
		$new_settings = array();

		$new_settings['enable_live_chat']       = isset( $_POST['enable_live_chat'] ) ? true : false;
		$new_settings['enable_tags']            = isset( $_POST['enable_tags'] ) ? true : false;
		$new_settings['default_tag_color']      = isset( $_POST['default_tag_color'] ) ? preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['default_tag_color'] ) ) : 'FFFFFF';
		$new_settings['communication_protocol'] = isset( $_POST['communication_protocol'] ) ? sanitize_text_field( $_POST['communication_protocol'] ) : 'http';

		return $settings->merge( $new_settings );
	}

	public static function save_restrictions_settings( SettingsModel $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );
		$new_settings = array();

		$new_settings['inclusion_list']       = ! empty( trim( wp_kses_post( $_POST['inclusion_list'] ) ) ) ? explode( "\n", wp_kses_post( $_POST['inclusion_list'] ) ) : array();
		$new_settings['exclusion_list']       = ! empty( trim( wp_kses_post( $_POST['exclusion_list'] ) ) ) ? explode( "\n", wp_kses_post( $_POST['exclusion_list'] ) ) : array();
		$new_settings['ip_addresses_list']    = ! empty( trim( wp_kses_post( $_POST['ip_addresses_list'] ) ) ) ? explode( "\n", wp_kses_post( $_POST['ip_addresses_list'] ) ) : array();
		$new_settings['email_addresses_list'] = ! empty( trim( wp_kses_post( $_POST['email_addresses_list'] ) ) ) ? explode( "\n", wp_kses_post( $_POST['email_addresses_list'] ) ) : array();
		return $settings->merge( $new_settings );
	}

	public static function save_email_settings( SettingsModel $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );
		$new_settings = array();

		$new_settings['email_notifications']   = isset( $_POST['email_notifications'] ) ? true : false;
		$new_settings['feedback_form']         = isset( $_POST['feedback_form'] ) ? true : false;
		$new_settings['email_transcript_chat'] = isset( $_POST['email_transcript_chat'] ) ? true : false;

		return $settings->merge( $new_settings );
	}

	public static function save_customization_settings( SettingsModel $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings->header_online_text      = isset( $_POST['header_online_text'] ) ? sanitize_text_field( $_POST['header_online_text'] ) : '';
		$settings->header_offline_text     = isset( $_POST['header_offline_text'] ) ? sanitize_text_field( $_POST['header_offline_text'] ) : '';
		$settings->offline_auto_reply_text = isset( $_POST['offline_auto_reply_text'] ) ? sanitize_text_field( $_POST['offline_auto_reply_text'] ) : '';
		$settings->header_text             = isset( $_POST['header_text'] ) ? sanitize_text_field( $_POST['header_text'] ) : '';
		$settings->alert_tone              = isset( $_POST['alert_tone'] ) ? sanitize_text_field( $_POST['alert_tone'] ) : 'bell';
		$settings->alert_tone_url          = Utils::get_resource_url( '/resources/tones/' ) . $settings->alert_tone;
		$settings->font_family             = isset( $_POST['font_family'] ) ? sanitize_text_field( $_POST['font_family'] ) : 'auto';

		$colors = $settings->get( 'colors', array() );

		if ( isset( $_POST['colors']['--wschat-bg-primary'] ) ) {
			$colors['--wschat-bg-primary'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-bg-primary'] ) );
		}

		if ( isset( $_POST['colors']['--wschat-text-primary'] ) ) {
			$colors['--wschat-text-primary'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-text-primary'] ) );
		}

		if ( isset( $_POST['colors']['--wschat-bg-secondary'] ) ) {
			$colors['--wschat-bg-secondary'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-bg-secondary'] ) );
		}

		if ( isset( $_POST['colors']['--wschat-text-secondary'] ) ) {
			$colors['--wschat-text-secondary'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-text-secondary'] ) );
		}

		if ( isset( $_POST['colors']['--wschat-icon-color'] ) ) {
			$colors['--wschat-icon-color'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-icon-color'] ) );
		}

		if ( isset( $_POST['colors']['--wschat-text-gray'] ) ) {
			$colors['--wschat-text-gray'] = preg_replace( '/[^A-Za-z0-9. -]/', '' , sanitize_text_field( $_POST['colors']['--wschat-text-gray'] ) );
		}

		$settings->colors = $colors;

		return $settings;
	}
}
