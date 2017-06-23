<?php
class CampusPress_Style_Suggested implements CampusPress_themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// combine all the css files into one string to make it easier to search
		$css = implode( ' ', $css_files );

		campuspress_checkcount();
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme URI:' => 'Theme URI:',
			'[ \t\/*#]*Author URI:' => 'Author URI:',
			);

		foreach ($checks as $key => $check) {
			if ( !preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf('<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('%s is missing from your style.css header.', 'theme-check'), '<strong>' . $check . '</strong>' );
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_Style_Suggested;