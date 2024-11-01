<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WP_User;
use WP_User_Query;
use WSChat\Models\Settings;

class WSAgent {

	public static function init() {
		add_action( 'wp_ajax_wschat_add_new_role', array( self::class, 'add_new_role' ) );
		add_action( 'wp_ajax_wschat_edit_role', array( self::class, 'edit_role' ) );
		add_action( 'wp_ajax_wschat_delete_role', array( self::class, 'delete_role' ) );

		add_action( 'wp_ajax_wschat_add_new_agent', array( self::class, 'create_an_agent' ) );
		add_action( 'wp_ajax_wschat_edit_agent', array( self::class, 'edit_an_agent' ) );
		add_action( 'wp_ajax_wschat_delete_agent', array( self::class, 'delete_agent' ) );

		add_action( 'wp_ajax_wschat_edit_existing_agent', array( self::class, 'edit_existing_agent' ) );
		add_action( 'wp_ajax_elex-wschat-search-existing-users', array( self::class, 'search_existing_users' ) );

		add_action( 'wschat_settings_tab_agent', array( self::class, 'load_agent_settings' ) );
		add_filter( 'wschat_settings_saving_agent', array( self::class, 'save_agent_settings' ) );

		add_action( 'wp_ajax_wschat_set_agent_status', array( self::class, 'set_agent_status' ) );

		add_action( 'wp_logout', array( self::class, 'on_agent_logout' ) );
		add_action( 'wp_login', array( self::class, 'on_agent_login' ), 10, 2 );
	}

	public static function delete_agent() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'delete-an-agent' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request' ),
				)
			);
		}

		$agent_id = sanitize_text_field( $_POST['id'] );
		wp_delete_user( $agent_id );

		wp_send_json_success(
			array(
				'message' => __( 'Agent was deleted' ),
			)
		);

		exit;
	}

	public static function edit_existing_agent() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'add-an-agent' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}
		$user_id = isset( $_POST['existingUsers'] ) ? sanitize_text_field( $_POST['existingUsers'] ) : '';
		$status = get_user_meta(
			$user_id,
			'wschat_online_status',
			true
		);
		if ( empty( $status ) ) {
			update_user_meta( $user_id, 'wschat_online_status', 'offline' );
		} else {
			update_user_meta( $user_id, 'wschat_online_status', $status );
		}

		$user    = new WP_User( $user_id );
		if ( $user->exists() ) {
			// Get the current role of the user
			$current_role       = get_user_meta( $user_id, 'wp_capabilities', true );
			$current_user_roles = array_keys( $current_role );
			$new_role           = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';
			if ( ! in_array( $new_role, $current_user_roles ) ) {
				$user->add_role( $new_role );
				wp_send_json_success(
					array(
						'message' => __( 'New Roles Added successfully', 'wschat' ),
					)
				);
			} else {
				wp_send_json_error(
					array(
						'message' => __( $user->display_name . ' already has this role added', 'wschat' ),
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'User not found.', 'wschat' ),
				)
			);

		}
	}


	public static function delete_role() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}

		if ( ! isset( $_POST['role'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request' ),
				)
			);
		}

		remove_role( sanitize_text_field( $_POST['role'] ) );

		wp_send_json_success(
			array(
				'message' => __( 'Role was deleted' ),
			)
		);

		exit;
	}

	public static function get_agents( $args = array() ) {
		$args['role__in'] = array_keys( self::get_roles() );

		$query = new WP_User_Query( $args );

		return $query->get_results();
	}

	public static function load_agent_page() {
		$data['roles'] = self::get_roles();
		
		$data['limit']   = (int) Utils::get_param( 'limit', 10 );
		$data['page_no'] = (int) Utils::get_param( 'page_no', 1 );

		$query = new WP_User_Query(
			array(
				'role__in' => array_keys( $data['roles'] ),
				'number'   => $data['limit'],
				'paged'    => $data['page_no'],
			)
		);

		$data['wschat_capabilities'] = self::get_capabilities();
		$data['agents']              = (array) $query->get_results();
		$data['total']               = $query->get_total();
		$data['total_pages']         = (int) ceil( $data['total'] / $data['limit'] );

		Utils::include_file( '/resources/views/admin/agents.php', $data );
	}

	public static function create_an_agent() {
		check_ajax_referer( 'add-an-agent' );
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'add-an-agent' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}

		$username = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$role     = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';

		if ( empty( $username ) || empty( $email ) && empty( $role ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Fields seems to be empty', 'wschat' ),
				)
			);
		}

		$password = wp_generate_password();

		if ( username_exists( $username ) || email_exists( $email ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Username or Email already taken', 'wschat' ),
				)
			);
		}

		$user_id = wp_create_user( $username, $password, $email );
		update_user_meta( $user_id, 'wschat_online_status', 'offline' );

		$user = new WP_User( $user_id );

		$user->set_role( $role );
		
		if ( $user_id ) {
			wp_new_user_notification( $user_id, null, 'both' );
		}

		wp_send_json_success(
			array(
				'message' => __( 'New Agent Added successfully', 'wschat' ),
			)
		);
	}

	public static function edit_an_agent() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'edit-an-agent' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}

		$user_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$role    = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';

		Utils::abort_if(
			empty( $email ),
			array(
				'message' => __( 'Invalid data', 'wschat' ),
			),
			422
		);

		$existing_user = get_user_by( 'email', $email );

		Utils::abort_if(
			$existing_user && $existing_user->ID !== (int) $user_id,
			array(
				'message' => __( 'Email id exists', 'wschat' ),
			),
			422
		);

		$user             = new WP_User( $user_id );
		$user->user_email = $email;

		wp_update_user( $user );

		$user->set_role( $role );

		$capabilities = self::get_capabilities();

		foreach ( $capabilities as $cap ) {
			if ( ! isset( $_POST['wschat_role_capability'][ $cap ] ) ) {
				$user->remove_cap( $cap );
			} else {
				$user->add_cap( $cap );
			}
		}

		wp_send_json_success(
			array(
				'message' => __( 'Agent was updated', 'wschat' ),
			)
		);
	}

	public static function load_agent_settings() {
		$data['wschat_options']      = WSSettings::get_widget_settings();
		$data['wschat_capabilities'] = self::get_capabilities();
		$data['wschat_roles']        = self::get_roles();

		Utils::include_file( '/resources/views/admin/settings/agent.php', $data );
	}

	public static function save_agent_settings( Settings $settings ) {
		check_admin_referer( 'wschat_save_settings', 'wschat_settings_nonce' );

		$settings->agent_setup = isset( $_POST['agent_setup'] ) ? true : false;

		return $settings;
	}

	public static function get_capabilities() {
		$caps = array(
			'wschat_init_chat',
			'wschat_delete_chat',
			'wschat_invite_agent',
			'wschat_can_see_unassigned_chats',
		);

		/**
		 * Fire a filter hook for agent capabilities
		 *
		 * @param $caps
		 * @since 2.0.0
		 */
		return apply_filters( 'wschat_agent_capabilities', $caps );
	}

	public static function get_roles() {
		$caps = self::get_capabilities();

		$roles = array_filter(
			wp_roles()->roles,
			function ( $role ) use ( $caps ) {
				$role_caps = array_keys( $role['capabilities'] );

				if ( 'Administrator' === $role['name'] ) {
					return true;
				}

				return count( array_intersect( $caps, $role_caps ) ) > 0;
			}
		);

		$roles = array_map(
			function ( $role ) use ( $caps ) {
				$role['capabilities'] = Arr::only( $role['capabilities'], $caps );

				if ( 'Administrator' === $role['name'] ) {
					$role['capabilities'] = array();
					foreach ( $caps as $cap ) {
						$role['capabilities'][ $cap ] = true;
					}
				}

				return $role;
			},
			$roles
		);

		/**
		 * Fire a filter hook for agent roles
		 *
		 * @param $roles
		 * @since 2.0.0
		 */
		return apply_filters( 'wschat_agent_roles', $roles );
	}

	public static function add_new_role() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}

		Utils::abort_unless( Utils::isAgent() );

		if ( ! isset( $_POST['name'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Please enter a role name', 'wschat' ),
				),
				422
			);
		}

		if ( ! isset( $_POST['wschat_role_capability'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Please select atleast one of the capabilities', 'wschat' ),
				),
				422
			);
		}

		$role_name = sanitize_text_field( $_POST['name'] );

		$capabilities = array(
			'read'                 => true, // This is needed to show admin bar
			'view_admin_dashboard' => true, // This is needed to show admin bar
		);

		$wschat_caps = self::get_capabilities();

		foreach ( $wschat_caps as $cap ) {
			if ( isset( $_POST['wschat_role_capability'][ $cap ] ) ) {
				$capabilities[ $cap ] = true;
			}
		}

		/**
		 * Fire a filter hook for new role capabilities
		 *
		 * @param $capabilities
		 * @since 2.0.0
		 */
		$capabilities = apply_filters( 'wschat_new_role_capabilities', $capabilities );

		$role = add_role( \Illuminate\Support\Str::slug( $role_name ), $role_name, $capabilities );

		Utils::abort_if(
			is_null( $role ),
			array(
				'message' => __( 'Role name exists', 'wschat' ),
			),
			422
		);

		wp_send_json_success(
			array(
				'message' => __( 'Role has been added successfully', 'wschat' ),
				'role'    => $role,
			)
		);
	}
	public static function edit_role() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'wschat' ),
				)
			);
		}


		Utils::abort_unless( Utils::isAgent() );

		if ( ! isset( $_POST['slug'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Please enter a role name', 'wschat' ),
				),
				422
			);
		}

		if ( ! isset( $_POST['name'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Please enter a role name', 'wschat' ),
				),
				422
			);
		}

		if ( ! isset( $_POST['wschat_role_capability'] ) ) {
			Utils::abort(
				array(
					'message' => __( 'Please select atleast one of the capabilities', 'wschat' ),
				),
				422
			);
		}


		$role_name = sanitize_text_field( $_POST['name'] );
		$slug      = sanitize_text_field( $_POST['slug'] );

		$capabilities = array(
			'read'                 => true, // This is needed to show admin bar
			'view_admin_dashboard' => true, // This is needed to show admin bar
		);


		$wschat_caps = self::get_capabilities();

		foreach ( $wschat_caps as $cap ) {
			if ( isset( $_POST['wschat_role_capability'][ $cap ] ) ) {
				$capabilities[ $cap ] = true;
			}
		}

		// Since there is no option to update a role, remove the role and add again
		wp_roles()->remove_role( $slug );

		$role = add_role( $slug, $role_name, $capabilities );

		Utils::abort_if(
			is_null( $role ),
			array(
				'message' => __( 'Role name exists', 'wschat' ),
			),
			422
		);

		wp_send_json_success(
			array(
				'message' => __( 'Role has been updated successfully', 'wschat' ),
				'role'    => $role,
			)
		);
	}

	public static function can( $cap, $user = false ) {
		$settings = WSSettings::get_widget_settings();

		if ( true !== $settings->agent_setup ) {
			return true;
		}

		return Utils::isAgent( $user, array( $cap ) );
	}

	public static function set_agent_status() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , true );

		$status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'offline';

		update_user_meta( get_current_user_id(), 'wschat_online_status', $status );

		wp_send_json_success(
			array(
				'message' => 'Status saved',
			)
		);
	}

	/**
	 * Get the agent online status
	 *
	 * @param $agent_id int
	 *
	 * @return void
	 */
	public static function get_agent_online_status( $agent_id ) {
		$status = get_user_meta(
			$agent_id,
			'wschat_online_status',
			true
		);

		/**
		 * Fire a filter hook for default online status of agent
		 *
		 * @since 3.0.0
		 */
		$status = $status ? $status : apply_filters( 'wschat_agent_default_online_status', 'online' );

		/**
		 * Fire a filter hook for agent online status
		 *
		 * @param $status string        online|offline|busy
		 * @param $agent WP_User|int    WP_User object or agent_id
		 * @since 3.0.0
		 */
		return apply_filters( 'wschat_agent_online_status', $status, $agent_id );
	}


	public static function on_agent_login( $user_login, $user ) {
		error_log( 'on_agent_login' );
		update_user_meta( $user->ID, 'wschat_online_status', 'online' );
	}

	public static function on_agent_logout( $user_id ) {
		update_user_meta( $user_id, 'wschat_online_status', 'offline' );
	}

	public static function is_agents_online( $agents ) {
		$is_online = collect( $agents )->map(
			function ( $agent_id ) {
				return self::get_agent_online_status( $agent_id );
			}
		)->reduce(
			function ( $carry, $item ) {
				if ( 'offline' === $carry && 'offline' === $item ) {
					return 'offline';
				}

				return 'online';
			},
			'offline'
		) === 'online';

		return $is_online;
	}
}
