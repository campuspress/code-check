<?php
class CampusPress_IframeCheck implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$checks = array(
			'/<(iframe)[^>]*>/' => __( 'iframes are sometimes used to load unwanted adverts and code on your site', 'theme-check' )
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				campuspress_checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = campuspress_tc_filename( $php_key );
					$error = ltrim( $matches[1], '(' );
					$error = rtrim( $error, '(' );
					$grep = campuspress_tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-info">'.__('INFO','theme-check').'</span>: '.__('<strong>%1$s</strong> was found in the file <strong>%2$s</strong> %3$s.%4$s', 'theme-check'), $error, $filename, $check, $grep ) ;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_IframeCheck;