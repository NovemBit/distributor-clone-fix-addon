<?php
/**
 * Handle UI
 *
 * @package distributor-clone-fix
 */

namespace DT\NbAddon\CloneFix\Ui;

/**
 * Setup actions
 */
function setup() {
	add_action(
		'init',
		function() {
			add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\add_bulk_script', 10, 1 );
			setup_bulk_fix();
		}
	);
}


/**
 *  Setup filters to show bulk clone fix in ui
 */
function setup_bulk_fix() {
	$list = \Distributor\Utils\distributable_post_types();
	foreach ( $list as $screen ) {
		add_filter( 'bulk_actions-edit-' . $screen, __NAMESPACE__ . '\display_bulk_fix' );

	}
}


/**
 * Add fix to bulk actions
 *
 * @param array $actions Array of bulk actions.
 * @return array
 */
function display_bulk_fix( $actions ) {
	$actions['clone_fix'] = __( 'Fix connection', 'distributor-clone-fix' );
	return $actions;
}

/**
 * Add js scripts to pages
 *
 * @param string $hook Current hook
 */
function add_bulk_script( $hook ) {
	if ( 'edit.php' === $hook ) {
		wp_enqueue_script( 'dt_bulk_clone_fix', plugins_url( 'build/js/bulk.min.js', __DIR__ ), [], CLONE_FIX_VERSION, true );
		wp_enqueue_style( 'dt_bulk_clone_fix-styles', plugins_url( 'build/css/bulk.min.css', __DIR__ ), [], CLONE_FIX_VERSION );
		wp_localize_script(
			'dt_bulk_clone_fix',
			'cloneFixData',
			[
				'connections' => \DT\NbAddon\CloneFix\Utils\get_external_connections(),
				'nonce'       => wp_create_nonce( 'dt-fix-clones' ),
			]
		);
	}
}
