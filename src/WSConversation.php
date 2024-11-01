<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WP_User_Query;
use WSChat\Models\Conversation;
use WSChat\Models\Participant;
use WSChat\Models\User;
use WSChat\Models\Message;
use WSChat\Transformers\ConversationTransformer;

class WSConversation {
	public static function init() {
		$self = new self();

		add_action( 'wp_ajax_nopriv_wschat_start_conversation', array( $self, 'start_conversation' ) );
		add_action( 'wp_ajax_wschat_start_conversation', array( $self, 'start_conversation' ) );
		add_action( 'wp_ajax_wschat_admin_deAssign_Agent', array( $self, 'deAssign_Agent' ) );
		add_action( 'wp_ajax_wschat_admin_get_conversations', array( $self, 'get_conversations' ) );
		add_action( 'wp_ajax_wschat_admin_join_conversation', array( $self, 'join_conversation' ) );
		add_action( 'wp_ajax_elex_chatgpt_generate_reply', array( $self, 'elex_chatgpt_generate_reply' ) );
		add_action( 'wp_ajax_wschat_admin_delete_conversation', array( $self, 'delete_conversation' ) );
		add_action( 'wp_ajax_wschat_admin_end_session', array( $self, 'end_session' ) );

		add_action( 'wp_ajax_nopriv_wschat_email_transcript', array( $self, 'email_transcript' ) );
		add_action( 'wp_ajax_wschat_email_transcript', array( $self, 'email_transcript' ) );
		add_action( 'wschat_conversation_session_ended', array( self::class, 'send_convo_transcript_on_end' ), 20, 2 );
		add_action( 'wschat_conversation_session_ended', array( self::class, 'send_feedback_email' ), 10, 2 );

		add_action( 'wp_ajax_nopriv_wschat_coversation_feedback', array( $self, 'send_feedback' ) );
		add_action( 'wp_ajax_wschat_coversation_feedback', array( $self, 'send_feedback' ) );
		add_action( 'wp_footer', array( self::class, 'update_feedback_from_email' ) );

		add_filter(
			'query_vars',
			function ( $query_vars ) {
			$query_vars[] = 'wschat_email_feedback';
			return $query_vars;
			}
		);

		add_action( 'wschat_on_send_message', array( self::class, 'start_session' ), 10, 3 );

		add_action( 'wp_ajax_wschat_agent_accept_invitaion', array( self::class, 'accept_invitation' ), 10, 3 );
		add_action( 'wp_ajax_wschat_agent_decline_invitaion', array( self::class, 'decline_invitation' ), 10, 3 );

		add_action( 'admin_menu', array( self::class, 'add_history' ) );

		add_action( 'wschat_conversation_session_started', array( self::class, 'update_geo_location' ) );
		add_action( 'wschat_create_new_conversation', array( self::class, 'update_geo_location' ) );

	}

	/**
	 * Start a conversation as a Guest or User
	 */
	public static function start_conversation() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , true );

		$user = ( new WSUser() )->get_user();

		$conversation = self::get_conversation( $user );

		if ( $conversation->is_session_ended() ) {
			$conversation->end_session( $conversation->participants()->findByUser( $user ) );
		}

		$conversation = $conversation->toArray();

		/**
		 * Fire a filter hook to check whether to start the conversation or not
		 *
		 * @param array of user info and conversation info
		 *
		 * @since 2.0.0
		 *
		 */
		$can_start_conversation = apply_filters( 'wschat_can_start_a_conversation', true );

		if ( false === $can_start_conversation ) {
			$failed_response = array(
				'message' => __( 'You are not able to start conversation' ),
			);

			/**
			 * Fire a filter hook for failed response on conversation start
			 *
			 * @param array of user info and conversation info
			 *
			 * @since 2.0.0
			 *
			 */
			$failed_response = apply_filters( 'wschat_start_conversation_failed_response', $failed_response );

			Utils::abort( $failed_response, 503 );
		}

		$conversation['user'] = $user;

		if ( $conversation['user'] ) {
			$meta = $user->meta;

			if ( isset( $_REQUEST['current_url'] ) ) {
				$current_url = sanitize_text_field( $_REQUEST['current_url'] );
				if ( ! isset( $meta['page_visits'] ) ) {
					$meta['page_visits'] = array();
				}

				if ( current( $meta['page_visits'] ) !== $current_url ) {
					array_unshift(
						$meta['page_visits'],
						array(
							'title' => isset( $_REQUEST['title'] ) ? sanitize_text_field( $_REQUEST['title'] ) : '',
							'url'   => $current_url,
						)
					);
				}

				if ( count( $meta['page_visits'] ) > 2 ) {
					array_pop( $meta['page_visits'] );
				}
			}

			$user->updateMeta( $meta );

			$conversation['user'] = $conversation['user']->toArray();
		}

		/**
		 * Fire an action hook for starting conversation
		 *
		 * @param array of conversation info and user info
		 *
		 * @since 2.0.0
		 *
		 */
		do_action(
			'wschat_on_start_conversation',
			$conversation,
			$user
		);

		/**
		 * Fire a filter hook for user conversation
		 *
		 * @param $conversation
		 *
		 * @since 2.0.0
		 *
		 */
		$conversation = apply_filters( 'wschat_user_conversation', $conversation );
		wp_send_json_success( $conversation );
	}

	/**
	 * Get conversation from a Chat User
	 *
	 * @param User $user
	 *
	 * @return Conversation
	 */
	public static function get_conversation( $user ) {
		$conversation = Conversation::find_by_chat_user_id( $user->id );

		if ( false === $conversation ) {
			$conversation = self::create_conversation( $user );
		}

		return $conversation;
	}

	private static function create_conversation( User $user ) {
		$conversation = Conversation::create( $user->id );
		$conversation->participants()->add( $user );

		/**
		 * Fire an action hook to create new conversation
		 *
		 * @param array of user info and conversation info
		 *
		 * @since 2.0.0
		 *
		 */
		do_action(
			'wschat_create_new_conversation',
			$conversation,
			$user
		);

		return $conversation;
	}

	/**
	 * Join to a conversation as an Agent
	 */
	public function join_conversation() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$id = sanitize_text_field( $_POST['conversation_id'] );

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $id,
			)
		);

		// Abort on bad request
		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		Utils::abort_unless(
			is_super_admin() || WSAgent::can( 'wschat_init_chat' ),
			array(
				'message' => __( 'You dont have permission to join this conversation' ),
			)
		);

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );
		Utils::abort_if(
			false === $participant && WSAgent::can( 'wschat_can_see_unassigned_chats' ) === false,
			array(
				'message' => __( 'You dont have permission to join this conversation' ),
			),
			403
		);

		if ( isset( $_POST['agent_id'] ) ) {
			Utils::abort_unless(
				is_super_admin() || WSAgent::can( 'wschat_invite_agent' ),
				array(
					'message' => __( 'You dont have permission to invite an Agent' ),
				)
			);
		}

		$agent = isset( $_POST['agent_id'] ) ? get_user_by( 'ID', sanitize_text_field( $_POST['agent_id'] ) ) : wp_get_current_user();
		Utils::abort_unless(
			Utils::isAgent( $agent ),
			array(
				'message' => __( 'You are not an agent', 'wschat' ),
			)
		);

		$invited_by = get_current_user_id() !== $agent->ID ? get_current_user_id() : null;

		$participant = $conversation->participants()->add( $agent, $invited_by );

		// ==> Joining chat Message to be only displayed when agents accepts the invitation.
		
		// if ( null !== $invited_by ) {

		// 	/**
		// 	 * Fire an action hook when participant has joined the chat
		// 	 *
		// 	 * @param $conversation
		// 	 * @param $participant
		// 	 *
		// 	 * @since 2.0.0
		// 	 *
		// 	 */
		// 	do_action( 'wschat_participant_has_joined_the_chat', $conversation, $participant );
		// }

		// <=== Joining chat Message to be only displayed when agents accepts the invitation.

		$participants = $conversation->participants()->applyFilters(
			array(
				'type'            => Participant::TYPE_AGENT,
				'conversation_id' => $id,
			)
		);

		$participants = $participants->get();

		$conversation = $conversation->toArray();

		$conversations = ( new ConversationTransformer( collect( [ $conversation ] ), wp_get_current_user() ) )->transform();
		$conversation  = $conversations->first();

		$conversation['participants'] = get_users(
			array(
				'include' => Arr::pluck( $participants, 'user_id' ),
				'fields'  => array(
					'ID',
					'display_name',
				),
			)
		);

		$agent_query = new WP_User_Query(
			array(
				'role__in' => array_keys( WSAgent::get_roles() ),
				'fields'   => array(
					'ID',
					'display_name',
				),
			)
		);

		$conversation['agents'] = $agent_query->get_results();

		$conversation['roles'] = WSAgent::get_roles();

		$conversation['agents'] = array_map(
			function ( $agent ) {
					return array(
						'ID'           => $agent->ID,
						'display_name' => $agent->display_name,
						'avatar'       => get_avatar_url( $agent->ID ),
						'status'       => WSAgent::get_agent_online_status( $agent->ID ),
					);
			},
			$conversation['agents']
		);

		if ( isset( $conversation['meta']['session']['initiated_at'] ) ) {
			$initiated_at = date_create( $conversation['meta']['session']['initiated_at'] );
			$now          = date_create( $conversation['updated_at'] );

			if ( isset( $conversation['meta']['session']['ended_at'] ) && isset( $conversation['meta']['session']['is_ended'] ) && $conversation['meta']['session']['is_ended'] ) {
				$now = date_create( $conversation['meta']['session']['ended_at'] );
			}

			$diff = $now->diff( $initiated_at );

			$conversation['time_spent'] = array(
				'hour'    => $diff->h,
				'minutes' => $diff->i,
			);
		}

		/**
		 * When an agent joins the conversation
		 *
		 * @param array (
		 *  conversation array $conversation
		 *  participant array $participant
		 * )
		 *
		 * @since 2.0.0
		 *
		 */
		do_action(
			'wschat_on_agent_join_conversation',
			array(
				'conversation' => $conversation,
				'participant'  => $participant,
			)
		);

		wp_send_json_success( $conversation );
	}

	/* This callback function will retrieve the latest message from user end */
	public function elex_chatgpt_generate_reply() {

		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		Utils::abort_unless_agent();

		$conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( $_POST['conversation_id'] ) : null;
		$participant_id  = isset( $_POST['participant_id'] ) ? sanitize_text_field( $_POST['participant_id'] ) : null;
		global $wpdb;
		$recent_text = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT body 
				FROM {$wpdb->prefix}wschat_messages 
				WHERE conversation_id = %s 
				AND participant_id = %s 
				AND id = (SELECT MAX(id) 
						FROM wp_wschat_messages
						WHERE conversation_id = %s
						AND participant_id = %s
						)",
				$conversation_id,
				$participant_id,
				$conversation_id,
				$participant_id
			)
		);


		

		$recent_msg = json_decode( $recent_text[0], true );
		wp_send_json_success( $recent_msg );
	}


	public static function accept_invitation() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$id = sanitize_text_field( $_POST['conversation_id'] );

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $id,
			)
		);

		// Abort on bad request
		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );

		Utils::abort_unless(
			intval( $participant->status_id ) === Participant::STATUS_INVITE && Participant::TYPE_AGENT === $participant->type,
			array(
				'message' => __( 'You dont have permission to join this conversation', 'wschat' ),
			)
		);

		$participant->accept();

		/**
		 * Fire an action hook when participant has joined the chat
		 *
		 * @param $conversation
		 * @param $participant
		 *
		 * @since 2.0.0
		 *
		 */
		do_action( 'wschat_participant_has_joined_the_chat', $conversation, $participant );

		wp_send_json_success(
			array(
				'message' => __( 'You joined the chat successfully', 'wschat' ),
			)
		);
	}

	public static function decline_invitation() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$id = sanitize_text_field( $_POST['conversation_id'] );

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $id,
			)
		);

		// Abort on bad request
		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );

		Utils::abort_unless(
			intval( $participant->status_id ) === Participant::STATUS_INVITE && Participant::TYPE_AGENT === $participant->type,
			array(
				'message' => __( 'You dont have permission to join this conversation', 'wschat' ),
			)
		);

		$participant->delete();

		/**
		 * Fire an action when participant has declined the chat
		 *
		 * @param $conversation
		 * @param $participant
		 *
		 * @since 2.0.0
		 *
		 */
		do_action( 'wschat_participant_has_declined_the_chat', $conversation, $participant );

		wp_send_json_success(
			array(
				'message' => __( 'You have declined the invitation', 'wschat' ),
			)
		);
	}

	public function get_conversations() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		Utils::abort_unless_agent();

		$filters = array();

		if ( isset( $_POST['before'] ) ) {
			$filters['before'] = sanitize_text_field( $_POST['before'] );
		}


		if ( isset( $_POST['search'] ) ) {
			$filters['search'] = sanitize_text_field( $_POST['search'] );
		}

		if ( isset( $_POST['type'] ) ) {
			$type = sanitize_text_field( $_POST['type'] );
			if ( 'unassigned' === $type ) {
				$filters['unassigned'] = true;
			}

			if ( 'my_chats' === $type ) {
				$filters['agent_id'] = get_current_user_id();
			}
		}

		if ( false === WSAgent::can( 'wschat_can_see_unassigned_chats' ) ) {
			unset( $filters['unassigned'] );
			$filters['agent_id'] = get_current_user_id();
		}

		$filters['status'] = 'active';
		if ( isset( $_POST['status'] ) ) {
			$filters['status'] = sanitize_text_field( $_POST['status'] );
		}

		$filters['has_messages'] = true;

		$conversations = collect( ( new Conversation() )->get( $filters ) );

		if ( $conversations->count() === 0 ) {
			wp_send_json_success( $conversations );
			return;
		}

		wp_send_json_success( ( new ConversationTransformer( $conversations, wp_get_current_user() ) )->transform() );
	}

	public static function get_live_visitors() {
		$filters['has_no_messages'] = true;

		if ( false === WSAgent::can( 'wschat_can_see_unassigned_chats' ) ) {
			unset( $filters['unassigned'] );
			$filters['agent_id'] = get_current_user_id();
		}

		$conversations = collect( ( new Conversation() )->get( $filters ) );

		if ( $conversations->count() === 0 ) {
			return $conversations;
		}

		return ( new ConversationTransformer( $conversations, wp_get_current_user() ) )->transform();
	}

	public function email_transcript() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		$email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';

		if ( is_user_logged_in() ) {

			if ( ! isset( $_POST['conversation_id'] ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid Conversation ID', 'wschat' ),
					),
					412
				);
			}

			$id = sanitize_text_field( $_POST['conversation_id'] );

			$conversation = ( new Conversation() )->find(
				array(
					'id' => $id,
				)
			);
		} else {
			$user = ( new WSUser() )->get_user( false );

			if ( ! $user || ! $email ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid Request', 'wschat' ),
					),
					412
				);
			}

			$conversation = self::get_conversation( $user );
		}

		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		self::send_email_transcript( $conversation, $email );

		wp_send_json_success(
			array(
				'msg' => __( 'Email has been sent', 'wschat' ),
			)
		);
	}

	public static function send_email_transcript( $conversation, $email ) {
		$meta = $conversation->get_meta();

		if ( ! isset( $meta['session'] ) ) {
			return;
		}

		$filters = array(
			'page_no' => 1,
			'limit'   => 250,
		);

		if ( isset( $meta['session']['initiated_at'] ) ) {
			$filters['created_at']['min'] = $meta['session']['initiated_at'];
		}

		if ( isset( $filters['created_at']['min'] ) ) {
			$filters['created_at']['max'] = current_time( 'mysql', true );
		}

		$start_time = get_date_from_gmt( $meta['session']['initiated_at'], get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );

		$messages = collect( $conversation->messages()->get( $filters ) );

		$participants = collect(
			$conversation->participants()->get(
				array(
					'conversation_id' => $conversation->id,
				)
			)
		)->keyBy( 'id' );

		$users = collect(
			( new User() )->get(
				array(
					'id' => $participants->pluck( 'user_id' )->toArray(),
				)
			)
		)->keyBy(
			function ( $user ) {
				return  $user->id;
			}
		);

		$participant_users = array();

		foreach ( $participants as $user_participant ) {
			if ( Participant::TYPE_AGENT === $user_participant['type'] ) {
				continue;
			}

			$participant_users[ $user_participant['id'] ] = $users->get( $user_participant['user_id'] );
		}

		$messages = $messages->map(
			function ( $message ) use ( $participant_users, $participants ) {
				$message               = ( new Message() )->parseData( $message )->toArray();
				$message['created_at'] = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , strtotime( $message['created_at'] ), true );
				$user                  = Arr::get( $participant_users, Arr::get( $message, 'participant_id' ) );

				$user = '';
				if ( array_key_exists( Arr::get( $message, 'participant_id' ), $participant_users ) ) {
					$user = $participant_users[ Arr::get( $message, 'participant_id' ) ];
				}

				if ( $user ) {
					$message['user'] = $user->meta;
				} else {
					$message['user'] = array(
						'name' => __( 'Deleted User', 'wschat' ),
					);

					if ( isset( $participants[ $message['participant_id'] ] ) && Participant::TYPE_AGENT === $participants[ $message['participant_id'] ]['type'] ) {
						$agent = get_user_by( 'ID', $participants[ $message['participant_id'] ]['user_id'] );

						if ( $agent ) {
							$message['user']['name'] = $agent->display_name;
						}
					}
				}

				return $message;
			}
		);

		ob_start();
		Utils::include_file( '/resources/views/email/transcript.php', compact( 'conversation', 'messages', 'start_time', 'meta' ) );
		$content = ob_get_clean();

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail(
			$email,
			/**
			 * Fire a filter hook for email transcript subject
			 *
			 * @since 2.0.0
			 */
			apply_filters( 'wschat_transcript_email_subject', __( 'WSChat email transcipt', 'wschat' ) ),
			$content,
			$headers
		);
	}

	public static function send_feedback_email( Conversation $conversation ) {
		$email_Settings = WSSettings::get_widget_settings();
		if ( true !== $email_Settings['feedback_form'] || true !== $email_Settings['email_notifications'] ) {
			return;
		}

		$user          = $conversation->chat_user();
		$data['email'] = Arr::get( $user->toArray(), 'meta.email' );
		$data['name']  = Arr::get( $user->toArray(), 'meta.name' );

		if ( ! $data['email'] ) {
			return;
		}

		ob_start();
		Utils::include_file( '/resources/views/email/feedback.php', $data );
		$content = ob_get_clean();

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail(
			$data['email'],
			/**
			 * Fire a filter hook for feedback email subject
			 *
			 * @since 2.0.0
			 */
			apply_filters( 'wschat_feedback_email_subject', __( 'WSChat Feedback', 'wschat' ) ),
			$content,
			$headers
		);
	}

	public function send_feedback() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		$feedback = isset( $_POST['feedback'] ) ? sanitize_text_field( $_POST['feedback'] ) : null;

		Utils::abort_unless(
			$feedback,
			array(
				'message' => __( 'Invalid feedback' ),
			)
		);

		$user = ( new WSUser() )->get_user();

		$conversation = self::get_conversation( $user );

		$conversation->add_meta( 'user_feedback', $feedback );

		wp_send_json_success(
			array(
				'msg' => __( 'Thanks for your valuable feedback' ),
			)
		);
	}

	public function delete_conversation() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$id = sanitize_text_field( $_POST['conversation_id'] );

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $id,
			)
		);

		// Abort on bad request
		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		// Deletes conversations, messages, participants and chat user
		$conversation->delete();

		wp_send_json_success(
			array(
				'msg' => __( 'Conversation has been deleted' ),
			)
		);
	}

	public static function start_session( Message $message, Participant $participant, Conversation $conversation ) {
		if ( 'info' === $message->type ) {
			return false;
		}
		$started = $conversation->start_session( $participant );

		if ( true === $started ) {
			/**
			 * Fire action on session start
			 *
			 * @param Conversation $conversation
			 * @param Participant $participant
			 * @param Message $message
			 *
			 * @since 2.0.0
			 *
			 */
			do_action( 'wschat_conversation_session_started', $conversation, $participant, $message );
		}
	}

	public static function end_session() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$id           = sanitize_text_field( $_POST['conversation_id'] );
		$conversation = ( new Conversation() )->find(
			array(
				'id' => $id,
			)
		);

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );

		$conversation->end_session( $participant );

		/**
		 * Fire an action hook when session was ended
		 *
		 * @param self $this Conversation which being started
		 * @param Participant $participant Who started the session
		 *
		 * @since 2.0.0
		 *
		 */
		do_action( 'wschat_conversation_session_ended', $conversation, $participant );

		wp_send_json_success(
			array(
				'msg'  => __( 'Conversation session has been ended' ),
				'meta' => $conversation->get_meta(),
			)
		);
	}

	public static function add_history() {
		add_submenu_page(
			'wschat_chat',
			__( 'Chat History', 'wschat' ),
			__( 'Chat History', 'wschat' ),
			'read',
			'wschat_history',
			array( self::class, 'render_history_page' ),
			2
		);
	}

	public static function render_history_page() {
		if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ) ) ) {
			Utils::abort();
		}
		if ( isset( $_GET['conversation_id'] ) ) {
			Utils::include_file( '/resources/views/admin/chat-history.php' );
			return;
		}

		$filters = array(
			'status'       => 'history',
			'limit'        => 10,
			'search'       => isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '',
			'page_no'      => isset( $_GET['page_no'] ) ? sanitize_text_field( $_GET['page_no'] ) : 1,
			'has_messages' => true,
		);

		if ( false === WSAgent::can( 'wschat_can_see_unassigned_chats' ) ) {
			unset( $filters['unassigned'] );
			$filters['agent_id'] = get_current_user_id();
		}

		$conversations = ( new Conversation() )->get( $filters );
		$conversations = ( new ConversationTransformer( collect( $conversations ), wp_get_current_user() ) )->transform();

		$data = array(
			'conversations' => $conversations,
			'limit'         => $filters['limit'],
			'page_no'       => $filters['page_no'],
			'total'         => ( new Conversation() )->applyFilters( $filters )->count(),
			'search'        => isset( $_GET['search'] ) ? esc_attr( sanitize_text_field( $_GET['search'] ) ) : '',
		);

		$data['total_pages'] = (int) ceil( $data['total'] / $data['limit'] );
		Utils::include_file( '/resources/views/admin/chat-histories.php', $data );
	}

	public static function send_convo_transcript_on_end( Conversation $conversation ) {
		$user           = $conversation->chat_user();
		$email          = Arr::get( $user->toArray(), 'meta.email' );
		$email_Settings = WSSettings::get_widget_settings();
		if ( ! $email ) {
			return;
		}
		if ( true === $email_Settings['email_transcript_chat'] && true === $email_Settings['email_notifications'] ) {
			self::send_email_transcript( $conversation, $email );
		}
	}

	public static function update_geo_location( $conversation ) {
		$meta = $conversation->get_meta();

		$geo_ips = Arr::get( $meta, 'geo_ips', array() );

		$client_ip = Utils::get_client_ip();

		if ( isset( $geo_ips[ $client_ip ] ) ) {
			return;
		}

		$ttl = get_option( 'wschat_geo_ip_ttl', strtotime( 'now' ) );

		if ( $ttl > strtotime( 'now' ) ) {
			return;
		}

		$info = Utils::get_geo_location( $client_ip );

		if ( ! is_array( $info ) ) {
			return;
		}

		$info['ip'] = $client_ip;

		$geo_ips[ $client_ip ] = Utils::get_geo_location( $client_ip );

		$meta['geo_ips'] = $geo_ips;

		$conversation->update_meta( $meta );
	}

	public static function update_feedback_from_email() {
		add_filter(
			'query_vars',
			function ( $query_vars ) {
			$query_vars[] = 'wschat_email_feedback';
			}
		);

		$feedback = get_query_var( 'wschat_email_feedback' );

		if ( 'good' !== $feedback && 'bad' !== $feedback ) {
			return;
		}

		$user = ( new WSUser() )->get_user();

		$conversation = self::get_conversation( $user );

		$conversation->add_meta( 'user_feedback', 'good' === $feedback ? 'thumb_up' : 'thumb_down' );
	}

	/* This function is for de-assigning of agents from customer info tab 
	is still in pending stage
*/
	public function deAssign_Agent() {

		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );

		Utils::abort_unless_agent();

		if ( ! isset( $_POST['conversation_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Conversation ID', 'wschat' ),
				),
				412
			);
		}

		$conversation_id = sanitize_text_field( $_POST['conversation_id'] );

		$conversation = ( new Conversation() )->find(
			array(
				'id' => $conversation_id,
			)
		);

		// Abort on bad request
		Utils::abort_unless(
			$conversation,
			array(
				'message' => __( 'Invalid Request', 'wschat' ),
			),
			412
		);

		Utils::abort_unless(
			is_super_admin() || WSAgent::can( 'wschat_init_chat' ),
			array(
				'message' => __( 'You dont have permission to remove Agent from this conversation' ),
			)
		);

		$participant = $conversation->participants()->findByUser( wp_get_current_user() );
		Utils::abort_if(
			false === $participant && WSAgent::can( 'wschat_can_see_unassigned_chats' ) === false,
			array(
				'message' => __( 'You dont have permission to remove Agent from this conversation' ),
			),
			403
		);

		if ( ! isset( $_POST['agent_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Agent ID', 'wschat' ),
				),
				412
			);
		}

		$participant_id = sanitize_text_field( $_POST['agent_id'] );

		$participants = $conversation->participants()->applyFilters(
			array(
				'type'            => Participant::TYPE_AGENT,
				'conversation_id' => $conversation_id,
			)
		);
		$participants = $participants->get();

		if ( count( $participants ) > 1 ) {
			// Start a transaction
			wpFluent()->statement( 'SET FOREIGN_KEY_CHECKS=0' );
			try {
				// Delete associated rows from the message notifications table
				wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS )
					->where( 'participant_id', $participant_id )
					->where( 'conversation_id', $conversation_id )
					->delete();

				// Delete associated rows from the chat messages table
				wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES )
					->where( 'participant_id', $participant_id )
					->where( 'conversation_id', $conversation_id )
					->delete();

				// Delete the row from the participants table
				wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )
					->where( 'user_id', $participant_id )
					->where( 'conversation_id', $conversation_id )
					->delete();

				// Commit the transaction
			} catch ( Exception $e ) {
				// Handle or log the error as needed
				echo 'Error: ' . esc_html( $e->getMessage() );
			}
			wpFluent()->statement( 'SET FOREIGN_KEY_CHECKS=1' );


			$participants = $conversation->participants()->applyFilters(
				array(
					'type'            => Participant::TYPE_AGENT,
					'conversation_id' => $conversation_id,
				)
			);

			$participants   = $participants->get();
			$conversation_1 = $conversation;
			$conversation   = $conversation->toArray();

			$conversations = ( new ConversationTransformer( collect( [ $conversation ] ), wp_get_current_user() ) )->transform();
			$conversation  = $conversations->first();

			$conversation['participants'] = get_users(
				array(
					'include' => Arr::pluck( $participants, 'user_id' ),
					'fields'  => array(
						'ID',
						'display_name',
					),
				)
			);

			$agent_query = new WP_User_Query(
				array(
					'role__in' => array_keys( WSAgent::get_roles() ),
					'fields'   => array(
						'ID',
						'display_name',
					),
				)
			);

			$conversation['agents'] = $agent_query->get_results();

			$conversation['roles'] = WSAgent::get_roles();

			$conversation['agents'] = array_map(
				function ( $agent ) {
					return array(
						'ID'           => $agent->ID,
						'display_name' => $agent->display_name,
						'avatar'       => get_avatar_url( $agent->ID ),
						'status'       => WSAgent::get_agent_online_status( $agent->ID ),
					);
				},
				$conversation['agents']
			);

			if ( isset( $conversation['meta']['session']['initiated_at'] ) ) {
				$initiated_at = date_create( $conversation['meta']['session']['initiated_at'] );
				$now          = date_create( $conversation['updated_at'] );

				if ( isset( $conversation['meta']['session']['ended_at'] ) && isset( $conversation['meta']['session']['is_ended'] ) && $conversation['meta']['session']['is_ended'] ) {
					$now = date_create( $conversation['meta']['session']['ended_at'] );
				}

				$diff = $now->diff( $initiated_at );

				$conversation['time_spent'] = array(
					'hour'    => $diff->h,
					'minutes' => $diff->i,
				);
			}

			/**
			 * Fire an action hook when agent is been deassigned  from the chat
			 *
			 * @param $conversation
			 * @param $participant
			 *
			 * @since 2.0.0
			 *
			 */
			do_action( 'wschat_agent_is_designed_from_the_chat', $conversation_1, $participant, $participant_id );

		} else {
				wp_send_json_error(
					array(
						'message' => __( 'Above Agent can not be removed from the chat.', 'wschat' ),
					),
					412
				);
		}

		wp_send_json_success( $conversation );

	}
}
