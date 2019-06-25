<?php
/**
 * Helper functions
 *
 * @package distributor-clone-fix
 */

namespace DT\NbAddon\CloneFix\Utils;

/**
 * Get all published external connections
 */
function get_external_connections() {
	$args        = [
		'numberposts' => -1,
		'post_type'   => 'dt_ext_connection',
		'post_status' => 'publish',
	];
	$connections = get_posts( $args );
	$result      = [];
	foreach ( $connections as $connection ) {
		$result[ $connection->post_name ] = [
			'title' => $connection->post_title,
			'id'    => $connection->ID,
		];
	}
	return $result;
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
