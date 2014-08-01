<?php

/**
 * @package   BP_Profile_Tabs
 * @author    Jacob Schweitzer <allambition@gmail.com>
 * @license   GPL-2.0+
 * @link      http://ijas.me
 * @copyright 2014 Jacob Schweitzer
 */


class BP_Profile_Tabs {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.5.0';

	/**
	 * @since    1.5.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'bp-profile-tabs';

	/**
	 * @since    1.5.0
	 *
	 * @var      string
	 */
	protected $plugin_name = 'BuddyPress Profile Tabs';

	/**
	 * Instance of this class.
	 *
	 * @since    1.5.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	
	
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.5.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		add_action('bp_before_profile_loop_content', array( $this, 'bpt_load') );

		add_action('bp_before_profile_loop_content', array( $this, 'bp_profile_tabs_top') );
		add_action('bp_before_profile_field_content', array( $this, 'bp_profile_tabs_start_div') );

		add_action('bp_after_profile_field_content', array( $this, 'bp_profile_tabs_end_div') );
		add_action('bp_after_profile_loop_content', array( $this, 'bp_profile_tabs_end_div') );

		
		add_action( 'wp_enqueue_scripts', array($this, 'register_bpt_script' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.5.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the plugin name.
	 *
	 * @since    1.5.0
	 *
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the version
	 *
	 * @since    1.5.0
	 *
	 * @return    Version const.
	 */
	public function get_plugin_version() {
		return self::VERSION;
	}


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.5.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.5.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();
				}
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.5.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.5.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.5.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here

		$bpt_options = get_option("bp_profile_tabs_option"); // option name

		// if no option settings set some defaults or import from old settings
		if ( empty( $bpt_options )  ) {

			// check for old option settings
			$old_theme_option = get_option("bp_profile_tabs_theme_option");
			if ( !empty( $old_theme_option ) ) {
				$bpt_options['bpt_theme'] = $old_theme_option;
				$bpt_options['bpt_cdn'] = 'google';
				update_option("bp_profile_tabs_option",$bpt_options);
				delete_option("bp_profile_tabs_theme_option");
			}

			// set default options on new option name if there is no old options
			if ( empty( $old_theme_option ) ) {
				$bpt_options['bpt_theme'] = 'cupertino';
				$bpt_options['bpt_cdn'] = 'google';
				add_option("bp_profile_tabs_option",$bpt_options);
			}
		}
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.5.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.5.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}


	/**
	 * Create the tabs div and un-ordered list for the tab titles from the database.
	 *
	 * @since    1.5.0
	 */
	public function bp_profile_tabs_top() {
		echo '<div id="tabs"><ul>';
		global $wpdb;
		$profile_field_groups_query = 'SELECT name FROM `'.$wpdb->prefix.'bp_xprofile_groups` order by group_order';
		$groups_list = $wpdb->get_col($profile_field_groups_query);
		$looper = 1;
		foreach ( $groups_list as $one_group_name ) {
			echo '<li><a href="#tabs-'.$looper.'">';
			echo $one_group_name;
			echo '</a></li>';
			$looper++;
		}
		echo '</ul>';
	}
	/**
	 * Creates the start of each tabs div to hold the tab content. 
	 *
	 * @since    1.5.0
	 */
	public function bp_profile_tabs_start_div() {
		if ( bp_is_user_profile() && !bp_is_user_profile_edit() ) {
			$profile_group_slug = bp_get_the_profile_group_id();
			echo '<div id="tabs-'.$profile_group_slug.'">';
		}
		
	}

	/**
	 * Ends the profile tabs div created to hold the tab content.
	 *
	 * @since    1.5.0
	 */
	public function bp_profile_tabs_end_div() { 
		if ( bp_is_user_profile() && !bp_is_user_profile_edit() ) {
			echo "</div>"; 
		}
	}

	/**
	 * Loads the tabs javascript and jQuery UI style.
	 *
	 * @since    1.5.0
	 */
	public function bpt_load() {

		wp_enqueue_style( $this->plugin_slug . '-jquery-ui-style' );
		wp_enqueue_script( $this->plugin_slug . '-script' );

	}
	 /**
	 * Registers the tabs javascript and jQuery UI style.
	 * @since    1.5.0
	 */

	public function register_bpt_script() {
		wp_register_script( $this->plugin_slug . '-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery', 'jquery-ui-tabs' ), self::VERSION );

		$bpt_options = get_option("bp_profile_tabs_option");
		if ( $bpt_options['bpt_cdn'] == 'google' ) {
				$jquery_ui_css_url = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/'.$bpt_options["bpt_theme"].'/jquery-ui.css';
		}
		if ( $bpt_options['bpt_cdn'] == 'microsoft' ) {
			$jquery_ui_css_url = 'http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.4/themes/'.$bpt_options["bpt_theme"].'/jquery-ui.css';
		}
		if ( $bpt_options['bpt_cdn'] == 'jquery' ) {
			$jquery_ui_css_url = 'http://code.jquery.com/ui/1.11.0/themes/'.$bpt_options["bpt_theme"].'/jquery-ui.css';
		}

		wp_register_style( $this->plugin_slug . '-jquery-ui-style', $jquery_ui_css_url, array(), self::VERSION );
	}

}
