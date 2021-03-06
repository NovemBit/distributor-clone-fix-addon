<?php
/**
 * Functions  performed in hub
 *
 * @package distributor-clone-fix
 */

namespace DT\NbAddon\CloneFix\Hub;

/**
 * Setup actions
 */
function setup() {
	add_action(
		'init',
		function() {
			add_action( 'wp_ajax_fix_clones', __NAMESPACE__ . '\ajax_fix' );
			add_filter( 'dt_blacklisted_meta', __NAMESPACE__ . '\blacklist_meta_keys', 10, 1 );
		}
	);
}

/**
 * Handle AJAX clone fixing
 */
function ajax_fix() {
	if ( ! check_ajax_referer( 'dt-fix-clones', 'nonce', false ) ) {
		wp_send_json_error();
		exit;
	}
	if ( empty( $_POST['posts'] ) || empty( $_POST['connection'] ) ) {
		wp_send_json_error();
		exit;
	}
	$posts = explode( ',', $_POST['posts'] );

	$connection_id = $_POST['connection'];
	if (! wp_doing_cron() ) {
		/**
		 * Add possibility to send notification in background
		 *
		 * @param bool      true            Whether to run clone fix in background or not, default 'false'
		 * @param array     $posts          Posts for which need to run 'clone fix'
		 * @param string    $connection_id  The connection id
		 */
		$allow_clone_fix = apply_filters( 'dt_allow_clone_fix', true, $posts, $connection_id );

		if ( false === $allow_clone_fix ) {
			wp_send_json_success(
				array(
					'results' => 'Scheduled a task.',
				)
			);
			return;
		}
	}


	$response = push_post_data( $posts, $connection_id );

	wp_send_json( apply_filters( 'dt_manage_clone_fix_response_hub', $response ) );
	exit;
}

/**
 * Push post data to spoke
 *
 * @param array $posts Array of post ids.
 * @param int   $connection_id Connection id to be fixed.
 *
 * @return array
 */
function push_post_data( $posts, $connection_id ) {
	$hosts = [];
	foreach ( $posts as $post_id ) {
		$hosts[ $connection_id ]['host']    = untrailingslashit( get_post_meta( $connection_id, 'dt_external_connection_url', true ) );
		$hosts[ $connection_id ]['posts'][] = $post_id;
	}

	$external_connection_class = \Distributor\Connections::factory()->get_registered( 'external' )['wp'];
	if ( empty( $hosts ) ) {
		$result = [
			'status' => 'failure',
			'data'   => 'Selected posts have not distributed via selected connection',
		];
	} else {
		$result = [
			'status' => 'success',
			'data'   => [],
		];
		foreach ( $hosts as $connection_id => $host ) {
			$url                      = $host['host'] . '/wp/v2/distributor/repair-clone';
			$external_connection_auth = get_post_meta( $connection_id, 'dt_external_connection_auth', true );
			$auth_handler             = new $external_connection_class::$auth_handler_class( $external_connection_auth );

			//TODO: we need to send and replace post ID as well.
			$response = wp_remote_post(
				$url,
				$auth_handler->format_post_args(
					[

						'timeout' => 60,

						'body'    => $host['posts'],
					]
				)
			);
			
			if ( ! is_wp_error( $response ) ) {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				foreach ( $data as $post_id => $remote_post_data ) {
					// in case if post not found in spoke
					if( isset( $remote_post_data[ 'error' ] ) && $remote_post_data[ 'error' ] == true ) {
						//\Distributor\Logger\log( 'failed', 'clone fix', $connection_id, $post_id, [$remote_post_data[ 'message' ]], '' );
						continue;
					} elseif( empty( $remote_post_data['remote_id'] ) || empty( $remote_post_data['signature'] )) {
						//\Distributor\Logger\log( 'failed', 'clone fix', $connection_id, $post_id, ['Post not found in spoke or non created signature'], '' );
						continue;
					}
					
					/* create or update connection map */
					$connection_map = get_post_meta( $post_id, 'dt_connection_map', true );
					if ( empty( $connection_map ) || empty( $connection_map['external'] ) ) {
						$connection_map = [ 'external' => [] ];
					}
					
					$connection_map['external'][ $connection_id ] = [
						'post_id' => $remote_post_data['remote_id'],
						'time'    => time(),
					];
					
					/* create or update subscription */
					$subscriptions = get_post_meta( $post_id, 'dt_subscriptions', true );
					if ( !empty( $subscriptions ) ) {
						foreach ( $subscriptions as $subscription_key => $subscription_id ) {
							$signature      = get_post_meta( $subscription_id, 'dt_subscription_signature', true );
							$remote_post_id = get_post_meta( $subscription_id, 'dt_subscription_remote_post_id', true );
							$target_url     = get_post_meta( $subscription_id, 'dt_subscription_target_url', true );
							
							if ( empty( $signature ) ) {
								//\Distributor\Logger\log( 'failed', 'clone fix', $connection_id, $post_id, ['Empty internal signature'], '' );
							}

							// compare subscriptions based on target URL, since we don't have connection ID
							if ( untrailingslashit ( $target_url ) == $host['host'] ) {
								
								// if subscription's remote post ID doesn't much with distributed post ID
								if ( $remote_post_id != $remote_post_data['remote_id'] ) {
									//\Distributor\Logger\log( 'failed', 'clone fix', $connection_id, $post_id, [ 'Subscription\'s remote post ID doesn\'t much with distributed post ID' ], '' );
								}
								\Distributor\Subscriptions\delete_subscription($post_id, $signature);
								continue;
							}
						}
					}
					
					
					\Distributor\Subscriptions\create_subscription( $post_id, $remote_post_data['remote_id'], $host['host'], $remote_post_data['signature'] );
					
					
					$result['data'][ $post_id ] = [ 'status' => 'success' ];
					update_post_meta( $post_id, 'dt_connection_map', $connection_map );
					delete_post_meta( $post_id, 'dt_repair_post', $connection_id );
				}
			} else {
				$result['data'][ $post_id ] = [
					'status' => 'failure',
					'info'   => $response->get_error_messages(),
				];
			}
		}
	}
	return $result;
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
		[
			'dt_repair_post',
		]
	);
}
