<?php
/**
 * Plugin Name:       Distributor Clone Fix Add-on
 * Description:       Distributor Clone Fix add-on is for extending the Distributor Plugin functionality to fix subscriptions if spoke was cloned
 * Version:           1.0.0
 * Author:            Novembit
 * Author URI:        https://novembit.com
 * License:           GPLv3 or later
 * Domain Path:       /lang/
 * GitHub Plugin URI: git@github.com:NovemBit/distributor-clone-fix-addon.git
 * Text Domain:       distributor-clone-fix
 *
 * @package distributor-clone-fix
 */

/**
 * Bootstrap function
 */
function dt_clone_fix_add_on_bootstrap() {
	if ( ! function_exists( '\Distributor\ExternalConnectionCPT\setup' ) ) {
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function() {
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), esc_html( 'You need to have Distributor plug-in activated to run the {Add-on name}.', 'distributor-acf' ) );
				}
			);
		}
		return;
	}
	define( 'CLONE_FIX_VERSION', '1.0.0' );
	require_once plugin_dir_path( __FILE__ ) . 'manager.php';
}
add_action( 'plugins_loaded', 'dt_clone_fix_add_on_bootstrap' );
