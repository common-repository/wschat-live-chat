<?php

use WSChat\Models\User;
use WSChat\WSUser;

class Test_New_User extends WP_UnitTestCase {

	public function test_create_user_event() {
		$wsuser = new WSUser();

		$on_create_user_hook_called = false;

		$new_user_hook = 'wschat_new_chat_user';

		add_filter($new_user_hook, function ($user) use (&$on_create_user_hook_called) {
			$on_create_user_hook_called = true;
		});

		$user = $wsuser->get_user();

		$this->assertTrue($user instanceof User);
		$this->assertTrue($on_create_user_hook_called);
	}

	public function test_create_user_event_not_triggered_on_request() {
		$wsuser = new WSUser();

		$on_create_user_hook_called = false;

		add_filter('wschat_new_chat_user', function ($user) use (&$on_create_user_hook_called) {
			$on_create_user_hook_called = true;
		});

		$user = $wsuser->get_user(false);

		$this->assertFalse($user);
		$this->assertFalse($on_create_user_hook_called);
	}

	public function test_get_user_returns_logged_in_user() {
		$user_id = self::factory()->user->create();

		wp_set_current_user($user_id);
		$wsuser = new WSUser();

		$user = $wsuser->get_user();

		$this->assertEquals($user->user_id, $user_id);
	}

	public function test_user_has_role() {
		wp_set_current_user(1);

		$user = wp_get_current_user();

		$this->assertTrue(is_super_admin());
		// $this->assertTrue($user->has_cap('wschat_crm_role'));
	}
}
