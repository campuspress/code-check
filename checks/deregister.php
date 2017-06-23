<?php
class CampusPress_DeregisterCheck implements CampusPress_themecheck {
	protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

			$ret = true;
			campuspress_checkcount();

			foreach ( $php_files as $file_path => $file_content ) {

				$filename = campuspress_tc_filename( $file_path );

				if ( preg_match( '/wp_deregister_script/', $file_content) ) {

					$error = '/wp_deregister_script/';
					$grep = campuspress_tc_preg( $error, $file_path );

					$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __('WARNING','theme-check') . '</span>: ' . __( 'Found wp_deregister_script in %1$s. Themes must not deregister core scripts.', 'theme-check' ),
						'<strong>' . $filename . '</strong>') . $grep;	
					$ret = false;			
				}
			}
			return $ret;

		}

	function getError() { return $this->error; }
}
$themechecks[] = new CampusPress_DeregisterCheck;