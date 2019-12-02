<?php
/**
 * Repair subscriptions in spoke
 *
 * @package distributor-clone-fix
 */

namespace DT\NbAddon\CloneFix\Spoke;

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
		[
			'methods'  => 'POST',
			'callback' => __NAMESPACE__ . '\repair_posts',
		]
	);
}

/**
 * Try to repair posts
 *
 * @param array $data Array of post data
 */
function repair_posts( $data ) {
	$posts    = $data->get_params();
	$response = [];
	foreach ( $posts as $original_post_id ) {
		$post_id = \Distributor\Utils\get_post_id_from_original_id( $original_post_id );
		if ( ! empty( $post_id ) ) {
			$signature = \Distributor\Subscriptions\generate_signature();
			update_post_meta( $post_id, 'dt_subscription_signature', $signature );
			$response[ $original_post_id ] = [
				'remote_id' => $post_id,
				'signature' => $signature,
			];
		} else {
			$response[ $original_post_id ] = [
				'error'   => true,
				'message' => 'Post does not exist in destination',
			];
		}
	}
	return $response;
}
