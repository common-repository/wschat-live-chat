<?php
namespace WSChat\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use WSChat\Utils;

class Settings extends Fluent {

	const SETTINGS_KEY = 'wschat_site_settings';

	const STATUS_ONLINE  = 'online';
	const STATUS_OFFLINE = 'offline';

	public static function get_defaults() {
		return  array(
			'enable_live_chat'        => true,
			'chatgpt_enabled'         => false,
			'chatgpt_api_key'         => null,
			'enable_tags'             => true,
			'agent_setup'             => true,
			'email_transcript_chat'   => true,
			'widget_status'           => self::STATUS_ONLINE,
			'default_tag_color'       => '2489db',
			'header_online_text'      => 'Online',
			'header_offline_text'     => 'Offline',
			'offline_auto_reply_text' => 'We are offline now, Will get back to you soon',
			'header_text'             => __( 'Chat with us!', 'wschat' ),
			'font_family'             => '',
			'alert_tone'              => 'messenger',
			'alert_tone_url'          => Utils::get_resource_url( '/resources/tones/messenger.wav' ),
			'colors'                  => array(
				'--wschat-bg-primary'     => '2489DB',
				'--wschat-bg-secondary'   => 'f5f5f5',
				'--wschat-text-primary'   => 'ffffff',
				'--wschat-text-secondary' => '5E5E5E',
				'--wschat-icon-color'     => 'ffffff',
				'--wschat-text-gray'      => '808080',
			),
			'communication_protocol'  => 'pusher',
			'email_notifications'     => true,
			'feedback_form'           => true,
			'email_transcript_chat'   => true,
			'pusher'                  => array(
				'app_key'    => '',
				'secret_key' => '',
				'app_id'     => '',
				'cluster'    => '',
			),
		);
	}

	public function enabled() {
		return $this->get( 'enable_live_chat', false );
	}

	public function tags_enabled() {
		return $this->get( 'enable_tags', false );
	}

	public function agents_enabled() {
		return $this->get( 'agent_setup', false );
	}

	public function is_online() {
		return $this->enabled() && $this->get( 'widget_status', self::STATUS_ONLINE );
	}

	public static function load() {
		return new self( get_option( self::SETTINGS_KEY, self::get_defaults() ) );
	}

	public function save() {
		update_option( self::SETTINGS_KEY, parent::toArray() );

		return $this;
	}

	public function hide_sensitive( $hide_sensitive = true ) {
		$this->hide_sensitive = $hide_sensitive;

		return $this;
	}

	public function toArray() {
		$data = parent::toArray();

		if ( $this->get( 'hide_sensitive', false ) === false ) {
			return $data;
		}

		$data = Arr::except(
			$data,
			array(
				'pusher.secret_key',
				'pusher.app_id',
			)
		);

		return $data;
	}

	public function merge( $new_options ) {
		$this->attributes = array_merge( $this->attributes, $new_options );

		return $this;
	}

	public function masked( $string, $no_of_chars_shown = 3 ) {
		if ( strlen( $string ) <= $no_of_chars_shown ) {
			return $string;
		}

		$masked = substr( $string, 0, $no_of_chars_shown );

		$masked .= str_repeat( 'x', strlen( $string ) - $no_of_chars_shown );

		return $masked;
	}
}
