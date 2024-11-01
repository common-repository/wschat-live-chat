<?php
namespace WSChat\WfApiManager;

class Config {

	public static function init() {

		$product_name        = 'wschat'; // name should match with 'Software Title' configured in server, and it should not contains white space
		$product_version     = '3.0.2';
		$product_slug        = 'wschat-premium/wschat-premium.php'; //product base_path/file_name
		$serve_url           = 'https://elextensions.com/';
		$plugin_settings_url = admin_url( 'admin.php?page=wschat_settings' );

		$script_name = basename( isset( $_SERVER['PHP_SELF'] ) ? sanitize_text_field( $_SERVER['PHP_SELF'] ) : '' );
		if ( in_array( $script_name, array( 'plugins.php', 'update-core.php' ) ) ) {
			$current       = get_site_transient( 'update_core' );
			$timeout       = 1 * HOUR_IN_SECONDS;
			$need_to_check = isset( $current->last_checked ) && $timeout < ( time() - $current->last_checked );
			if ( $need_to_check ) {
				wp_clean_update_cache();
			}
		}

		new WfApiManager( $product_name, $product_version, $product_slug, $serve_url, $plugin_settings_url );
	}
}
