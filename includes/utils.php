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
