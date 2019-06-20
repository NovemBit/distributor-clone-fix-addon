<?php
/**
 * Repair subscriptions in spoke
 *
 * @package Distributor
 */

namespace Distributor\CloneFixSpoke;

/**
 * Setup actions
 */
function setup() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_routes' );
}

/**
 * Register REST routes
 */
function register_routes() {
	register_rest_route(
		'wp/v2',
		'/distributor/repair-clone',
		array(
			'methods'  => 'POST',

			'callback' => __NAMESPACE__ . '\repair_posts',
		)
	);
}

/**
 * Try to repair posts
 *
 * @param array $data Array of post data
 */
function repair_posts( $data ) {
	$posts    = $data->get_params();
	$response = array();
	foreach ( $posts as $post_id ) {
		$spoke_id  = get_post_from_original_id( $post_id );
		$signature = \Distributor\Subscriptions\generate_signature();
		update_post_meta( $spoke_id, 'dt_subscription_signature', $signature );
		$response[ $post_id ] = array(
			'remote_id' => $spoke_id,
			'signature' => $signature,
		);
	}
	return $response;
}


/**
 * Get post in destination using original post id
 *
 * @param int $original_id Original post id.
 * @return null|int
 */
function get_post_from_original_id( $original_id ) {
	global $wpdb;
	return $wpdb->get_var( "SELECT post_id from $wpdb->postmeta WHERE meta_key = 'dt_original_post_id' AND meta_value = '$original_id'" ); //phpcs:ignore
}
