<?php

namespace WSChat;

use Exception;
use Illuminate\Support\Arr;
use WSChat\Models\User;

class Utils {
	public static function abort( $data = array(), $code = 404 ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( $data, $code );
		} else {
			throw new Exception( Arr::get( $data, 'message', '' ), $code );
		}
		die;
	}

	public static function abort_if( $boolean, $data, $code ) {
		if ( ! $boolean ) {
			return false;
		}

		return self::abort( $data, $code );
	}

	public static function abort_unless( $boolean, $data = array(), $code = 403 ) {
		return self::abort_if( false === $boolean, $data, $code );
	}

	public static function isAgent( $user = false, $caps = false ) {
		$user = false === $user ? wp_get_current_user() : $user;

		$caps = false === $caps ? WSAgent::get_capabilities() : $caps;

		if ( $user instanceof \WP_User ) {
			if ( in_array( User::ROLE_ADMIN, $user->roles, true ) ) {
				return true;
			}

			$wschat_caps = array_intersect( array_keys( $user->allcaps ), $caps );
			if ( count( $wschat_caps ) ) {
				return true;
			}
		}

		return false;
	}

	public static function abort_unless_agent() {
		self::abort_unless(
			self::isAgent( wp_get_current_user() ),
			array(
				'message' => __( 'Unauthorized', 'wschat' ),
			),
			403
		);
	}

	public static function get_url( $page ) {
		return admin_url( 'admin.php?page=' . $page );
	}

	public static function get_param( $param, $default = null ) {
		$value = sanitize_text_field( filter_input( INPUT_GET, $param ) );

		return $value ? $value : $default;
	}

	public static function is_widget_online() {
		$settings = WSSettings::get_widget_settings();

		return $settings->is_online();
	}

	public static function get_resource_url( $path = '' ) {
		return plugins_url(
			dirname( plugin_basename( __DIR__ ) )
		) . $path;
	}

	public static function verify_nonce( $action, $param ) {
		$data = array(
			'message' => __( 'Invalid request' ),
		);

		if ( ! isset( $_REQUEST[ $param ] ) ) {
			self::abort( $data, 403 );
		}

		if ( isset( $_REQUEST[ $param ] ) && ! wp_verify_nonce( sanitize_text_field( $_REQUEST[ $param ] ), $action ) ) {
			self::abort( $data, 403 );
		}
	}

	public static function include_file( $path, $args = array() ) {
		extract( $args );
		$file = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __DIR__ ) ) . $path;

		include_once $file;
	}

	public static function woocommerce_installed() {
		return in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins', array() ), true );
	}

	public static function needs_activation() {
		return WSChat::INSTANCE === 'PREMIUM';
	}

	public static function is_activated() {
		return 'activated' === get_option( 'wschat_activation_status' );
	}

	public static function get_client_ip() {
		$remotes_keys = [
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
			'HTTP_X_CLUSTER_CLIENT_IP',
		];

		foreach ( $remotes_keys as $key ) {
			$address = getenv( $key );
			if ( $address ) {
				foreach ( explode( ',', $address ) as $ip ) {
					if ( self::is_valid_ip( $ip ) ) {
						return $ip;
					}
				}
			}
		}

		return '127.0.0.0';
	}

	public static function is_valid_ip( $ip ) {
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE )
			&& ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE )
		) {
			return false;
		}

		return true;
	}

	public static function get_geo_location( $ip ) {
		$url      = 'http://ip-api.com/json/' . $ip;
		$response = wp_remote_get(
			$url,
			array(
				'body' => array(
					'fields' => 'country,countryCode,region,regionName,city,zip,timezone',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$rate_limit = wp_remote_retrieve_header( $response, 'x-rl' );

		if ( 0 === intval( $rate_limit ) ) {
			/**
			 * Fire an action hook for geo ip rate limit exceed
			 *
			 * @since 2.0.0
			 */
			do_action( 'wschat_geo_ip_rate_limit_exceed', wp_remote_retrieve_header( $response, 'x-ttl' ) );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}

