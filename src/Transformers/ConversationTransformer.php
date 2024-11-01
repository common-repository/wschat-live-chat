<?php
namespace WSChat\Transformers;

use Illuminate\Support\Collection;
use WP_User;
use WP_User_Query;
use WSChat\Models\Participant;
use WSChat\Models\Message;
use WSChat\Models\User;
use WSChat\WSAgent;

class ConversationTransformer {

	/**
	 * Conversations
	 *
	 * @var Collection $conversations
	 */
	private $conversations;

	/**
	 * Agent who get the conversation
	 *
	 * @var WP_User $agent
	 */
	private $agent;

	public function __construct( Collection $conversations, WP_User $agent ) {
		$this->conversations = $conversations;
		$this->agent         = $agent;
	}

	public function transform() {
		if ( $this->conversations->count() === 0 ) {
			return $this->conversations;
		}
		$conversation_ids = $this->conversations->pluck( 'id' )->toArray();

		$users = $this->get_users();

		$participants = ( new Participant( 0 ) )->getStatuses(
			array(
				'conversation_id' => $conversation_ids,
			)
		);

		$logged_in_user_participant_ids = $this->filter_current_user_participant_ids( $participants );

		$agents = $this->get_agents( $participants );

		$participants = $participants->groupBy(
			function ( $participant ) {
				return $participant->conversation_id;
			}
		);

		$unreadCounts = ( new Message( 0 ) )->getAllUnreadCount(
			$logged_in_user_participant_ids->toArray(),
			$conversation_ids
		);

		$recent_messages = $this->get_recent_messages( $conversation_ids );

		$this->conversations->transform(
			function ( $conversation ) use ( $users, $participants, $unreadCounts, $logged_in_user_participant_ids, $agents, $recent_messages ) {
				$conversation['user'] = $users->get( $conversation['chat_user_id'] );
				if ( isset( $conversation['meta'] ) && is_string( $conversation['meta'] ) ) {
					$conversation['meta'] = json_decode( $conversation['meta'], true );

					if ( isset( $conversation['meta']['session']['ended_at'] ) ) {
						$conversation['meta']['session']['ended_at'] = $this->get_date_from_gmt( $conversation['meta']['session']['ended_at'] );
					} else {
						$conversation['meta']['session']['ended_at'] = $this->get_date_from_gmt( $conversation['updated_at'] );
					}
				}

				$conversation['last_message'] = false;
				if ( $recent_messages->has( $conversation['id'] ) ) {
					$conversation['last_message'] = $recent_messages->get( $conversation['id'] )->id;
				}

				if ( $conversation['user'] ) {
					$conversation['user'] = $conversation['user']->toArray();

					if ( 0 === intval( $conversation['user']['user_id'] ) && isset( $conversation['meta']['pre_chat_form']['name'] ) ) {
						$conversation['user']['meta']['name'] = $conversation['meta']['pre_chat_form']['name']['value'];
					}
				}

				if ( $participants->has( $conversation['id'] ) ) {
					$conversation['participants'] = $participants->get( $conversation['id'] );

					$invited_participant     = $conversation['participants']->filter(
						function ( Participant $participant ) {
						return Participant::TYPE_AGENT === $participant->type && intval( $participant->user_id ) === $this->agent->ID && intval( $participant->status_id ) === Participant::STATUS_INVITE;
						}
					)->first();
					$conversation['invited'] = $invited_participant ? true : false;

					$last_agent = $conversation['participants']->filter(
						function ( $participant ) {
							return ( ( Participant::TYPE_AGENT === $participant->type ) && ( '2' === $participant->status_id ) );
						}
					)->last();

					if ( $last_agent ) {
						$conversation['agent'] = $agents->get( $last_agent->user_id );

						if ( $invited_participant ) {
						$invited_by = $invited_participant ? $agents->get( $invited_participant->invited_by ) : false;
							if ( $conversation['invited'] && $invited_by ) {
								/* translators: %s name of the agent who invited */
								$conversation['invited_msg'] = sprintf( __( '%s have added you to the chat', 'wschat' ), $invited_by['display_name'] );
								$conversation['invited_by']  = $invited_participant->toArray();
							}
						}
					}

					$conversation['is_user_online'] = $conversation['participants']->filter(
						function ( $participant ) {
						return Participant::TYPE_AGENT !== $participant->type && $participant->isOnline();
						}
					)->count() > 0;

					$conversation['is_agent_online'] = $conversation['participants']->filter(
						function ( $participant ) {
						return Participant::TYPE_AGENT === $participant->type && $participant->isOnline();
						}
					)->count() > 0;
				}

				if ( $unreadCounts->has( $conversation['id'] ) ) {
					$conversation['unread_count'] = $unreadCounts->get( $conversation['id'] )
												  ->get( $logged_in_user_participant_ids->get( $conversation['id'] ) );
				}

				return $conversation;
			}
		);

		return $this->conversations;
	}

	public function get_agents( $participants ) {
		$agent_participants = $participants->filter(
			function ( $participant ) {
				return Participant::TYPE_AGENT === $participant->type;
			}
		)->map(
			function ( $participant ) {
					return $participant->user_id;
			}
		);

		$invited_agents = $participants->filter(
			function ( $participant ) {
			return $participant->invited_by;
			}
		)->map(
			function ( $participant ) {
				return $participant->invited_by;
			}
		);

		$agent_participants = $agent_participants->merge( $invited_agents );

		$agents = new WP_User_Query(
			array(
				'include' => $agent_participants->toArray(),
				'fields'  => array( 'ID', 'display_name' ),
			)
		);

		return collect( $agents->get_results() )->keyBy(
			function ( $agent ) {
			return $agent->ID;
			}
		)->map(
			function ( $agent ) {
			return array(
				'id'           => $agent->ID,
				'ID'           => $agent->ID,
				'avatar'       => get_avatar_url( $agent->ID ),
				'display_name' => $agent->display_name,
				'avatar'       => get_avatar_url( $agent->ID ),
				'status'       => WSAgent::get_agent_online_status( $agent->ID ),
			);
			}
		);
	}

	public function get_users() {
		$filters = array();

		$filters['id'] = $this->conversations->pluck( 'chat_user_id' )->toArray();

		$users = ( new User() )->get( $filters );

		$users = $users->keyBy(
			function ( $user ) {
				return $user->id;
			}
		);

		return $users;
	}

	public function filter_current_user_participant_ids( $participants ) {
		$logged_in_user_participant_ids = $participants->filter(
			function ( $participant ) {
				return get_current_user_id() === (int) $participant->user_id && Participant::TYPE_AGENT === $participant->type;
			}
		)->map(
			function ( $participant ) {
				return [
					'conversation_id' => $participant->conversation_id,
					'participant_id'  => $participant->id,
				];
			}
		)->pluck( 'participant_id', 'conversation_id' );

		return $logged_in_user_participant_ids;
	}

	public function get_recent_messages( $conversation_ids ) {
		$recentMessages = ( new Message( 0 ) )->getRecentMessage(
			array(
				'conversation_id' => $conversation_ids,
			)
		)->keyBy(
			function ( $message ) {
				return $message->conversation_id;
			}
		);
		return $recentMessages;
	}

	public function get_date_from_gmt( $timestamp ) {
		$dt = date_create( get_date_from_gmt( $timestamp ) );

		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $dt->getTimestamp(), true );
	}
}
