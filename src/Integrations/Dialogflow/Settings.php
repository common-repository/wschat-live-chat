<?php
namespace WSChat\Integrations\Dialogflow;

use Illuminate\Support\Fluent;
/** 
	 * Apply_filter function
 *
	 * @since 2.0.0
	 */
use function apply_filters;

/**
 * Dialogflow settings class
 *
 * Store data in options table and provide fluent API to access those data
 */
class Settings extends Fluent {

	const SETTINGS_OPTION_KEY = 'wschat_dialogflow_settings';

	public static function load() {
		$self = new self();
		return new self( get_option( self::SETTINGS_OPTION_KEY, $self->get_defaults() ) );
	}

	public function get_defaults() {
		$defaults = array(
			'enabled'       => false,
			'project_id'    => '',
			'credentials'   => '',
			'language_code' => 'en-US',
			'agent_id'      => 1,
		);

		/** 
		 * Fire a filter hook for default settings of dialog flow
		 *
		 * @since 2.0.0
		 * @param $defaults
		 */
		return apply_filters( 'wschat_dialog_flow_settings_data_defaults', $defaults );
	}

	public function get( $key, $default = null ) {

		/** 
		 * Fire a filter hook for dialog flow settings
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wschat_dialog_flow_settings_' . $key, parent::get( $key, $default ) );
	}

	public function get_project_id( $default = null ) {
		return $this->get( 'project_id', $default );
	}

	public function get_credentials( $default = null ) {
		return $this->get( 'credentials', $default );
	}

	public function get_language_code( $default = null ) {
		return $this->get( 'language_code', $default );
	}

	public function get_agent_id() {
		return $this->get( 'agent_id', false );
	}

	public function enabled() {
		return $this->get( 'enabled', false );
	}

	public function save() {
		update_option( self::SETTINGS_OPTION_KEY, $this->toArray() );
	}

	public static function get_language_codes() {
		$language_codes = array(
			'en-US' => 'English - US',
			'en-IN' => 'English - India',
			'en-CA' => 'English - Canada',
			'en'    => 'English',
			'fr'    => 'French',
			'fr-CA' => 'French - Canada',
			'fr-Fr' => 'French - France',
			'ge'    => 'German',
		);

		/** 
		 * Fire a filter hook for dialog flow language codes
		 *
		 * @since 2.0.0
		 * @param $language_codes
		 */
		return apply_filters( 'wschat_dialogflow_language_codes', $language_codes );
	}
}
