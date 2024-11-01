<?php
/**
 * Plugin Name:     ELEX WordPress Live Chat (WSChat)
 * Plugin URI:      https://elextensions.com/plugin/wschat-wordpress-live-chat-plugin/
 * Description:     Let's you connect to your customers in real-time.
 * Version:         3.1.3
 * Requires PHP:    7.1.3
 * WC requires at least: 3.0.0
 * WC tested up to: 9.2
 * Author:          ELEXtensions
 * Author URI:      https://elextensions.com/
 * Text Domain:     wschat-live-chat
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once  ABSPATH . 'wp-admin/includes/plugin.php' ;
}
$wschat_plugins        = array(
	'wschat-live-chat/wschat.php'       => "Basic Version of WSChat Plugin is installed. Please deactivate the Basic Version of WSChat before activating PREMIUM version.<br>Don't worry! Your conversation and messages data will be retained.<br>Go back to <a href='" . esc_html( admin_url( 'plugins.php' ) ) . "'>plugins page</a>",
	'wschat/wschat.php'                 => "Basic Version of WSChat Plugin is installed. Please deactivate the Basic Version of WSChat before activating PREMIUM version.<br>Don't worry! Your conversation and messages data will be retained.<br>Go back to <a href='" . esc_html( admin_url( 'plugins.php' ) ) . "'>plugins page</a>",
	'wschat-premium/wschat-premium.php' => "Premium Version of WSChat Plugin is installed. Please deactivate the Premium Version of WSChat before activating Basic version.<br>Don't worry! Your conversation and messages data will be retained.<br>Go back to <a href='" . esc_html( admin_url( 'plugins.php' ) ) . "'>plugins page</a>",
);
$current_wschat_plugin = plugin_basename( __FILE__ );
foreach ( $wschat_plugins as $wschat_plugin => $error_msg ) {
	if ( $current_wschat_plugin === $wschat_plugin ) {
		continue;
	}

	if ( is_plugin_active( $wschat_plugin ) ) {
		deactivate_plugins( $current_wschat_plugin );
		wp_die( wp_kses_post( $error_msg ) );
	}
}

require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'ELEX_WSCHAT_CRM_MAIN_URL' ) ) {
	define( 'ELEX_WSCHAT_CRM_MAIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ELEX_WSCHAT_CRM_MAIN_IMG' ) ) {
	define( 'ELEX_WSCHAT_CRM_MAIN_IMG', ELEX_WSCHAT_CRM_MAIN_URL . 'resources/img/' );
}

define( 'WSCHAT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WSCHAT_VIEW_PATH', WSCHAT_PLUGIN_PATH . 'resources/views/' );
define( 'WSCHAT_SRC_PATH', WSCHAT_PLUGIN_PATH . 'src/' );


$wschat = new \WSChat\WSChat();

$wschat->with_basename( plugin_basename( __FILE__ ) );

$wschat->boot();

register_activation_hook( __FILE__, array( $wschat, 'migrate' ) );

add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} 
);
