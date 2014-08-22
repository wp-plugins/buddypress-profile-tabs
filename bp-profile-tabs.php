<?php

/**
 *
 * @package   BP_Profile_Tabs
 * @author    Jacob Schweitzer <allambition@gmail.com>
 * @license   GPL-2.0+
 * @link      http://ijas.me
 * @copyright 2014 Jacob Schweitzer
 *
 * Plugin Name:       BuddyPress Profile Tabs
 * Plugin URI:        http://ijas.me
 * Description:       Makes the profile groups BuddyPress provides into tabs to better organize and consolidate the profile page. 
 * Version:           1.5.4
 * Author:            Jacob Schweitzer
 * Author URI:        http://ijas.me
 * Text Domain:       bp-profile-tabs
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/*
 * Load Main Plugin File
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-bp-profile-tabs.php' );

/*
 * Load Language wrapper function for WPML/Ceceppa Multilingua/Polylang
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/language.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'BP_Profile_Tabs', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BP_Profile_Tabs', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'BP_Profile_Tabs', 'get_instance' ) );

/*
 * Load Admin Settings Page
 */
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-bp-profile-tabs-admin.php' );
	add_action( 'plugins_loaded', array( 'BP_Profile_Tabs_Admin', 'get_instance' ) );
}
