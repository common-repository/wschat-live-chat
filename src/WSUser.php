<?php

namespace WSChat;

use WSChat\Models\User;

class WSUser {

	public static function init() {
	}

	public function get_user( $create = true ) {
		$user   = false;
		$cookie = null;

		if ( isset( $_COOKIE[ User::SESSION_KEY_COOKIE_NAME ] ) ) {
			$cookie = sanitize_text_field( $_COOKIE[ User::SESSION_KEY_COOKIE_NAME ] );
		}

		if ( is_user_logged_in() ) {
			$user = User::getByUserId( get_current_user_id() );
		} else {
			if ( false === $user && null !== $cookie ) {
				$user = User::getBySessionKey( $cookie );
			}
		}

		/**
		 * Fire a filter hook to check new user can be created or not
		 *
		 * @param $create
		 *
		 * @since 2.0.0
		 */
		$create = apply_filters( 'wschat_can_create_user', $create );

		if ( true === $create && false === $user ) {
			$user = $this->create_user();
			setcookie( User::SESSION_KEY_COOKIE_NAME, $user->session_key );
		}

		return $user;
	}

	public function create_user() {
		$user = User::create();

		/**
		 * Fire an action hook for new chat user
		 *
		 * @param $user
		 *
		 * @since 2.0.0
		 */
		do_action( 'wschat_new_chat_user', $user );

		return $user;
	}
}
