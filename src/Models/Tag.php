<?php
namespace WSChat\Models;

class Tag {
	/**
	 * Filter tags and return matched tags
	 *
	 * @param array $filters
	 *
	 * @return array
	 */
	public function get( $filters ) {
		$query = $this->applyFilters( $filters );

		$limit   = isset( $filters['limit'] ) ? $filters['limit'] : 10;
		$page_no = isset( $filters['page_no'] ) ? $filters['page_no'] : 1;

		$query->offset( ( $page_no - 1 ) * $limit )->limit( $limit );

		return $query->get();
	}

	public function applyFilters( $filters = array() ) {
		$query = wpFluent()->table( 'wschat_tags' );

		if ( isset( $filters['query'] ) ) {
			$query->where( 'name', 'like', '%' . $filters['query'] . '%' );
		}

		return $query;
	}

	/**
	 * Get messages based of a tag
	 *
	 * @param array $filters {
	 *  @type int   $tag_id Tag ID
	 *  @type array created_at Array of date range
	 * }
	 *
	 * @return array
	 */
	public static function get_messages( $filters ) {
		$messages = array_map(
			function ( $message ) {
				return ( new Message() )->parseData( $message )->toArray();
			},
			( new Message() )->get( $filters )
		);

		return $messages;
	}
}
