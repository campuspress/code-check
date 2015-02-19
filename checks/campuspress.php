<?php
class CampusPress_CampusPress_Checks implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$checks = array(
			'/wp_feed_cache_transient_lifetime/' => __( 'Don’t ever change wp_feed_cache_transient_lifetime (hook to the filter).', 'theme-check' ),
			'/SHOW TABLES\s/i' => __( 'Do not list every table in the database (you don\'t want to millions of tables in the list.', 'theme-check' ),
			'/dbDelta\s?\(/' => __( 'Do not create/modify tables for themes.', 'theme-check' ),
			'/remove_role\s?\(/' => __( 'Do not remove roles.', 'theme-check' ),
			'/flush_rules|flush_rewrite_rules/' => __( 'Do not flush rewrite rules.', 'theme-check' ),
			'/wp_cache_flush/' => __( 'Do not flush cache', 'theme-check' ),
			'/chdir|chroot|closedir|dir\s?\(|glob\s?\(|getcwd|opendir|readdir|rewinddir|scandir/' => __( 'Directory disk operations are not allowed', 'theme-check' ),
			'/googlesyndication\.com/' => __( 'Loading content from googlesyndication.com is not allowed', 'theme-check' ),
			'/ALLOW_EXTERNAL/' => __( 'Changing ALLOW_EXTERNAL constant is not allowed', 'theme-check' ),
			'/CURLOPT_CONNECTTIMEOUT/' => __( 'Do not set CURLOPT_CONNECTTIMEOUT constant', 'theme-check' ),
			'/WP_DEBUG|error_reporting|display_errors/' => __( 'Changing WP_DEBUG, error_reporting or display_errors is not allowed', 'theme-check' ),
		);

		$grep = '';

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
			campuspress_checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = campuspress_tc_filename( $php_key );
					$error = ltrim( trim( $matches[0], '(' ) );
					$grep = campuspress_tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'. __( 'WARNING', 'theme-check' ) . '</span>: Found <strong>%1$s</strong> in the file <strong>%2$s</strong>. %3$s. %4$s', $error, $filename, $check, $grep );
					$ret = false;
				}
			}
		}



		$checks = array(
			'/wp_remote_.*\s?\(/' => __( 'Looks like you are trying to retrieve a URL using HTTP requests. Please, remember that you cannot modify timeouts on these requests', 'theme-check' ),
			'/file_get_contents/' => __( 'Make sure that you don\t call <strong>file_get_contents</strong> to get a remote file, use <strong>campus_remote_get_contents</strong> for that purpose', 'theme-check' ),
			'/DESC\s/' => __( 'Use DESCRIBE to describe tables not DESC', 'theme-check' ),
			'/WPCom_Theme_Updater/' => __( '<strong>WPCom_Theme_Updater</strong> class is usally used for updates. Please, deactivate the class.', 'theme-check' ),
			'/\$wpdb/' => __( 'Looks like you\re making queries. Please, check that they are not expensive or replace them for native WordPress functions', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				campuspress_checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = campuspress_tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$grep = campuspress_tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('ALERT','theme-check').'</span>: '.__('<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. %3$s', 'theme-check'), $error, $filename, $check );
				}
			}
		}

		
		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_CampusPress_Checks;
