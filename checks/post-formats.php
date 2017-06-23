<?php

class CampusPress_PostFormatCheck implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$php = implode( ' ', $php_files );
		$css = implode( ' ', $css_files );

		campuspress_checkcount();

		$checks = array(
			'/add_theme_support\(\s?("|\')post-formats("|\')/m'
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $check ) {
				campuspress_checkcount();
				if ( preg_match( $check, $phpfile, $matches ) ) {
					if ( !strpos( $php, 'get_post_format' ) && !strpos( $php, 'has_post_format' ) && !strpos( $css, '.format' ) ) {
						$filename = campuspress_tc_filename( $php_key );
						$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
						$error = esc_html( rtrim($matches[0], '(' ) );
						$grep = campuspress_tc_grep( rtrim($matches[0], '(' ), $php_key);
						$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('%1$s was found in the file %2$s. However get_post_format and/or has_post_format were not found, and no use of formats in the CSS was detected.', 'theme-check'), '<strong>' . $error . '</strong>', '<strong>' . $filename . '</strong>');
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_PostFormatCheck;