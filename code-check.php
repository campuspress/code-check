<?php
/*
Plugin Name: Code Check
Plugin URI: https://github.com/campuspress/code-check
Description: Checks plugins and themes for requirements needed to pass in order to be supported on sites hosted by CampusPress and WPMU DEV Hosting.
Author: CampusPress
Author URI: https://campuspress.com
Version: 1.2.1
Requires at least: 3.8
Tested up to: 4.8
Text Domain: theme-check

This plugin is a fork of Theme Check created by Otto42 and pross. There are just a few checks more for CampusPress users.

Please, do not use this plugin if you don't have a site in CampusPress network.
You can found the original Theme Check here: https://wordpress.org/plugins/theme-check/
*/

class CampusPress_ThemeCheckMain {
	function __construct() {
		add_action( 'admin_init', array( $this, 'tc_i18n' ) );
		add_action( 'admin_menu', array( $this, 'themecheck_add_page' ) );
		add_action( 'admin_menu', array( $this, 'plugincheck_add_page' ) );

		add_filter( 'campuspress_files_checks', array( $this, 'filter_files_checks' ) );
	}

	function tc_i18n() {
		load_plugin_textdomain( 'theme-check', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );
	}

	function load_styles() {
		wp_enqueue_style('style', plugins_url( 'assets/style.css', __FILE__ ), null, null, 'screen');
	}

	function themecheck_add_page() {
		$page = add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'campus-themecheck', array( $this, 'themecheck_do_page' ) );
		add_action('admin_print_styles-' . $page, array( $this, 'load_styles' ) );
	}

	function tc_add_headers( $extra_headers ) {
		$extra_headers = array( 'License', 'License URI', 'Template Version' );
		return $extra_headers;
	}

	function themecheck_do_page() {
		if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
		}

		add_filter( 'extra_theme_headers', array( $this, 'tc_add_headers' ) );

		include 'checkbase.php';
		include 'main.php';

		?>
		<div id="theme-check" class="wrap">
		<h1><?php _ex( 'Theme Check', 'title of the main page', 'theme-check' ); ?></h1>
		<div class="theme-check">
		<?php
			campuspress_tc_form( 'theme' );
			if ( !isset( $_POST[ 'themename' ] ) )  {
				campuspress_tc_intro();

			}

			if ( isset( $_POST[ 'themename' ] ) ) {
				if ( isset( $_POST[ 'trac' ] ) ) define( 'TC_TRAC', true );
				if ( defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
					@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
				}
				campuspress_check_main( $_POST[ 'themename' ], 'theme' );
			}
			?>
			</div> <!-- .theme-check-->
		</div>
		<?php
	}

	function plugincheck_add_page() {
		$page = add_plugins_page( 'Plugin Check', 'Plugin Check', 'manage_options', 'campus-plugincheck', array( $this, 'plugincheck_do_page' ) );
		add_action('admin_print_styles-' . $page, array( $this, 'load_styles' ) );
	}

	function plugincheck_do_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
		}

		add_filter( 'extra_theme_headers', array( $this, 'tc_add_headers' ) );

		include 'checkbase.php';
		include 'main.php';

		?>
		<div id="theme-check" class="wrap">
			<h1><?php _ex( 'Plugin Check', 'title of the main page', 'theme-check' ); ?></h1>
			<div class="theme-check">
				<?php
				campuspress_tc_form( 'plugin' );
				if ( !isset( $_POST[ 'themename' ] ) )  {
					campuspress_tc_intro();

				}

				if ( isset( $_POST[ 'themename' ] ) ) {
					if ( isset( $_POST[ 'trac' ] ) ) define( 'TC_TRAC', true );
					if ( defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
						@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
					}
					campuspress_check_main( $_POST[ 'themename' ], 'plugin' );
				}
				?>
			</div> <!-- .theme-check-->
		</div>
		<?php
	}

	function filter_files_checks( $files ) {
		$screen = get_current_screen();
		if ( 'plugins_page_campus-plugincheck' === $screen->id ) {
			$remove = array(
				'admin_menu.php',
				'adminbar.php',
				'basic.php',
				'comment_reply.php',
				'commpage.php',
				'content-width.php',
				'customs.php',
				'deregister.php',
				'plugin-territory.php',
				'navmenu.php',
				'links.php',
				'included-plugins.php',
				'include.php',
				'editorstyle.php',
				'favicon.php',
				'postthumb.php',
				'screenshot.php',
				'searchform.php',
				'style_needed.php',
				'style_suggested.php',
				'style_tags.php',
				'tags.php',
				'textdomain.php',
				'time_date.php',
				'title.php',
				'widgets.php'
			);
		}
		else {
			$remove = array(
				'cp_users_levels.php'
			);
		}

		$_files = array();
		foreach ( $files as $file ) {
			$basename = basename( $file );
			if ( ! in_array( $basename, $remove ) ) {
				$_files[] = $file;
			}
		}
		return $_files;
	}
}
new CampusPress_ThemeCheckMain;
