<?php
/**
 * Plugin Name: Hide posts for specific roles
 * Plugin URI: http://www.marcelotorresweb.com/hide-posts-for-specific-roles/
 * Description: Hide posts(post, page, post types, attachments) for specific roles
 * Author: marcelotorres
 * Author URI: http://marcelotorresweb.com/
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: hpfsr
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Load the plugin text domain for translation. 
function hpfsr_load_plugin_textdomain() {
	load_plugin_textdomain( 'hpfsr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'hpfsr_load_plugin_textdomain' );

//include classes
include( 'classes/class-hide-posts-for-specific-roles.php' );

//Initialize the plugin
(new Hide_Posts_For_Specific_Roles());