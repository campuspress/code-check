<?php
class CampusPress_PHPShortTagsCheck implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		foreach ( $php_files as $php_key => $phpfile ) {
			campuspress_checkcount();
			if ( preg_match( '/<\?(\=?)(?!php|xml)/i', $phpfile ) ) {
				$filename = campuspress_tc_filename( $php_key );
				$grep = campuspress_tc_preg( '/<\?(\=?)(?!php|xml)/', $php_key );
				$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Found PHP short tags in file %1$s.%2$s', 'theme-check'), '<strong>' . $filename . '</strong>', $grep);
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new CampusPress_PHPShortTagsCheck;