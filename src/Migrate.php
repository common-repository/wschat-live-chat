<?php

namespace WSChat;

class Migrate {

	const MIGRATION_BATCH_KEY = 'WSCHAT_MIGRATION_BATCH';
	const TABLE_WP_OPTIONS    = 'options';

	const TABLE_CHAT_USERS = 'wschat_users';

	const TABLE_CHAT_CONVERSATIONS = 'wschat_conversations';

	const TABLE_CHAT_PARTICIPANTS = 'wschat_participants';

	const TABLE_CHAT_MESSAGES = 'wschat_messages';

	const TABLE_CHAT_MESSAGE_NOTIFICATIONS = 'wschat_message_notifications';

	const TABLE_TAGS = 'wschat_tags';

	private $current_batch = 4;

	private $batch;

	public static function run() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$self = new self();

		if ( is_multisite() === false ) {
			$self->up();
			return;
		}

		// Get all blogs in the network and activate plugin on each one
		global $wpdb;
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			$self->up();
			restore_current_blog();
		}
	}

	public function up() {
		$this->batch = get_option( self::MIGRATION_BATCH_KEY, 0 );

		while ( $this->batch < $this->current_batch ) {
			$this->batch++;
			$method = 'upgrade_' . $this->batch;

			if ( method_exists( $this, $method ) ) {
				$this->{$method}();
			}
		}

		update_option( self::MIGRATION_BATCH_KEY, $this->current_batch, false );
	}

	public function upgrade_1() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// chat_users table
		$table_name  = $wpdb->prefix . self::TABLE_CHAT_USERS;
		$sql_tickets = "CREATE TABLE IF NOT EXISTS $table_name
                (   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `session_key` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
			        `meta` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
					`created_at` TIMESTAMP NOT NULL,
					`updated_at` TIMESTAMP NOT NULL,
                    PRIMARY KEY (`id`)
                ) $charset_collate;";
		dbDelta( $sql_tickets );

		// chat_conversations table
		$table_name  = $wpdb->prefix . self::TABLE_CHAT_CONVERSATIONS;
		$sql_tickets = "CREATE TABLE IF NOT EXISTS $table_name
                (   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `chat_user_id` BIGINT UNSIGNED NOT NULL,
			        `meta` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
					`created_at` TIMESTAMP NOT NULL,
					`updated_at` TIMESTAMP NOT NULL,
                    PRIMARY KEY (`id`)
                ) $charset_collate;";
		dbDelta( $sql_tickets );

		// chat_participants table
		$table_name  = $wpdb->prefix . self::TABLE_CHAT_PARTICIPANTS;
		$sql_tickets = "CREATE TABLE IF NOT EXISTS $table_name
                (   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `conversation_id` BIGINT UNSIGNED NOT NULL,
                    `user_id` BIGINT UNSIGNED NOT NULL,
                    `type` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                    `last_active_at` TIMESTAMP NOT NULL,
                    PRIMARY KEY (`id`)
                ) $charset_collate;";
		dbDelta( $sql_tickets );

		// chat_messages table
		$table_name  = $wpdb->prefix . self::TABLE_CHAT_MESSAGES;
		$sql_tickets = "CREATE TABLE IF NOT EXISTS $table_name
                (   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `conversation_id` BIGINT UNSIGNED NOT NULL,
                    `participant_id` BIGINT UNSIGNED NOT NULL,
			        `body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
                    `type` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					`created_at` TIMESTAMP NOT NULL,
					`updated_at` TIMESTAMP NOT NULL,
                    PRIMARY KEY (`id`)
                ) $charset_collate;";
		dbDelta( $sql_tickets );

		// chat_message_notifications table
		$table_name  = $wpdb->prefix . self::TABLE_CHAT_MESSAGE_NOTIFICATIONS;
		$sql_tickets = "CREATE TABLE IF NOT EXISTS $table_name
                (   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `conversation_id` BIGINT UNSIGNED NOT NULL,
                    `participant_id` BIGINT UNSIGNED NOT NULL,
					`seen_at` TIMESTAMP NULL DEFAULT NULL,
					`is_sender` BOOLEAN NOT NULL DEFAULT FALSE,
					`created_at` TIMESTAMP NOT NULL,
					`updated_at` TIMESTAMP NOT NULL,
                    PRIMARY KEY (`id`)
                ) $charset_collate;";
		dbDelta( $sql_tickets );

		// Add Agent Role
		global $wp_roles;
		$user_roles        = $wp_roles->role_names;
		$user_roles_create = array(
			'wschat_agent' => 'WSChat Agents',
		);

		foreach ( $user_roles_create as $role_slug => $user_role ) {
			if ( ! isset( $user_roles[ $role_slug ] ) ) {
				add_role(
					$role_slug,
					$user_role,
					array(
						'wschat_crm_role'      => true,
						'read'                 => true,
						'view_admin_dashboard' => true,
					)
				);
			}
		}
	}

	public function upgrade_2() {
		global $wpdb;
		// chat_message_notifications table
		$conversation       = $wpdb->prefix . self::TABLE_CHAT_CONVERSATIONS;
		$conversation_users = $wpdb->prefix . self::TABLE_CHAT_USERS;

		$participants = $wpdb->prefix . self::TABLE_CHAT_PARTICIPANTS;

		$message              = $wpdb->prefix . self::TABLE_CHAT_MESSAGES;
		$message_notification = $wpdb->prefix . self::TABLE_CHAT_MESSAGE_NOTIFICATIONS;

		$if_exists = $wpdb->get_row( ' DESCRIBE ' . $wpdb->prefix . 'wschat_message_notifications message_id' );


		if ( empty( $if_exists ) ) {

			$sql_tickets = "ALTER TABLE `$message_notification` ADD `message_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `conversation_id`";
			wpFluent()->statement( $sql_tickets );
		}

		// Conversation
		wpFluent()->statement( 'ALTER TABLE ' . $conversation . ' ADD FOREIGN KEY (`chat_user_id`) REFERENCES ' . $conversation_users . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		// Participants
		wpFluent()->statement( 'ALTER TABLE ' . $participants . ' ADD FOREIGN KEY (`conversation_id`) REFERENCES ' . $conversation . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		// Message
		wpFluent()->statement( 'ALTER TABLE ' . $message . ' ADD FOREIGN KEY (`conversation_id`) REFERENCES ' . $conversation . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		wpFluent()->statement( 'ALTER TABLE ' . $message . ' ADD FOREIGN KEY (`participant_id`) REFERENCES ' . $participants . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		// Message Notification
		wpFluent()->statement( 'ALTER TABLE ' . $message_notification . ' ADD FOREIGN KEY (`conversation_id`) REFERENCES ' . $conversation . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		wpFluent()->statement( 'ALTER TABLE ' . $message_notification . ' ADD FOREIGN KEY (`participant_id`) REFERENCES ' . $participants . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );

		wpFluent()->statement( 'ALTER TABLE ' . $message_notification . ' ADD FOREIGN KEY (`message_id`) REFERENCES ' . $message . ' (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT' );
	}

	public function upgrade_3() {
		global $wpdb;

		$table_name  = $wpdb->prefix . self::TABLE_TAGS;
		$sql_tickets = "CREATE TABLE  IF NOT EXISTS $table_name
                (   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `name` VARCHAR(60) NOT NULL,
                    `color` VARCHAR(60) NOT NULL,
                    PRIMARY KEY (`id`)
                )" . $wpdb->get_charset_collate() . ';';
		dbDelta( $sql_tickets );
	}

	public function upgrade_4() {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_CHAT_PARTICIPANTS;
		$if_exists  = $wpdb->get_row( ' DESCRIBE ' . $wpdb->prefix . 'wschat_participants status_id ' );

		if ( empty( $if_exists ) ) {

			$sql_tickets = "ALTER TABLE $table_name ADD `status_id` TINYINT NOT NULL DEFAULT 2 AFTER `type`";
			wpFluent()->statement( $sql_tickets );

		}

		$if_exists = $wpdb->get_row( 'DESCRIBE ' . $wpdb->prefix . 'wschat_participants invited_by ' );

		if ( empty( $if_exists ) ) {

			$sql_tickets = "ALTER TABLE $table_name ADD `invited_by` BIGINT(20) NULL DEFAULT NULL AFTER `status_id`";
			wpFluent()->statement( $sql_tickets );

		}
	}
}
