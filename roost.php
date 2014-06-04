<?php
/**
 * Plugin Name: Roost Web Push
 * Plugin URI: http://www.roost.me/
 * Description: Drive traffic to your website with Safari Mavericks push notifications and Roost.
 * Version: 2.1
 * Author: Roost.me
 * Author URI: http://roost.me
 * License: GPLv2 or later
 */
	
define( 'ROOST_URL', plugin_dir_url( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-roost-core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-roost-api.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-roost-bbpress.php' );

$roost = new Roost();
$bbPress_active = Roost_bbPress::bbPress_active();
if( !empty( $bbPress_active['present'] ) && !empty( $bbPress_active['enabled'] ) ) {
    $roost_bbp = new Roost_bbPress();
}
register_activation_hook( __FILE__, array( 'ROOST', 'init' ) );
register_uninstall_hook( __FILE__, array( 'ROOST', 'uninstall' ) );
