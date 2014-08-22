<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package   BP_Profile_Tabs
 * @author    Jacob Schweitzer <allambition@gmail.com>
 * @license   GPL-2.0+
 * @link      http://ijas.me
 * @copyright 2014 Jacob Schweitzer
 */

// If uninstall not called from WordPress, then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	
	if ( $blogs ) {

		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog[ 'blog_id' ] );
			
			delete_option("bp_profile_tabs_theme_option");
			delete_option("bp_profile_tabs_option");

			restore_current_blog();
		}
	}
} 
else {
	delete_option("bp_profile_tabs_theme_option");
	delete_option("bp_profile_tabs_option");
}