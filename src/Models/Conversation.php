<?php

namespace WSChat\Models;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use WSChat\Migrate;
use WSChat\WSAgent;

class Conversation {
	protected $attributes = [];

	public static function find_by_chat_user_id( $chat_user_id ) {
		$self = new self();

		$conversation = $self->find(
			array(
				'chat_user_id' => $chat_user_id,
			)
		);

		return $conversation;
	}
	public function get( $filters = array() ) {
		$conversations = $this->applyFilters( $filters );
		$conversations = $conversations->get();
		$conversations = array_map(
			function ( $conversation ) {
				return json_decode( wp_json_encode( $conversation ), true );
			},
			$conversations
		);

		return $conversations;
	}

	public function applyFilters( $filters ) {
		$limit   = Arr::get( $filters, 'limit', 15 );
		$page_no = Arr::get( $filters, 'page_no', 1 );
		$offset  = ( $page_no - 1 ) * $limit;

		$subQuery = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES )->select( Migrate::TABLE_CHAT_MESSAGES . '.conversation_id' );

		$query = wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )
					 ->limit( $limit )
					 ->offset( $offset )
					 ->orderBy( 'updated_at', 'desc' );

		if ( Arr::has( $filters, 'after' ) ) {
			$query->where( 'updated_at', '>', Arr::get( $filters, 'after' ) );
		}

		if ( Arr::has( $filters, 'before' ) ) {
			$query->where( 'updated_at', '<', Arr::get( $filters, 'before' ) );
		}

		if ( Arr::has( $filters, 'search' ) ) {
			$search = Arr::get( $filters, 'search' );
			$query->where(
				function ( $query ) use ( $search ) {
					$usersQuery = wpFluent()->table( Migrate::TABLE_CHAT_USERS )->select( 'id' );
					$usersQuery->where( 'meta', 'like', '%' . $search . '%' );

					$query->orWhere( wpFluent()->raw( 'chat_user_id in (' . $usersQuery->getQuery()->getRawSql() . ')' ) );
				}
			);
		}

		if ( Arr::has( $filters, 'status' ) ) {
			$status = Arr::get( $filters, 'status' );

			if ( 'active' === $status ) {
				$query->where(
					function ( $query ) {
						$query->where( 'updated_at', '>=', Carbon::now()->subSeconds( Participant::ONLINE_DIFF_SECONDS * 2 )->toDateTimeString() );
						$query->Where( 'meta', 'like', '%"is_ended":false%' );
					}
				);
			}

			if ( 'history' === $status ) {
				$query->where(
					function ( $query ) {
					$query->where( 'updated_at', '<', Carbon::now()->subSeconds( Participant::ONLINE_DIFF_SECONDS * 2 )->toDateTimeString() );
					$query->orWhere( 'meta', 'like', '%"is_ended":true%' );
					}
				);
			}
		}

		if ( Arr::has( $filters, 'agent_id' ) ) {
			$agents = Arr::get( $filters, 'agent_id' );
			$agents = is_array( $agents ) ? $agents : array( $agents );

			$agent_query = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )
							->where( 'type', Participant::TYPE_AGENT )
							->whereIn( 'user_id', $agents )
							->select( 'conversation_id' );

			$query->where( wpFluent()->raw( 'id in ( ' . $agent_query->getQuery()->getRawSql() . ' )' ) );
		}

		if ( Arr::has( $filters, 'unassigned' ) ) {
			$agent_query = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )
							->where( 'type', Participant::TYPE_AGENT )
							->select( 'conversation_id' );

			$query->where( wpFluent()->raw( 'id not in ( ' . $agent_query->getQuery()->getRawSql() . ' )' ) );
		}

		if ( Arr::has( $filters, 'has_messages' ) ) {
			$query->where( wpFluent()->raw( 'id in (' . $subQuery->getQuery()->getRawSql() . ')' ) );
		}

		if ( Arr::has( $filters, 'has_no_messages' ) ) {
			$usersQuery = wpFluent()->table( Migrate::TABLE_CHAT_USERS )->select( 'id' );
			$usersQuery->where( 'updated_at', '>=', Carbon::now()->subSeconds( Participant::ONLINE_DIFF_SECONDS * ( 2 / 3 ) )->toDateTimeString() );

			$query->where( wpFluent()->raw( 'chat_user_id in (' . $usersQuery->getQuery()->getRawSql() . ')' ) );

			$subQuery->join(
				Migrate::TABLE_CHAT_PARTICIPANTS,
				Migrate::TABLE_CHAT_PARTICIPANTS . '.id',
				'=',
				Migrate::TABLE_CHAT_MESSAGES . '.participant_id'
			);

			$query->where( wpFluent()->raw( 'id not in (' . $subQuery->getQuery()->getRawSql() . ')' ) );
		}

		return $query;
	}

	public static function create( $chat_user_id, $meta = [] ) {
		if ( 0 === intval( $chat_user_id ) ) {
			return false;
		}

		$self = new self();

		$data = [];

		$data['chat_user_id'] = $chat_user_id;

		$data['created_at'] = current_time( 'mysql' );
		$data['updated_at'] = current_time( 'mysql' );

		$data['meta'] = $meta;

		/**
		 * Fire a filter hook before create a conversaion
		 *
		 * @param array $data
		 * @since 2.0.0
		 */
		$data = apply_filters( 'wschat_conversation_before_create', $data );

		$data['meta'] = wp_json_encode( $meta );

		$data['id'] = wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )->insert( $data );

		$self->parseData( $data );

		/**
		 * Fire a filter hook after create a conversation
		 *
		 * @param Conversation $conversation
		 * @since 2.0.0
		 */
		$self = apply_filters( 'wschat_conversation_after_create', $self );

		return $self;
	}

	public function save() {
		$data = $this->attributes;

		$data['meta'] = wp_json_encode( $data['meta'] );

		/**
		 * Fire a filter hook before saving a conversation
		 *
		 * @param $data
		 * @since 2.0.0
		 */
		$data = apply_filters( 'wschat_conversation_before_save', $data, $this );

		wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )->where( 'id', $this->id )->update( $data );
	}

	public function participants() {
		return new Participant( $this->id );
	}

	public function messages() {
		return new Message( $this->id );
	}

	public function chat_user() {
		return ( new User() )->findById( $this->chat_user_id );
	}

	public function findById( $id ) {
		return $this->find(
			array(
				'id' => $id,
			)
		);
	}

	public function find( $where = [] ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS );

		foreach ( $where as $key => $value ) {
			$query = $query->where( $key, '=', $value );
		}

		$data = $query->first();

		if ( ! $data ) {
			return false;
		}

		return $this->parseData( $data );
	}

	public function touch( $id ) {
		wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )
			->where( 'id', '=', $id )
			->update( [ 'updated_at' => current_time( 'mysql', true ) ] );
	}

	public function parseData( $data ) {
		if ( false === is_array( $data ) ) {
			$data = json_decode( wp_json_encode( $data ), true );
		}

		$this->attributes         = $data;
		$this->attributes['meta'] = json_decode( $data['meta'], true );

		return $this;
	}

	public function delete() {
		$this->messages()->delete();
		$participants = $this->participants()->get(
			array(
				'conversation_id' => $this->id,
			)
		);

		foreach ( $participants as $participant ) {
			( new Participant( $this->id ) )->parseData( $participant )->delete();
		}

		wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )
			->where( 'id', '=', $this->id )
			->delete();

		$this->chat_user()->delete();

		return true;
	}

	/**
	 * Add a meta element to a conversation
	 *
	 * @param string $key Meta key
	 * @param mixed $data Meta value
	 */
	public function add_meta( $key, $data ) {
		$meta = Arr::get( $this->attributes, 'meta', array() );

		$meta[ $key ] = $data;

		$this->attributes['meta'] = $meta;

		wpFluent()->table( Migrate::TABLE_CHAT_CONVERSATIONS )
			->where( 'id', '=', $this->id )
			->update(
				[
					'meta'       => wp_json_encode( $meta ),
					'updated_at' => Carbon::now()->toJSON(),
				]
			);
	}

	public function start_session( Participant $participant ) {
		$meta = $this->get_meta();

		$session = Arr::get( $meta, 'session', array() );

		if ( false === $this->is_session_ended() ) {
			return false;
		}

		$session['is_ended']     = false;
		$session['initiated_by'] = $participant->id;
		$session['initiated_at'] = current_time( 'mysql', true );

		$meta['session']          = $session;
		$this->attributes['meta'] = $meta;

		$this->save();

		return true;
	}

	/**
	 * End the conversation session
	 *
	 * @param Participant $participant
	 *
	 * @return bool
	 */
	public function end_session( Participant $participant ) {
		$meta = $this->get_meta();

		$session = Arr::get( $meta, 'session', array() );

		if ( true === $this->is_session_ended() ) {
			return false;
		}

		$session['is_ended'] = true;
		$session['ended_by'] = $participant->id;
		$session['ended_at'] = current_time( 'mysql', true );

		$meta['session']          = $session;
		$this->attributes['meta'] = $meta;

		$this->save();

		return true;
	}

	/**
	 * Check wether the session is ended or not being created
	 *
	 * @return bool
	 */
	public function is_session_ended() {
		if ( Arr::has( $this->attributes, 'meta.session' ) === false ) {
			return true;
		}

		$last_acitive_time = strtotime( $this->updated_at );

		return true === Arr::get( $this->attributes, 'meta.session.is_ended', true ) || ( time() - $last_acitive_time ) > ( Participant::ONLINE_DIFF_SECONDS * 2 );
	}

	public function update_meta( $meta ) {
		$this->attributes['meta'] = $meta;
		$this->save();
	}

	public function get_meta() {
		return Arr::get( $this->attributes, 'meta', array() );
	}

	public function are_agents_online() {
		$participants = $this->participants()->get(
			array(
				'type'            => Participant::TYPE_AGENT,
				'conversation_id' => $this->id,
			)
		);

		if ( count( $participants ) > 0 ) {
			$agents = array_map(
				function ( $participant ) {
						return $participant['user_id'];
				},
				$participants
			);
		} else {
			$agents = WSAgent::get_agents( array( 'fields' => 'ID' ) );
		}

		return WSAgent::is_agents_online( $agents );
	}

	public function __get( $key ) {
		return Arr::get( $this->attributes, $key );
	}

	public function toArray() {
		return $this->attributes;
	}
}
