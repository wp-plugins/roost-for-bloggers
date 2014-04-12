<?php
/**
 * Plugin Name: Roost Web Push
 * Plugin URI: http://www.roost.me/
 * Description: Drive traffic to your website with Safari Mavericks push notifications and Roost.
 * Version: 2.0.5
 * Author: Roost.me
 * Author URI: http://roost.me
 * License: GPLv2 or later
 */
	
define( 'ROOST_URL', plugin_dir_url( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'includes/roost-core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/roost-api.php' );

$roost = new Roost();

register_activation_hook( __FILE__, array( 'ROOST', 'init' ) );
register_uninstall_hook( __FILE__, array( 'ROOST', 'uninstall' ) );
