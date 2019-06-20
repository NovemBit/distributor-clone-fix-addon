<?php
/**
 * Functions  performed in hub
 *
 * @package distributor-clone-fix
 */

namespace Distributor\CloneFixHub;

/**
 * Setup actions
 */
function setup() {
	add_action(
		'init',
		function() {
			add_action( 'dt_repair_posts_hook', __NAMESPACE__ . '\push_post_data' );
			add_filter( 'dt_blacklisted_meta', __NAMESPACE__ . '\blacklist_meta_keys', 10, 1 );
		}
	);
}


/**
 * Push post data to spoke
 */
function push_post_data() {
	global $wpdb;

	$posts = $wpdb->get_col(
		$wpdb->prepare(
			"
	SELECT `post_id` 
		FROM $wpdb->postmeta AS `postmeta`
	INNER JOIN $wpdb->posts AS `posts`
		ON `postmeta`.`post_id`=`posts`.`ID` 
		AND `posts`.`post_status` IN ( 'publish','draft','trash' ) 
	WHERE `postmeta`.`meta_key`=%s
	LIMIT 20
  ",
			'dt_repair_post'
		)
	);

	$hosts = array();
	foreach ( $posts as $post_id ) {
		$connection_id                      = get_post_meta( $post_id, 'dt_repair_post', true );
		$host                               = get_post_meta( $connection_id, 'dt_external_connection_url', true );
		$hosts[ $connection_id ]['host']    = untrailingslashit( $host );
		$hosts[ $connection_id ]['posts'][] = $post_id;
	}

	$external_connection_class = \Distributor\Connections::factory()->get_registered( 'external' )['wp'];
	foreach ( $hosts as $connection_id => $host ) {
		$url                      = $host['host'] . '/wp/v2/distributor/repair-clone';
		$external_connection_auth = get_post_meta( $connection_id, 'dt_external_connection_auth', true );
		$auth_handler             = new $external_connection_class::$auth_handler_class( $external_connection_auth );

		$response = wp_remote_post(
			$url,
			$auth_handler->format_post_args(
				array(

					'timeout' => 60,

					'body'    => $host['posts'],
				)
			)
		);
		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			foreach ( $data as $post_id => $items ) {
				$connection_map = get_post_meta( $post_id, 'dt_connection_map', true );
				if ( empty( $connection_map ) || empty( $connection_map['external'] ) ) {
					$connection_map = array( 'external' => array() );
				}
				if ( ! in_array( $connection_id, array_keys( $connection_map['external'] ), true ) ) {
					$connection_map['external'][ $connection_id ] = array(
						'post_id' => $items['remote_id'],
						'time'    => time(),
					);

					if ( ! empty( $items['remote_id'] ) && ! empty( $items['signature'] ) ) {
						$subscription_id = \Distributor\Subscriptions\create_subscription( $post_id, $items['remote_id'], $host['host'], $items['signature'] );
					}
				}
				update_post_meta( $post_id, 'dt_connection_map', $connection_map );
				delete_post_meta( $post_id, 'dt_repair_post', $connection_id );
			}
		}
	}
}

/**
 * Add meta keys to blacklisted keys array
 *
 * @param array $blacklisted Array of blacklisted keys.
 *
 * @return array
 */
function blacklist_meta_keys( $blacklisted ) {
	return array_merge(
		$blacklisted,
		array(
			'dt_repair_post',
		)
	);
}
