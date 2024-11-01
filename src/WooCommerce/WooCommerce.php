<?php

namespace WSChat\WooCommerce;

use WSChat\Models\Conversation;
use WSChat\Utils;
use function add_query_arg;

class WooCommerce {
	public static function init() {
		add_action( 'wp_ajax_wschat_admin_wc_get_orders_summary', array( self::class, 'get_orders_info' ) );
		add_action( 'wp_ajax_wschat_admin_search_posts', array( self::class, 'get_products' ) );
	}

	public static function get_orders_info() {
		if ( self::is_wc_installed() === false ) {
			Utils::abort( array( 'message' => __( 'Woocommerce is not installed' ) ) , 404 );
		}

		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , true );

		$conversation_id = isset( $_REQUEST['conversation_id'] ) ? sanitize_text_field( $_REQUEST['conversation_id'] ) : '';
		$conversation    = ( new Conversation() )->findById( $conversation_id );

		if ( ! $conversation ) {
			Utils::abort( $_REQUEST , 404 );
		}

		$user = $conversation->chat_user();

		$data['orders']  = array();
		$data['summary'] = array();

		if ( intval( $user->user_id ) ) {
			$data['orders']  = self::get_recent_orders( $user );
			$data['summary'] = self::get_orders_summary( $user );
		}

		/**
		 * Apply a filter hook to get all the orders link
		 *
		 * @since 2.0.0
		 *
		 */
		$data['all_orders_link'] = apply_filters( 'wschat_wc_all_orders_link', add_query_arg( array( 'post_type' => 'shop_order' ), admin_url( 'edit.php' ) ) );

		wp_send_json_success( $data );
	}

	public static function is_wc_installed() {
		$plugin_path = 'woocommerce/woocommerce.php';

		return is_plugin_active( $plugin_path );
	}

	public static function get_recent_orders( $user ) {
		$args = array(
			'customer_id' => $user->user_id,
			'limit'       => 3,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		);

		$orders = wc_get_orders( $args );

		$orders = array_map(
			function ( $order ) {
				$products = array();

				foreach ( $order->get_items() as $item ) {
					$products[] = array(
						'id'        => $item->get_product_id(),
						'name'      => $item->get_product()->get_name(),
						'thumbnail' => self::get_product_thumbnail( $item->get_product() ),
					);
				}

				return array(
					'id'              => $order->get_id(),
					'date'            => $order->get_date_created()->format( get_option( 'date_format' ) ),
					'currency'        => $order->get_currency(),
					'currency_symbol' => get_woocommerce_currency_symbol( $order->get_currency() ),
					'total'           => $order->get_total(),
					'items_count'     => count( $order->get_items() ),
					'products'        => $products,
					'status'          => $order->get_status(),
					'link'            => $order->get_edit_order_url(),
				);
			},
			$orders
		);

		return $orders;
	}

	public static function get_orders_summary( $user ) {
		$args = array(
			'customer_id' => $user->user_id,
			'return'      => 'ids',
			'limit'       => -1,
		);

		$summary['total_orders'] = count( wc_get_orders( $args ) );

		/**
		 * Apply a filter hook to get all the statuses to consider as cancelled_orders
		 *
		 * @param array array of wc statuses
		 *
		 * @since 2.0.0
		 *
		 */
		$args['status']              = apply_filters( 'wschat_wc_cancelled_statuses', array( 'wc-cancelled', 'wc-failed', 'wc-refunded' ) );
		$summary['cancelled_orders'] = count( wc_get_orders( $args ) );

		/**
		 * Apply a filter hook to get all the statuses to consider as completed_orders
		 *
		 * @param array array of wc statuses
		 *
		 * @since 2.0.0
		 *
		 */
		$args['status']              = apply_filters( 'wschat_wc_completed_statuses', array( 'wc-completed' ) );
		$summary['completed_orders'] = count( wc_get_orders( $args ) );

		$summary['total_revenue'] = 0;
		unset( $args['return'] );

		foreach ( wc_get_orders( $args ) as $order ) {
			$summary['total_revenue'] += $order->get_total();
		}

		$summary['currency']        = get_woocommerce_currency();
		$summary['currency_symbol'] = get_woocommerce_currency_symbol( get_woocommerce_currency() );

		return $summary;
	}

	public static function get_product_thumbnail( $product ) {
		$thumbnail = wc_placeholder_img_src();
		$image_id  = $product->get_image_id();

		if ( ! $image_id ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			if ( $parent_product ) {
				$image_id = $parent_product->get_image_id();
			}
		}

		if ( $image_id ) {
			$thumbnail = wp_get_attachment_image_url( $image_id );
		}

		return $thumbnail;
	}

	public static function get_products() {
		check_ajax_referer( 'wschat-ajax-nonce', 'wschat_ajax_nonce' , true );
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

		$data = array( 'posts' => array() );

		foreach ( $post_types as $post_type ) {
			$post_type_posts          = array();
			$post_type_posts['type']  = $post_type->name;
			$post_type_posts['title'] = $post_type->labels->singular_name;
			$posts                    = get_posts(
				array(
					's'         => $search,
					'post_type' => array( $post_type->name ),
					'limit'     => 3,
				)
			);

			$posts = array_map(
				function ( $post ) {
				$post = array(
					'title'     => $post->post_title,
					'thumbnail' => get_the_post_thumbnail_url( $post, array( 35, 35 ) ),
					'url'       => get_post_permalink( $post ),
				);
				return $post;
				},
				$posts
			);

			$post_type_posts['posts'] = $posts;

			$data['posts'][] = $post_type_posts;
		}

		wp_send_json_success( $data );
	}
}

