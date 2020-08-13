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
			'methods'             => 'POST',
			'callback'            => __NAMESPACE__ . '\repair_posts',
			'permission_callback' => '__return_true',
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
	foreach ( $posts as $post_id ) {
		$spoke_id = \DT\NbAddon\CloneFix\Utils\get_post_from_original_id( $post_id );
		if ( ! empty( $spoke_id ) ) {
			$signature = \Distributor\Subscriptions\generate_signature();
			update_post_meta( $spoke_id, 'dt_subscription_signature', $signature );
			$response[ $post_id ] = [
				'remote_id' => $spoke_id,
				'signature' => $signature,
			];
		} else {
			$response[ $post_id ] = [
				'error'   => true,
				'message' => 'Post does not exist in destination',
			];
		}
	}
	return $response;
}
