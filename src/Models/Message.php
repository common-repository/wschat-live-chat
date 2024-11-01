<?php

namespace WSChat\Models;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use WSChat\Migrate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Message implements Arrayable {

	protected $attributes = [];

	protected $conversation_id;

	public function __construct( $conversation_id = 0 ) {
		$this->conversation_id = $conversation_id;
	}

	public function get( $filter = array() ) {
		$page_no = 1;
		$limit   = 25;

		if ( isset( $filter['page_no'] ) ) {
			$page_no = $filter['page_no'];
		}

		if ( isset( $filter['limit'] ) ) {
			$limit = $filter['limit'];
		}

		$query = $this->applyFilter( $filter );

		$query = $query->limit( $limit )
		->offset( ( $page_no - 1 ) * $limit )
		->orderBy( 'id', 'desc' );

		$messages = $query->get();

		return $messages;
	}

	public static function find( $id ) {
		$filter = array( 'id' => $id );

		$self    = new self();
		$message = $self->applyFilter( $filter )->first();

		return $self->parseData( $message );
	}

	public function count( $filter ) {
		return $this->applyFilter( $filter )->count();
	}

	public function unreadCount( $participant ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS )->whereNull( 'seen_at' );

		$query->where( 'participant_id', '=', $participant->id );

		$query->where( 'conversation_id', $this->conversation_id );
		$query->where( 'is_sender', 0 );

		return $query->count();
	}

	public function applyFilter( $filter ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES );
		if ( $this->conversation_id ) {
			$query->where( 'conversation_id', $this->conversation_id );
		}

		if ( isset( $filter['after'] ) && $filter['after'] ) {
			$query->where( 'id', '>', $filter['after'] );
		}

		if ( isset( $filter['before'] ) && $filter['before'] ) {
			$query->where( 'id', '<', $filter['before'] );
		}

		if ( isset( $filter['id'] ) && $filter['id'] ) {
			$query->where( 'id', $filter['id'] );
		}

		if ( isset( $filter['participant_id'] ) ) {
			$query->where( 'participant_id', '=', $filter['participant_id'] );
		}

		if ( isset( $filter['search'] ) ) {
			$query->where( 'body', 'like', '%' . $filter['search'] . '%' );
		}

		if ( isset( $filter['created_at'] ) && is_array( $filter['created_at'] ) && ! empty( $filter['created_at'] ) ) {
			$query->where(
				function ( $query ) use ( $filter ) {
				$min = Carbon::parse( $filter['created_at']['min'] )->tz( 'UTC' )->startOfMinute()->toDateTimeString();
				$max = Carbon::parse( $filter['created_at']['max'] )->tz( 'UTC' )->toDateTimeString();

				$query->where( 'created_at', '>=', $min );
				$query->where( 'created_at', '<=', $max );
				}
			);
		}

		if ( isset( $filter['tag'] ) ) {
			$query->where( wpFluent()->raw( 'json_contains(body, "' . $filter['tag'] . '", "$.tag")' ) );
		}

		$conversation = Arr::get( $filter, 'conversation_id' );

		if ( $conversation ) {
			if ( is_array( $conversation ) ) {
				$query->whereIn( 'conversation_id', $conversation );
			} else {
				$query->where( 'conversation_id', '=', $conversation );
			}
		}

		if ( isset( $filter['limit'] ) ) {
			$query->limit( $filter['limit'] );

			if ( isset( $filter['page_no'] ) ) {
				$query->offset( ( $filter['page_no'] - 1 ) * $filter['limit'] );
			}
		}

		return $query;
	}

	/**
	 * Add a new message to the conversation
	 *
	 * @param Participant $participant
	 * @param array $data
	 *
	 * @return self
	 */
	public function add( $participant, $data ) {
		$message = [
			'conversation_id' => $this->conversation_id,
			'participant_id'  => $participant->id,
			'type'            => $data['type'],
			// TODO: Parse the data based on the type
			'body'            => wp_json_encode( $data['body'] ),
			'created_at'      => current_time( 'mysql', true ),
			'updated_at'      => current_time( 'mysql', true ),
		];

		$message['id'] = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES )->insert( $message );

		$this->parseData( $message );

		$this->notify( $participant );

		( new Conversation() )->touch( $this->conversation_id );

		return $this;
	}

	public function update( $id, $data ) {
		if ( isset( $data['body'] ) ) {
			$data['body'] = wp_json_encode( $data['body'] );
		}

		wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES )->where( 'id', $id )->update( $data );
	}

	public function parseData( $attributes ) {
		if ( false === is_array( $attributes ) ) {
			$attributes = json_decode( wp_json_encode( $attributes ), true );
		}
		$this->attributes = $attributes;

		$this->attributes['id'] = (int) $attributes['id'];
		if ( isset( $attributes['body'] ) ) {
			$this->attributes['body'] = json_decode( $attributes['body'], true );
		}

		return $this;
	}

	public function notify( $participant ) {
		$notifications = array();
		$filter        = array(
			'conversation_id' => $this->conversation_id,
		);

		foreach ( $participant->get( $filter ) as $chat_participant ) {
			$notifications[] = [
				'conversation_id' => $this->conversation_id,
				'message_id'      => $this->id,
				'participant_id'  => $chat_participant['id'],
				'is_sender'       => ( $chat_participant['id'] === $participant->id ),
				'created_at'      => Carbon::now()->toJSON(),
				'updated_at'      => Carbon::now()->toJSON(),
			];
		}

		wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS )->insert( $notifications );
	}

	/**
	 * Read all unread messages
	 *
	 * @param Participant $participant
	 */
	public function readAll( $participant ) {
		$filter = [];

		$filter['conversation_id'] = $this->conversation_id;
		$filter['participant_id']  = $participant->id;

		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS );

		foreach ( $filter as $key => $value ) {
			$query->where( $key, '=', $value );
		}

		$query->whereNull( 'seen_at' );

		$query->delete();
	}

	/**
	 * Get recent messages per conversation_id
	 *
	 * @param array $filters
	 *
	 * @return Collection<self>
	 */
	public function getRecentMessage( $fitlers ) {
		$subQuery = $this->applyFilter( $fitlers )->select( wpFluent()->raw( 'max(id) as id, conversation_id' ) );
		$subQuery->groupBy( 'conversation_id' );

		$query = wpFluent()->table( wpFluent()->subQuery( $subQuery, wpFluent()->addTablePrefix( 'recent_messages' , false ) ) );
		/* $query = wpFluent()->table( wpFluent()->subQuery( $subQuery, wpFluent()->addTablePrefix( 'recent_messages' , false ) ) ) */
		/* 				->join( Migrate::TABLE_CHAT_MESSAGES, Migrate::TABLE_CHAT_MESSAGES . '.id', '=', 'recent_messages.id' ); */

		return collect( $query->get() )->map(
			function ( $message ) {
				return ( new self( $message->conversation_id ) )->parseData( $message );
			}
		);
	}

	/**
	 * Get all the unread count of a participant in a given conversation
	 *
	 * @var array $participants array of participant ids
	 * @var array $conversations array of ids
	 *
	 * @return Collection
	 */
	public function getAllUnreadCount( $participants, $conversations ) {
		if ( 0 === count( $participants ) ) {
			return collect();
		}
		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS );

		$query->whereIn( 'conversation_id', $conversations )
		->whereIn( 'participant_id', $participants )
		->whereNull( 'seen_at' )
		->where( 'is_sender', '=', 0 )
		->groupBy( 'conversation_id' )
		->groupBy( 'participant_id' )
		->select( wpFluent()->raw( 'count(id) as unread_count, conversation_id, participant_id' ) );

		return collect( $query->get() )->groupBy( 'conversation_id' )->map(
			function ( $counts ) {
				return collect( $counts )->pluck( 'unread_count', 'participant_id' );
			}
		);
	}

	/**
	 * Get unread notifications by filter
	 *
	 * @param array $filters filters to be applied
	 *      $filters = [
	 *          'messages' => (array) Array of message ids
	 *          'participants' => (array) Array of participant ids
	 *          'no_sender' => (bool) wether include sender or not
	 *      ]
	 *
	 * @return Collection
	 */
	public function get_unread_notifications( $filters = array() ) {
		$query = self::filter_unread_notifications( $filters );

		return collect( $query->get() );
	}

	public static function filter_unread_notifications( $filters ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS );
		$query->select( 'message_id' );
		$query->whereNull( 'seen_at' );
		$query->where( 'is_sender', 0 );

		if ( Arr::has( $filters, 'messages' ) ) {
			$query->whereIn( 'message_id', $filters['messages'] );
		}

		if ( Arr::has( $filters, 'participants' ) ) {
			if ( count( $filters['participants'] ) ) {
				$query->whereIn( 'participant_id', $filters['participants'] );
			}
		}

		if ( Arr::has( $filters, 'agent_id' ) ) {
			if ( count( $filters['agent_id'] ) ) {
				$query->join( Migrate::TABLE_CHAT_PARTICIPANTS, 'participant_id', '=', Migrate::TABLE_CHAT_PARTICIPANTS . '.id' );
				$query->where( 'type', Participant::TYPE_AGENT );
				$query->whereIn( 'user_id', $filters['agent_id'] );
			}
		}

		return $query;
	}

	public function delete() {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGE_NOTIFICATIONS );
		$query->where( 'conversation_id', $this->conversation_id );
		$query->delete();

		$query = wpFluent()->table( Migrate::TABLE_CHAT_MESSAGES );
		$query->where( 'conversation_id', $this->conversation_id );
		$query->delete();
	}

	public function toArray() {
		return $this->attributes;
	}

	public function __get( $key ) {
		return Arr::get( $this->attributes, $key );
	}

	public function get_text() {
		return Arr::get( $this->attributes, 'body.text' );
	}
}
