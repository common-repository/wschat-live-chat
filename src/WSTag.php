<?php

namespace WSChat;

use Illuminate\Support\Arr;
use WSChat\Models\Message;
use WSChat\Models\Settings;
use WSChat\Models\Tag;

class WSTag {

	public static function init() {
		add_action( 'wp_ajax_wschat_admin_add_a_tag', array( self::class, 'add_tag' ) );
		add_action( 'wp_ajax_wschat_admin_edit_a_tag', array( self::class, 'edit_tag' ) );
		add_action( 'wp_ajax_wschat_admin_delete_a_tag', array( self::class, 'delete_tag' ) );

		add_action( 'wp_ajax_wschat_admin_tag_a_message', array( self::class, 'tag_a_message' ) );
		add_action( 'wp_ajax_wschat_admin_untag_a_message', array( self::class, 'untag_a_message' ) );
	}

	public static function load_tags_page() {
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			check_admin_referer( 'tags_filter' );
		}

		$data['tags'] = collect( wpFluent()->table( 'wschat_tags' )->get() )->map(
			function ( $tag ) {
				return json_decode( wp_json_encode( $tag ), true );
			}
		)->keyBy( 'id' );

		$default_tag     = $data['tags']->count() ? $data['tags']->first()['id'] : null;
		$data['tag_id']  = isset( $_GET['tag_id'] ) ? sanitize_text_field( $_GET['tag_id'] ) : $default_tag;
		$data['limit']   = isset( $_GET['limit'] ) ? sanitize_text_field( $_GET['limit'] ) : 10;
		$data['page_no'] = isset( $_GET['page_no'] ) ? sanitize_text_field( $_GET['page_no'] ) : 1 ;

		$data['created_at']        = array();
		$data['date_period']       = '';
		$data['default_tag_color'] = self::get_default_tag_color();

		if ( isset( $_POST['created_at']['min'] ) && ! empty( $_POST['created_at']['min'] ) ) {
			$data['created_at']['min'] = get_gmt_from_date( sanitize_text_field( $_POST['created_at']['min'] ) );
		}

		if ( isset( $_POST['created_at']['max'] ) ) {
			$max_date = sanitize_text_field( $_POST['created_at']['max'] );
			if ( '' !== $max_date ) {
				$data['created_at']['max'] = get_gmt_from_date( date_create( $max_date )->modify( '1 day' )->format( 'Y-m-d' ) );
			}
		}

		if ( isset( $_POST['date_period'] ) ) {
			$data['date_period'] = sanitize_text_field( $_POST['date_period'] );
		}

		$filters = array(
			'tag'        => $data['tag_id'],
			'created_at' => array_filter( $data['created_at'] ),
			'limit'      => $data['limit'],
			'page_no'    => $data['page_no'],
		);

		$data['messages'] = array();
		$data['total']    = 0;

		if ( $data['tag_id'] ) {
			$data['messages'] = array_map(
				function ( $message ) {
					return ( new Message() )->parseData( $message )->toArray();
				},
				( new Message() )->get( $filters )
			);
			$data['total']    = ( new Message() )->count( $filters );
		}

		$data['total_pages'] = (int) ceil( $data['total'] / $data['limit'] );

		if ( isset( $_POST['created_at']['max'] ) ) {
			$data['created_at']['max'] = sanitize_text_field( $_POST['created_at']['max'] );
		}

		if ( isset( $max_date ) ) {
			$data['max_date'] = $max_date;
		}

		Utils::include_file( '/resources/views/admin/tags.php', $data );
	}

	public static function add_tag() {
		if ( isset( $_POST['wschat_ajax_nonce'] ) ) {
			check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		} else {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}
		}

		$data['name']  = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$data['color'] = isset( $_POST['color'] ) ? sanitize_text_field( $_POST['color'] ) : self::get_default_tag_color();

		if ( empty( $data['name'] ) || empty( $data['color'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Fields seems to be empty' ),
				),
				422
			);
			die;
		}

		$existing = wpFluent()->table( Migrate::TABLE_TAGS )->where( 'name', $data['name'] )->select( 'id' )->first();

		Utils::abort_if(
			$existing,
			array(
				'message' => __( 'Tag name was exisits already' ),
			),
			422
		);

		$data['id'] = wpFluent()->table( Migrate::TABLE_TAGS )->insert( $data );

		wp_send_json_success(
			array(
				'message' => __( 'Tag has been added' ),
				'tag'     => $data,
			)
		);
	}

	public static function edit_tag() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		$id            = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$data['name']  = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$data['color'] = isset( $_POST['color'] ) ? sanitize_text_field( $_POST['color'] ) : '';

		$existing = wpFluent()->table( Migrate::TABLE_TAGS )
						->where( 'name', $data['name'] )
						->where( 'id', '<>', $id )
						->select( 'id' )->first();

		Utils::abort_if(
			$existing,
			array(
				'message' => __( 'Tag name was exisits already' ),
			),
			422
		);

		wpFluent()->table( Migrate::TABLE_TAGS )->where( 'id', $id )->update( $data );

		wp_send_json_success(
			array(
				'message' => __( 'Tag has been updated' ),
			)
		);
	}

	public static function delete_tag() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		Utils::abort_unless( isset( $_POST['id'] ), array( 'message' => __( 'Please specify the tag id' ) ) );

		$id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';

		wpFluent()->table( Migrate::TABLE_TAGS )->where( 'id', $id )->delete();

		wp_send_json_success(
			array(
				'message'  => __( 'Tag has been deleted' ),
				'redirect' => add_query_arg( array( 'page' => 'wschat_tags' ), admin_url( 'admin.php' ) ),
			)
		);
	}

	public static function get_tags() {
		check_ajax_referer();
		$filters = array();

		if ( isset( $_REQUEST['query'] ) ) {
			$filters['query'] = sanitize_text_field( $_REQUEST['query'] );
		}

		if ( isset( $_REQUEST['limit'] ) ) {
			$filters['limit'] = sanitize_text_field( $_REQUEST['limit'] );
		}

		if ( isset( $_REQUEST['page_no'] ) ) {
			$filters['page_no'] = sanitize_text_field( $_REQUEST['page_no'] );
		}

		$data['tags'] = ( new Tag() )->get( $filters );

		wp_send_json_success( $data );
	}

	public static function tag_a_message() {
		if ( isset( $_REQUEST['wschat_ajax_nonce'] ) ) {
			check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		} else {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}
		}

		if ( ! isset( $_POST['message_id'] ) || ! isset( $_POST['tag_id'] ) ) {
			Utils::abort( array( 'message' => __( 'Invalid request' ) ), 422 );
		}

		$message_id = sanitize_text_field( $_POST['message_id'] );
		$tag_id     = sanitize_text_field( $_POST['tag_id'] );

		$message = Message::find( $message_id );
		$data    = $message->toArray();

		$data['body']['tag'] = intval( $tag_id );

		$message->update( $message_id, Arr::only( $data, 'body' ) );

		wp_send_json_success(
			array(
				'message' => __( 'Message has been tagged', 'wschat' ),
			)
		);
	}

	public static function untag_a_message() {
		if ( isset( $_REQUEST['wschat_ajax_nonce'] ) ) {
			check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' );
		} else {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}
		}

		if ( ! isset( $_POST['message_id'] ) ) {
			Utils::abort( array( 'message' => __( 'Invalid request' ) ), 422 );
		}

		$message_id = sanitize_text_field( $_POST['message_id'] );
		$message    = Message::find( $message_id );
		$data       = $message->toArray();

		unset( $data['body']['tag'] );

		$message->update( $message_id, Arr::only( $data, 'body' ) );

		wp_send_json_success(
			array(
				'message' => __( 'Message has been untagged', 'wschat' ),
			)
		);
	}

	public static function get_messages() {
		check_ajax_referer();
		Utils::abort_unless_agent();

		if ( ! isset( $_POST['tag_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'You must specify tag id' ) ) );
			die;
		}

		$filters['tag_id']  = sanitize_text_field( $_POST['tag_id'] );
		$filters['limit']   = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 10;
		$filters['page_no'] = isset( $_POST['page_no'] ) ? sanitize_text_field( $_POST['page_no'] ) : 1;

		$filters['created_at'] = array();

		if ( isset( $_POST['created_at']['min'] ) ) {
			$filters['created_at']['min'] = sanitize_text_field( $_POST['created_at']['min'] );
		}

		if ( isset( $_POST['created_at']['max'] ) ) {
			$filters['created_at']['max'] = sanitize_text_field( $_POST['created_at']['max'] );
		}

		$filters['created_at'] = array_filter( $filters['created_at'] );

		$data['messages']    = ( new Tag() )->get_messages( $filters );
		$data['total']       = ( new Message() )->count( $filters );
		$data['total_pages'] = (int) ceil( $data['total'] / $data['limit'] );

		wp_send_json_success( $data );
	}

	public static function get_default_tag_color() {
		// TODO: Get the default color from settings
		$settings = Settings::load();
		/**
		* Fire a filter hook for default tag colour
		*
		* @since 2.0.0
		*/
		return apply_filters( 'wschat_default_tag_color', $settings->get( 'default_tag_color', '333333' ) );
	}
}

