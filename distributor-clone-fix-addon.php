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

/* Bail out if the "parent" plug-in insn't active */
require_once ABSPATH . '/wp-admin/includes/plugin.php';
if ( ! is_plugin_active( 'distributor/distributor.php' ) ) {
	return;
}
require_once plugin_dir_path( __FILE__ ) . 'manager.php';
