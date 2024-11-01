<?php

namespace WSChat;

use WSChat\Integrations\Dialogflow\Dialogflow;
use WSChat\Models\Message;
use WSChat\Models\Tag;
use WSChat\PreChatForm\Settings as PreChatFormSettings;
use WSChat\WfApiManager\Config;
use WSChat\WooCommerce\WooCommerce;
	use WSChat\HelpAndSupport\HelpAndSupportController;

class WSChat {
	const VERSION  = '2.0.0';
	const INSTANCE = 'PREMIUM';

	public $plugin_basename;

	public function with_basename( $basename ) {
		$this->plugin_basename = $basename;

		return $this;
	}

	public function boot() {
		$this->register_hooks();

		$this->include_review_component();
	}

	public function include_review_component() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once  ABSPATH . 'wp-admin/includes/plugin.php' ;
		}
		include_once dirname( __DIR__ ) . '/resources/review_and_troubleshoot_notify/review-and-troubleshoot-notify-class.php';
		$data                      = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->plugin_basename );
		$data['name']              = $data['Name'];
		$data['basename']          = $this->plugin_basename;
		$data['rating_url']        = 'https://elextensions.com/plugin/wschat-wordpress-live-chat-plugin-free/#reviews';
		$data['documentation_url'] = 'https://elextensions.com/knowledge-base/set-up-wschat-wordpress-live-chat-plugin/';
		$data['support_url']       = 'https://support.elextensions.com/';

		new \Elex_Review_Components( $data );
	}

	public function register_hooks() {

		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'action_link' ) );
		add_action( 'admin_notices', array( $this, 'wschat_admin_pusher_setup_notice' ) );

		add_action( 'init', array( $this, 'load_language' ) );
		add_action( 'init', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'add_menu' ) );
		add_action( 'init', array( $this, 'register_routes' ) );


		add_action( 'admin_init', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'wp_footer', array( $this, 'load_widget' ) );
	}

	public function wschat_admin_pusher_setup_notice() {
		
		/* Check transient, if available display notice */
		$status = true;
		$pusher = WSPusher::is_pusher_enabled();
		if ( $pusher ) {
			$can_connect = WSPusher::can_connect_to_pusher();
			if ( ! $can_connect ) {
			   $status = false;
			}
		}
		if ( false === $status ) {
			
			echo wp_kses_post(
				'<div class="notice notice-error is-dismissible" style="background-color:#FFE4E5;border-color:#ED1F24">
				<p><b>You need to <a href="' . wp_nonce_url( admin_url( 'admin.php?page=wschat_settings' ) ) . '">Setup Pusher</a> to start using WSchat.  <a target="_blank" href="https://pusher.com/">Create a free Pusher Account</a></b></p>
			</div>'
			);
		}
	}

	public function translate_wschat_admin_notice( $translated_text, $untranslated_text ) {
		$old        = array(
			'Plugin <strong>activated</strong>.',
			'Selected plugins <strong>activated</strong>.',
		);
		$error_text = 'BASIC Version of this Plugin Installed. Please uninstall the BASIC Version before activating PREMIUM.';
		$new        = "<span style='color:red'>" . $error_text . '</span>';

		if ( in_array( $untranslated_text, $old, true ) ) {
			$translated_text = $new;
		}

		return $translated_text;
	}


	public function register_routes() {
		WSUser::init();
		WSConversation::init();
		WSMessage::init();
		WSSettings::init();
		WSPusher::init();
		PreChatFormSettings::init();
		WooCommerce::init();
		Dialogflow::init();
		WSAgent::init();
		WSTag::init();
		Config::init();
		HelpAndSupportController::init();
	}


	public function load_widget() {

		$settings = WSSettings::get_widget_settings();

		if ( false === (bool) $settings['enable_live_chat'] ) {
			return;
		}

		$enable_widget = $this->restrictions();
		if ( false === $enable_widget ) {
			return false;
		}

		wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/user-chat.js' ), array( 'jquery', 'wp-element' ), self::VERSION, true );
		$this->localize_script( 'wschat' );

		include_once dirname( __DIR__ ) . '/resources/views/user/live-chat-v2.php';
	}

	public function restrictions() {

		$settings = WSSettings::get_widget_settings();

		$current_widget_url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' ;

		//Array of current widget side url
		$current_url['path'] = parse_url( $current_widget_url, PHP_URL_PATH );
		parse_str( parse_url( $current_widget_url, PHP_URL_QUERY ) ?? '', $current_url['query_string'] );

		if ( ! empty( $settings['exclusion_list'] ) ) {
			foreach ( $settings['exclusion_list'] as $elist ) {
				$exclusion_path = parse_url( trim( $elist ), PHP_URL_PATH );
				parse_str( parse_url( $elist, PHP_URL_QUERY ) ?? '', $exclusion_query_string );
				if ( $current_url['path'] === $exclusion_path ) {
					$match = array_intersect( $current_url['query_string'], $exclusion_query_string );

					if ( count( $match ) === count( $exclusion_query_string ) ) {
						return false;
					}
				}
			}
		}

		if ( ! empty( $settings['inclusion_list'] ) ) {
			$is_match = false;
			foreach ( $settings['inclusion_list'] as $ilist ) {
				$inclusion_path = parse_url( trim( $ilist ), PHP_URL_PATH );
				parse_str( parse_url( $ilist, PHP_URL_QUERY ), $inclusion_query_string );
				if ( $current_url['path'] === $inclusion_path ) {
					$match = array_intersect( $current_url['query_string'], $inclusion_query_string );
					if ( count( $match ) === count( $inclusion_query_string ) ) {
						$is_match = true;
					}
				}
			}
			if ( false === $is_match ) {
				return false;
			}
		}

		if ( ! empty( $settings['ip_addresses_list'] ) ) {
			$current_ip_address = self::get_client_ip_address();
			$is_match_ip        = false;
			foreach ( $settings['ip_addresses_list'] as $ilist ) {
				$ip_addresses = trim( $ilist );
				if ( $current_ip_address === $ip_addresses ) {
						$is_match_ip = true;
				}
			}
			if ( true === $is_match_ip ) {
				return false;
			}
		}

		if ( ! empty( $settings['email_addresses_list'] ) ) {
			$current_user_email_address = '';
			$current_user               = wp_get_current_user();
			if ( $current_user ) {
				$current_user_email_address = $current_user->user_email;
			}
			$is_match_email = false;
			if ( $current_user_email_address ) {
				foreach ( $settings['email_addresses_list'] as $ilist ) {
					$email_addresses = trim( $ilist );
					if ( $current_user_email_address === $email_addresses ) {
						$is_match_email = true;
					}
				}
			}
			if ( true === $is_match_email ) {
				return false;
			}
		}

		return true;
	}

	public function get_client_ip_address() {


		//whether ip is from share internet
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		return sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
		} 
		
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { //from proxy
		return sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
		}  //from remote address
		
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
		return $ip;

	}

	public function migrate() {
		set_transient( 'wschat-admin-pusher-notice-status', true, 5 );
		Migrate::run();
	}

	public function enqueue_admin_scripts() {
		global $plugin_page;
		wp_enqueue_style( 'select2', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/select2.css' ), array(), self::VERSION );
		wp_enqueue_script( 'select2-js', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/select2.js' ), array( 'jquery' ), self::VERSION , true );

		wp_enqueue_script( 'wschat-swal-esm', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/swal/sweetalert2.min.js' ), array( 'jquery' ), self::VERSION , true );
		wp_enqueue_style( 'wschat-swal-sweetalert', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/swal/sweetalert2.min.css' ), array(), self::VERSION );

		if ( in_array( $plugin_page, array( 'wschat_settings' ), true ) ) {
			wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/jscolor.js' ), array( 'jquery' ), self::VERSION , true );
			wp_enqueue_script( 'wschat-fields', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/plugins/prechat-form/field-settings.min.js' ), array( 'jquery', 'wp-element' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_chat' ), true ) ) {
			wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/admin-chat.js' ), array( 'jquery', 'underscore', 'wp-element' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_history' ), true ) ) {
			wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/admin-history.min.js' ), array( 'jquery', 'underscore', 'wp-element' ), self::VERSION , true );
			wp_enqueue_script( 'wschat-delete-history', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/chat-histories.js' ), array( 'wschat-swal-esm', 'jquery', 'underscore' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_live_visitors' ), true ) ) {
			wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/live-visitors.js' ), array( 'jquery', 'underscore' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_agents' ), true ) ) {
			wp_enqueue_script( 'wschat-agents', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/admin-agents.js' ), array( 'jquery', 'wschat-swal-esm' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_tags' ), true ) ) {
			wp_enqueue_script( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/jscolor.js' ), array( 'jquery' ), self::VERSION , true );
			wp_enqueue_script( 'wschat_tags', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/admin-tags.min.js' ), array( 'jquery', 'underscore' ), self::VERSION , true );
		}

		if ( in_array( $plugin_page, array( 'wschat_helpandsupport' ), true ) ) {
			wp_enqueue_script( 'bootstrap-js', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/bootstrap.js' ), array( 'jquery' ), self::VERSION , true );
			wp_enqueue_script( 'popper-js', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/popper.js' ), array( 'jquery' ), self::VERSION , true );
		}

		if ( Utils::isAgent() && 'wschat_chat' !== $plugin_page ) {
			wp_enqueue_script( 'wschat_gloabl_alert', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/global_alert.min.js' ), array( 'jquery', 'underscore' ), self::VERSION , true );
			$this->localize_script( 'wschat_gloabl_alert' );
		}

		wp_enqueue_script( 'wschat-pusher-setting-update', plugins_url( dirname( $this->plugin_basename ) . '/resources/js/pusher_setting_update.js' ), '', self::VERSION , true );

		$this->localize_script( 'wschat' );
	}

	public function localize_script( $handle ) {
		$settings = WSSettings::get_widget_settings();
		$data     = array(
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'wschat-ajax-nonce' ),
			'settings'    => $settings->hide_sensitive(),
			'enable_tags' => false,
			'user_id'     => get_current_user_id(),
		);

		if ( $settings->get( 'enable_tags' ) ) {
			$data['tags']        = ( new Tag() )->applyFilters()->get();
			$data['enable_tags'] = true;
		}

		if ( Utils::isAgent() ) {
			$data['unread_count'] = Message::filter_unread_notifications(
				array(
					'agent_id' => array( $data['user_id'] ),
				)
			)->count();

			$data['capabilities'] = wp_get_current_user()->get_role_caps();
		}

		if ( 'wschat' === $handle ) {
			$data['allowed_mime_types'] = array_values( wp_get_mime_types() );
			$data['max_upload_size']    = wp_max_upload_size();
		}

		wp_localize_script(
			$handle,
			'wschat_ajax_obj',
			$data
		);
	}

	public function enqueue_scripts() {
		// Disable WordPress default emoji. It takes too much of time to load all the SVGs
		remove_action( 'wp_head', 'print_emoji_detection_script', 0 );

		wp_enqueue_style( 'wschat', plugins_url( dirname( $this->plugin_basename ) . '/resources/dist/base.css' ), array(), self::VERSION );
	}

	public function load_language() {
		load_plugin_textdomain( 'wschat', false, __DIR__ . '/../lang' );
	}

	// Adding menu and Links
	// Can be moved to seperate class
	public function add_menu() {
		if ( Utils::isAgent() === false ) {
			return;
		}

		// TODO: Admin bar menu item with real count
		//add_action('admin_bar_menu', array($this, 'add_admin_bar_links'), 900);

		add_action( 'admin_menu', array( $this, 'add_admin_main_menu' ) );
	}

	public function add_admin_bar_links( $wp_admin_bar ) {
		$live_visitors = 0;

		$admin_bar_args = array(
			'id'    => 'wschat_visitors',
			'href'  => wp_nonce_url( admin_url( 'admin.php?page=wschat_chat' ) ),
			'title' => 'WSChat <span class="wschat_menu_badge">' . $live_visitors . '</span>',
		);

		$wp_admin_bar->add_node( $admin_bar_args );

		$admin_bar_args = array(
			'id'     => 'live_chat',
			'parent' => 'site-name',
			'href'   => wp_nonce_url( admin_url( 'admin.php?page=wschat_chat' ) ),
			'title'  => __( 'Live Chat', 'wschat' ),
		);

		$wp_admin_bar->add_node( $admin_bar_args );
	}

	public function add_admin_main_menu() {
		$parent_slug = 'wschat_chat';

		$cap = 'read';

		add_menu_page(
			__( 'Live Chat', 'wschat' ),
			__( 'WSChat', 'wschat' ),
			$cap,
			$parent_slug,
			array( $this, 'admin_live_chat' ),
			'dashicons-format-chat',
			25
		);

		add_submenu_page(
			$parent_slug,
			__( 'Live Chat', 'wschat' ),
			__( 'Live Chat', 'wschat' ),
			$cap,
			'wschat_chat',
			array( $this, 'admin_live_chat' )
		);

		if ( WSSettings::get_widget_settings()->tags_enabled() ) {
			add_submenu_page(
				$parent_slug,
				__( 'Tags', 'wschat' ),
				__( 'Tags', 'wschat' ),
				'administrator',
				'wschat_tags',
				array( WSTag::class, 'load_tags_page' )
			);
		}

		add_submenu_page(
			$parent_slug,
			__( 'Live Visitors', 'wschat' ),
			"Live Visitors <sup class='elex_wschat_go_premium_color'>[Premium]</sup>",
			$cap,
			'wschat_live_visitors',
			array( $this, 'admin_live_visitors' )
		);


		if ( WSSettings::get_widget_settings()->agents_enabled() ) {
			add_submenu_page(
				$parent_slug,
				__( 'Agents', 'wschat' ),
				__( 'Agents', 'wschat' ),
				'administrator',
				'wschat_agents',
				array( WSAgent::class, 'load_agent_page' )
			);
		}


		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'wschat' ),
			__( 'Settings', 'wschat' ),
			'administrator',
			'wschat_settings',
			array( new WSSettings(), 'admin_settings' )
		);

		add_submenu_page(
			$parent_slug,
			__( 'Help & Support', 'wschat' ),
			__( 'Help & Support', 'wschat' ),
			'administrator',
			'wschat_helpandsupport',
			array( HelpAndSupportController::class, 'load_view' )
		);

		add_submenu_page(
			$parent_slug,
			__( 'Go Premium', 'wschat' ),
			__( "<span class='elex_wschat_go_premium_color'>Go Premium!</span>" ),
			$cap,
			'wschat_gopremium',
			array( WSSettings::class, 'load_market_page' )
		);

		if ( false === Utils::needs_activation() ) {
			add_submenu_page( $parent_slug, __( 'Upgrade to Premium', 'wschat' ), __( 'Upgrade to Premium', 'wschat' ), 'administrator', 'wschat_Upgrade_to_Premium', array( $this, 'Upgrade_to_premium_tab' ) );
		}
	}

	public function action_link( $links ) {
		$plugin_links = array(
			'<a href="' . wp_nonce_url( admin_url( 'admin.php?page=wschat_settings' ) ) . '">' . __( 'Settings', 'wschat' ) . '</a>',
			'<a href="https://elextensions.com/knowledge-base/set-up-wschat-wordpress-live-chat-plugin/" target="_blank">' . __( 'Documentation', 'wschat' ) . '</a>',
			'<a href="https://elextensions.com/support/" target="_blank">' . __( 'Support', 'wschat' ) . '</a>',
		);

		if ( array_key_exists( 'deactivate', $links ) ) {
			$links['deactivate'] = str_replace( '<a', '<a class="wschat-deactivate-link"', $links['deactivate'] );
		}

		return array_merge( $plugin_links, $links );
	}

	public function admin_live_chat() {
		include_once dirname( __DIR__ ) . '/resources/views/admin/live-chat-v2.php';
	}

	public function admin_live_visitors() {
		$visitors = WSConversation::get_live_visitors();
		include_once dirname( __DIR__ ) . '/resources/views/admin/live-visitors.php';
	}

	public function Upgrade_to_premium_tab() {
		include_once dirname( __DIR__ ) . '/resources/views/admin/Upgrade-to-premium.php' ;
	}
}


