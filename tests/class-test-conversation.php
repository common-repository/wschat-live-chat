<?php

use WSChat\Models\User;
use WSChat\Models\Conversation;
use WSChat\WSConversation;
use WSChat\WSUser;

class Test_Conversation extends WP_UnitTestCase {

	protected function get_user() {
		$wsuser = new WSUser();

		return $wsuser->get_user();
	}

	protected function get_conversation($user = false) {
		$user = $user ? $user : $this->get_user();

		$WSconversation = new WSConversation();

		return $WSconversation->get_conversation($user);
	}

	public function test_create_new_conversation() {
		$user = $this->get_user();

		$conversation = $this->get_conversation($user);

		$this->assertTrue($conversation instanceof Conversation);
		$this->assertEquals($conversation->chat_user_id, $user->id);
	}

	public function test_create_new_conversation_action_hook() {
		$is_new_conversation_hook_called = false;
		$new_conversation_hook_arg = [];

		add_action('wschat_create_new_conversation', function ($args) use (&$is_new_conversation_hook_called, &$new_conversation_hook_arg) {
			$is_new_conversation_hook_called = true;
			$new_conversation_hook_arg = $args;
		});

		$user = $this->get_user();

		$conversation = $this->get_conversation($user);

		$this->assertTrue($is_new_conversation_hook_called);
		$this->assertIsArray($new_conversation_hook_arg);
		$this->assertArrayHasKey('user', $new_conversation_hook_arg);
		$this->assertArrayHasKey('conversation', $new_conversation_hook_arg);
	}

	public function test_make_sure_the_hook_is_not_called_if_exists() {

		wp_set_current_user(self::factory()->user->create());

		$user = $this->get_user();

		// Creates the conversation for the first time. So, the hook must be called
		$conversation = $this->get_conversation($user);

		$is_new_conversation_hook_called = false;

		add_action('wschat_create_new_conversation', function ($args) use (&$is_new_conversation_hook_called) {
			$is_new_conversation_hook_called = true;
		});

		// This is a second call. So, here the action should not be called
		$conversation = $this->get_conversation($user);

		$this->assertFalse($is_new_conversation_hook_called);
	}
}
