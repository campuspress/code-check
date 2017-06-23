<?php
/**
 * Checks for Plugin Territory Guidelines.
 *
 * See http://make.wordpress.org/themes/guidelines/guidelines-plugin-territory/
 */

class CampusPress_Plugin_Territory implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		// Functions that are required to be removed from the theme.
		$forbidden_functions = array(
			'register_post_type',
			'register_taxonomy',
		);

		foreach ( $forbidden_functions as $function ) {
			campuspress_checkcount();
			if ( preg_match( '/[\s?]' . $function . '\(/', $php ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'WARNING', 'theme-check').'</span>: ' . sprintf( __( 'The theme uses the %s function, which is plugin-territory functionality.', 'theme-check' ), '<strong>' . esc_html( $function ) . '()</strong>' ) ;
				$ret = false;
			}
		}

		// Shortcodes can't be used in the post content, so warn about them.
		if ( false !== strpos( $php, 'add_shortcode' ) ) {
			campuspress_checkcount();
			$this->error[] = '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check').'</span>: ' . sprintf( __( 'The theme uses the %s function. Custom post-content shortcodes are plugin-territory functionality.', 'theme-check' ), '<strong>add_shortcode()</strong>' ) ;
			$ret = false;
		}

		return $ret;
	}
	
	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_Plugin_Territory;
