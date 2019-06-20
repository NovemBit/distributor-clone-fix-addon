<?php
/**
 * Plugin Name:       Distributor { Add-on name } Add-on
 * Description:       { Add-on description }
 * Version:           1.0.0
 * Author:            Novembit
 * Author URI:        https://novembit.com
 * License:           GPLv3 or later
 * Domain Path:       /lang/
 * GitHub Plugin URI: { Add-on repository URI }
 * Text Domain:       distributor-{ Add-on prefix }
 *
 * @package distributor-{ Add-on slug }
 */

/* Bail out if the "parent" plug-in insn't active */
require_once ABSPATH . '/wp-admin/includes/plugin.php';
if ( ! is_plugin_active( 'distributor/distributor.php' ) ) {
	return;
}
require_once plugin_dir_path( __FILE__ ) . 'manager.php';
