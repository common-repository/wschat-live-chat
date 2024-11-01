<?php

namespace WSChat\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\Os;
use WP_User;
use WSChat\Migrate;

class User implements Arrayable {

	const SESSION_KEY_COOKIE_NAME = 'wschat_sesion_key';

	const ROLE_ADMIN = 'administrator';
	const ROLE_AGENT = 'wschat_agent';

	protected $attributes = [];

	public function __construct( $attributes = array() ) {
		$this->parseUserData( $attributes );
	}

	public static function getByUserId( $user_id ) {
		$self = new self();

		$user = $self->first(
			array(
				'user_id' => $user_id,
			)
		);

		return $user;
	}

	public static function getBySessionKey( $session_key ) {
		$self = new self();

		$self = $self->first(
			array(
				'session_key' => $session_key,
				'user_id'     => null,
			)
		);

		return $self;
	}

	public function get( $filters = array() ) {
		$users = $this->applyFilters( $filters )->get();
		$users = collect( $users )->transform(
			function ( $user ) {
				return new self( $user );
			}
		);

		return $users;
	}

	public function find( $filters = array() ) {
		$user = $this->applyFilters( $filters )->first();

		if ( null === $user ) {
			return false;
		}

		$this->parseUserData( $user );

		return $this;
	}

	public function findById( $id ) {
		return $this->find(
			array(
				'id' => [ $id ],
			)
		);
	}

	public function applyFilters( $filters = array() ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_USERS );

		if ( isset( $filters['id'] ) ) {
			$query->whereIn( 'id', $filters['id'] );
		}

		return $query;
	}

	public static function create( $data = [] ) {
		$self = new self();

		if ( ! isset( $data['user_id'] ) && is_user_logged_in() ) {
			$data['user_id'] = get_current_user_id();
		} else {
			$data['user_id'] = null;
		}

		$data['session_key'] = \Illuminate\Support\Str::random();

		if ( ! isset( $data['meta'] ) ) {
			$data['meta'] = array(
				'name' => __( 'Guest', 'wschat' ),
			);

			if ( is_user_logged_in() ) {
				$data['meta'] = array(
					'name' => wp_get_current_user()->display_name,
				);
			}
		}

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$data['meta']['ua'] = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] );
		}

		$data['meta']['browser'] = ( new Browser() )->getName();
		$data['meta']['device']  = ( new Device() )->getName();
		$data['meta']['os']      = ( new Os() )->getName();

		$data['meta'] = wp_json_encode( $data['meta'] );

		$data['created_at'] = Carbon::now()->toJSON();
		$data['updated_at'] = Carbon::now()->toJSON();

		$data['id'] = wpFluent()->table( Migrate::TABLE_CHAT_USERS )->insert( $data );

		return $self->parseUserData( $data );
	}

	public function updateMeta( $meta = array() ) {
		$data = $this->attributes;

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$data['meta']['ua'] = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] );
		}

		$data['meta']['browser'] = ( new Browser() )->getName();
		$data['meta']['device']  = ( new Device() )->getName();
		$data['meta']['os']      = ( new Os() )->getName();

		if ( $this->user_id && false === $this->isGuest() ) {
			$user                   = new WP_User( $this->user_id );
			$data['meta']['name']   = $user->display_name;
			$data['meta']['email']  = $user->user_email;
			$data['meta']['avatar'] = get_avatar_url( $user->ID, array( 'size' => 50 ) );
		} else {
			$data['meta']['avatar'] = get_avatar_url(
				'-',
				array(
					'size'          => 50,
					'force_default' => true,
				)
			);
			$data['meta']['email']  = '';
		}
		$data['meta']       = array_merge( $meta, $data['meta'] );
		$data['meta']       = wp_json_encode( $data['meta'] );
		$data['updated_at'] = Carbon::now()->toDateTimeString();

		wpFluent()->table( Migrate::TABLE_CHAT_USERS )->where( 'id', $this->id )->update( $data );

		return $this->parseUserData( $data );
	}

	public function update( $data ) {
		$data = array_merge(
			$this->attributes,
			$data
		);

		foreach ( $data as $key => $value ) {
			$data[ $key ] = 'meta' === $key ? wp_json_encode( $value ) : $value;
		};

		wpFluent()->table( Migrate::TABLE_CHAT_USERS )->where( 'id', $this->id )->update( $data );

		return $this->parseUserData( $data );
	}

	public function first( $where = [] ) {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_USERS );

		foreach ( $where as $key => $value ) {
			$query = $query->where( $key, $value );
		}

		$data = $query->first();

		if ( ! $data ) {
			return false;
		}

		return $this->parseUserData( $data );
	}

	public function parseUserData( $data ) {
		if ( false === is_array( $data ) ) {
			$data = json_decode( wp_json_encode( $data ), true );
		}

		$this->attributes = $data;

		$this->attributes['meta'] = [];

		if ( isset( $data['meta'] ) ) {
			$this->attributes['meta'] = json_decode( $data['meta'], true );
		}

		return $this;
	}

	public function delete() {
		$query = wpFluent()->table( Migrate::TABLE_CHAT_USERS )
			->where( 'id', $this->id );
		$query->delete();
	}

	public function __get( $key ) {
		return isset( $this->attributes[ $key ] ) ? $this->attributes[ $key ] : null;
	}

	public function __set( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}

	public function isGuest() {
		return is_null( $this->user_id ) || ! $this->user_id > 0;
	}

	public function type() {
		return $this->isGuest() ? Participant::TYPE_GUEST : Participant::TYPE_USER;
	}

	public function toArray() {
		return $this->attributes;
	}
}
