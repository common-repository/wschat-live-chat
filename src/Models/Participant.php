<?php

namespace WSChat\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use WpFluent\QueryBuilder\QueryBuilderHandler;
use WSChat\Migrate;

class Participant implements Arrayable {

	const TYPE_GUEST = 'guest';

	const TYPE_USER = 'user';

	const TYPE_AGENT = 'agent';

	const STATUS_INVITE = 1;

	const STATUS_JOINED = 2;

	const STATUS_DECLINED = 3;

	const ONLINE_DIFF_SECONDS = 60 * 15;

	protected $attributes = [];

	protected $conversation_id;

	public function __construct( $conversation_id ) {
		$this->conversation_id = $conversation_id;
	}

	/**
	 * Add a new particpant to the conversation
	 *
	 * @param User|WP_User $user
	 */
	public function add( $user, $invited_by = null ) {
		$data['conversation_id'] = $this->conversation_id;

		if ( $user instanceof User ) {
			$data['type']    = $user->isGuest() ? self::TYPE_GUEST : self::TYPE_USER;
			$data['user_id'] = $user->id;
		} else {
			$data['user_id'] = $user->ID;
			$data['type']    = self::TYPE_AGENT;
		}

		$data['invited_by'] = $invited_by;
		$data['status_id']  = $invited_by ? self::STATUS_INVITE : self::STATUS_JOINED;

		$existing_user = $this->findByUser( $user );

		if ( $existing_user ) {
			return $existing_user;  
		}

		$data['last_active_at'] = current_time( 'mysql' );

		$data['id'] = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )->insert( $data );

		return $this->parseData( $data );
	}

	public function accept() {
		wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )->where( 'id', $this->id )
												->update(
													array(
														'status_id' => self::STATUS_JOINED,
													)
												);

	}

	public function delete() {
		wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS )
			->where( 'participant_id', $this->id )
			->where( 'conversation_id', $this->conversation_id )
			->delete();
		wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES )->where( 'participant_id', $this->id )
			->where( 'conversation_id', $this->conversation_id )
			->delete();
		wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )->where( 'id', $this->id )
			->where( 'conversation_id', $this->conversation_id )
			->delete();
	}

	public function get( $filters = array(), $columns = array() ) {
		$particpants = $this->applyFilters( $filters )->get();

		$particpants = array_map(
			function ( $particpant ) {
				return json_decode( wp_json_encode( $particpant ), true );
			},
			$particpants
		);

		return $particpants;
	}

	/**
	 * Apply filters to the query builder
	 *
	 * @param array $filters
	 *
	 * @return QueryBuilderHandler
	 */
	public function applyFilters( $filters ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS );

		if ( isset( $filters['type'] ) ) {
			$query->where( 'type', '=', $filters['type'] );
		}

		if ( isset( $filters['conversation_id'] ) ) {
			if ( is_array( $filters['conversation_id'] ) ) {
				$query->whereIn( 'conversation_id', $filters['conversation_id'] );
			} else {
				$query->where( 'conversation_id', '=', $filters['conversation_id'] );
			}
		}

		return $query;
	}

	public function getStatuses( $filters ) {
		$query = $this->applyFilters( $filters );

		return collect( $query->get() )->map(
			function ( $participant ) {
				return ( new Participant( $participant->conversation_id ) )
					->parseData( $participant );
			}
		);
	}

	/**
	 * Make the participant user online
	 *
	 * @param User|WP_User $user
	 */
	public function online( $user = false ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS );
		$where = array();

		if ( $user ) {
			if ( $user instanceof User ) {
				$where['type']    = $user->isGuest() ? self::TYPE_GUEST : self::TYPE_USER;
				$where['user_id'] = $user->id;
			} else {
				$where['user_id'] = $user->ID;
				$where['type']    = self::TYPE_AGENT;
			}
		} else {
			$where['type']    = $this->type;
			$where['user_id'] = $this->user_id;
		}

		foreach ( $where as $key => $value ) {
			$query = $query->where( $key, '=', $value );
		}

		$query->update( [ 'last_active_at' => current_time( 'mysql' ) ] );
	}

	public function find( $where = array() ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_PARTICIPANTS )
					->where( 'conversation_id', '=', $this->conversation_id );

		foreach ( $where as $key => $value ) {
			$query = $query->where( $key, '=', $value );
		}

		$data = $query->first();

		if ( ! $data ) {
			return false;
		}

		return $this->parseData( $data );
	}

	/**
	 * Find Participant by User|WP_User object in the conversation
	 *
	 * @param User|WP_User $user
	 *
	 * @return self|false
	 */
	public function findByUser( $user ) {
		$where['conversation_id'] = $this->conversation_id;

		if ( $user instanceof User ) {
			$where['type']    = $user->isGuest() ? self::TYPE_GUEST : self::TYPE_USER;
			$where['user_id'] = $user->id;
		} else {
			$where['user_id'] = $user->ID;
			$where['type']    = self::TYPE_AGENT;
		}

		return $this->find( $where );
	}

	public function parseData( $data ) {
		if ( false === is_array( $data ) ) {
			$data = json_decode( wp_json_encode( $data ), true );
		}
		$this->attributes = $data;

		return $this;
	}

	public function __get( $key ) {
		return isset( $this->attributes[ $key ] ) ? $this->attributes[ $key ] : null;
	}

	public function isOnline() {
		return Carbon::parse( current_time( 'mysql' ) )->diffInSeconds( Carbon::parse( $this->last_active_at ) ) <= self::ONLINE_DIFF_SECONDS;
	}

	public function isAgent() {
		return self::TYPE_AGENT === $this->type;
	}

	public function status() {
		return $this->isOnline() ?
			__( 'Online', 'wschat' ) :
			__( 'Offline', 'wschat' );
	}

	public function toArray() {
		return $this->attributes;
	}
}
