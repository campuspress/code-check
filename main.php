<?php
function campuspress_check_main( $item, $type = 'theme' ) {
	global $themechecks, $data, $itemname;
	$itemname = $item;
	if ( 'theme' === $type ) {
		$item = get_theme_root( $item ) . "/$item";
		$data = campuspress_tc_get_theme_data( $item . '/style.css' );

		$files = campuspress_listdir( $item );

		if ( $data[ 'Template' ] ) {
			// This is a child theme, so we need to pull files from the parent, which HAS to be installed.
			$parent = get_theme_root( $data[ 'Template' ] ) . '/' . $data['Template'];
			if ( ! campuspress_tc_get_theme_data( $parent . '/style.css' ) ) { // This should never happen but we will check while were here!
				echo '<h2>' . sprintf(__('Parent theme <strong>%1$s</strong> not found! You have to have parent AND child-theme installed!', 'theme-check'), $data[ 'Template' ] ) . '</h2>';
				return;
			}
			$parent_data = campuspress_tc_get_theme_data( $parent . '/style.css' );
			$itemname = basename( $parent );
			$files = array_merge( campuspress_listdir( $parent ), $files );
		}
	}
	else {
		$plugin_path = WP_PLUGIN_DIR . '/' . $item;
		$data = get_plugin_data( $plugin_path );
		$files = campuspress_listdir( dirname( $plugin_path ) );
	}



	if ( $files ) {
		foreach( $files as $key => $filename ) {
			if ( substr( $filename, -4 ) == '.php' && ! is_dir( $filename ) ) {
				$php[$filename] = file_get_contents( $filename );
				$php[$filename] = campuspress_tc_strip_comments( $php[$filename] );
			}
			else if ( substr( $filename, -4 ) == '.css' && ! is_dir( $filename ) ) {
				$css[$filename] = file_get_contents( $filename );
			}
			else {
				$other[$filename] = ( ! is_dir($filename) ) ? file_get_contents( $filename ) : '';
			}
		}

		// run the checks
		$success = campuspress_run_themechecks($php, $css, $other);

		global $checkcount;

		// second loop, to display the errors
		if ( 'theme' === $type ) {
			echo '<h2>' . __( 'Theme Info', 'theme-check' ) . ': </h2>';
		}
		else {
			echo '<h2>' . __( 'Plugin Info', 'theme-check' ) . ': </h2>';
		}
		echo '<div class="theme-info">';
		if ( 'theme' === $type && file_exists( trailingslashit( WP_CONTENT_DIR . '/themes' ) . trailingslashit( basename( $item ) ) . 'screenshot.png' ) ) {
			$image = getimagesize( $item . '/screenshot.png' );
			echo '<div style="float:right" class="theme-info"><img style="max-height:180px;" src="' . trailingslashit( WP_CONTENT_URL . '/themes' ) . trailingslashit( basename( $item ) ) . 'screenshot.png" />';
			echo '<br /><div style="text-align:center">' . $image[0] . 'x' . $image[1] . ' ' . round( filesize( $item . '/screenshot.png' )/1024 ) . 'k</div></div>';
		}

		echo ( !empty( $data[ 'Title' ] ) ) ? '<p><label>' . __( 'Title', 'theme-check' ) . '</label><span class="info">' . $data[ 'Title' ] . '</span></p>' : '';

		echo ( !empty( $data['Name'] ) ) ? '<p><label>' . __( 'Plugin Name', 'theme-check' ) . '</label><span class="info">' . $data['Name'] . '</span></p>' : '';

		echo ( !empty( $data[ 'Version' ] ) ) ? '<p><label>' . __( 'Version', 'theme-check' ) . '</label><span class="info">' . $data[ 'Version' ] . '</span></p>' : '';

		if ( 'theme' === $type ) {
			echo ( !empty( $data[ 'AuthorName' ] ) ) ? '<p><label>' . __( 'Author', 'theme-check' ) . '</label><span class="info">' . $data[ 'AuthorName' ] . '</span></p>' : '';
		}
		else {
			echo ( !empty( $data['Author'] ) ) ? '<p><label>' . __( 'Author', 'theme-check' ) . '</label><span class="info">' . $data['Author'] . '</span></p>' : '';
		}

		echo ( !empty( $data[ 'AuthorURI' ] ) ) ? '<p><label>' . __( 'Author URI', 'theme-check' ) . '</label><span class="info"><a href="' . $data[ 'AuthorURI' ] . '">' . $data[ 'AuthorURI' ] . '</a>' . '</span></p>' : '';

		echo ( !empty( $data[ 'URI' ] ) ) ? '<p><label>' . __( 'Theme URI', 'theme-check' ) . '</label><span class="info"><a href="' . $data[ 'URI' ] . '">' . $data[ 'URI' ] . '</a>' . '</span></p>' : '';
		echo ( !empty( $data['PluginURI'] ) ) ? '<p><label>' . __( 'Plugin URI', 'theme-check' ) . '</label><span class=info"><a href="' . esc_url( $data['PluginURI'] ) . '">' . $data['PluginURI'] . '</a></span></p>' : '';

		echo ( !empty( $data[ 'License' ] ) ) ? '<p><label>' . __( 'License', 'theme-check' ) . '</label><span class="info">' . $data[ 'License' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'License URI' ] ) ) ? '<p><label>' . __( 'License URI', 'theme-check' ) . '</label><span class="info">' . $data[ 'License URI' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'Tags' ] ) ) ? '<p><label>' . __( 'Tags', 'theme-check' ) . '</label><span class="info">' . implode( $data[ 'Tags' ], ', ') . '</span></p>' : '';

		echo ( !empty( $data[ 'Description' ] ) ) ? '<p><label>' . __( 'Description', 'theme-check' ) . '</label><span class="info">' . $data[ 'Description' ] . '</span></p>' : '';

		echo ( !empty( $data['Network'] ) ) ? '<p><label>' . __( 'Network Only', 'theme-check' ) . '</label><span class=info">' . $data['Network'] . '</span></p>' : '';



		if ( 'theme' === $type && $data[ 'Template' ] ) {
		if ( $data['Template Version'] > $parent_data['Version'] ) {
			echo '<p>' . sprintf(
				__('This child theme requires at least version %1$s of theme %2$s to be installed. You only have %3$s please update the parent theme.', 'theme-check'),
				'<strong>' . $data['Template Version'] . '</strong>',
				'<strong>' . $parent_data['Title'] . '</strong>',
				'<strong>' . $parent_data['Version'] . '</strong>'
			) . '</p>';
		}
			echo '<p>' . sprintf(
				__( 'This is a child theme. The parent theme is: %s. These files have been included automatically!', 'theme-check'),
				'<strong>' . $data[ 'Template' ] . '</strong>'
			) . '</p>';
			if ( empty( $data['Template Version'] ) ) {
				echo '<p>' . __('Child theme does not have the <strong>Template Version</strong> tag in style.css.', 'theme-check') . '</p>';
			} else {
				echo ( $data['Template Version'] < $parent_data['Version'] ) ? '<p>' . sprintf(__('Child theme is only tested up to version %1$s of %2$s breakage may occur! %3$s installed version is %4$s', 'theme-check'), $data['Template Version'], $parent_data['Title'], $parent_data['Title'], $parent_data['Version'] ) . '</p>' : '';
			}
		 }
		echo '</div><!-- .theme-info-->';

		$plugins = get_plugins( '/campuspress-theme-check' );
		$version = explode( '.', $plugins['theme-check.php']['Version'] );

		$title = 'theme' === $type ? $data[ 'Title' ] : $data['Name'];
		echo '<p>' . sprintf(
			__(' Running %1$s tests against %2$s using Guidelines Version: %3$s Plugin revision: %4$s', 'theme-check'),
			'<strong>' . $checkcount . '</strong>',
			'<strong>' . $title . '</strong>',
			'<strong>' . $version[0] . '</strong>',
			'<strong>' . $version[1] . '</strong>'
		) . '</p>';
		$results = campuspress_display_themechecks();
		if ( !$success ) {
			echo '<h2>' . sprintf(__('One or more errors were found for %1$s.', 'theme-check'), $title ) . '</h2>';
		} else {
			echo '<h2>' . sprintf(__('%1$s passed the tests', 'theme-check'), $title ) . '</h2>';
			campuspress_tc_success( $type );
		}
		if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<div class="updated"><span class="tc-fail">' . __('WARNING','theme-check') . '</span> ' . __( '<strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="https://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!', 'theme-check' ) . '</div>';
		echo '<div class="tc-box">';
		echo '<ul class="tc-result">';
		echo $results;
		echo '</ul></div>';
	}
}

// strip comments from a PHP file in a way that will not change the underlying structure of the file
function campuspress_tc_strip_comments( $code ) {
	$strip = array( T_COMMENT => true, T_DOC_COMMENT => true);
	$newlines = array( "\n" => true, "\r" => true );
	$tokens = token_get_all($code);
	reset($tokens);
	$return = '';
	$token = current($tokens);
	while( $token ) {
		if( !is_array($token) ) {
			$return.= $token;
		} elseif( !isset( $strip[ $token[0] ] ) ) {
			$return.= $token[1];
		} else {
			for( $i = 0, $token_length = strlen($token[1]); $i < $token_length; ++$i )
			if( isset($newlines[ $token[1][$i] ]) )
			$return.= $token[1][$i];
		}
		$token = next($tokens);
	}
	return $return;
}

function campuspress_tc_intro() {
?>
	<h2><?php _e( 'About', 'theme-check' ); ?></h2>
	<p><?php _e( "The Incsub Code Check is an easy way to test your theme or plugin and make sure it's up to date with the latest theme review standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.", 'theme-check' ); ?></p>
	<?php
}

function campuspress_tc_success( $type = 'theme' ) {
	if ( 'theme' === $type ) {
		?>
		<div class="tc-success"><p><?php _e( 'Now your theme has passed the basic tests you need to check it properly using the test data before you upload to CampusPress.', 'theme-check' ); ?></p>
			<p><?php _e( 'Make sure to review the guidelines at <a href="https://codex.wordpress.org/Theme_Review">Theme Review</a> before uploading a Theme.', 'theme-check' ); ?></p>
			<h3><?php _e( 'Codex Links', 'theme-check' ); ?></h3>
			<ul>
				<li><a href="https://codex.wordpress.org/Theme_Development"><?php _e('Theme Development', 'theme-check' ); ?></a></li>
				<li><a href="https://wordpress.org/support/forum/5"><?php _e('Themes and Templates forum', 'theme-check' ); ?></a></li>
				<li><a href="https://codex.wordpress.org/Theme_Unit_Test"><?php _e('Theme Unit Tests', 'theme-check' ); ?></a></li>
			</ul></div>
		<?php
	}
	else {
		?>
		<div class="tc-success"><p><?php _e( 'Now your plugin has passed the basic tests you need to check it properly using the test data before you upload to CampusPress.', 'theme-check' ); ?></p>
			<h3><?php _e( 'Codex Links', 'theme-check' ); ?></h3>
			<ul>
				<li><a href="https://codex.wordpress.org/Writing_a_Plugin"><?php _e('Theme Development', 'theme-check' ); ?></a></li>
			</ul></div>
		<?php
	}

}

function campuspress_tc_form( $type = 'theme' ) {
	$action = 'theme' === $type ? 'themes.php?page=campus-themecheck' : 'plugins.php?page=campus-plugincheck';

	if ( 'theme' === $type ) {
		$items = campuspress_tc_get_themes();
	}
	else {
		$items = campuspress_tc_get_plugins();
	}

	echo '<form action="' . esc_attr( $action ) . '" method="post">';
	echo '<select name="themename">';
	foreach( $items as $name => $item ) {
		_campuspress_tc_items_dropdown_option( $item, $name, $type );
	}
	echo '</select>';
	echo '<input class="button" type="submit" value="' . __( 'Check it!', 'theme-check' ) . '" />';
	if ( defined( 'TC_PRE' ) || defined( 'TC_POST' ) ) echo ' <input name="trac" type="checkbox" /> ' . __( 'Output in Trac format.', 'theme-check' );
	echo '<input name="s_info" type="checkbox" /> ' . __( 'Suppress INFO.', 'theme-check' );
	echo '<input name="type" type="hidden" value="' . esc_attr( $type ) . '" />';
	echo '</form>';
}

function _campuspress_tc_items_dropdown_option( $item, $name, $type = 'theme' ) {
	if ( 'theme' === $type ) {
		echo '<option ';
		if ( isset( $_POST['themename'] ) ) {
			echo ( $item['Stylesheet'] === $_POST['themename'] ) ? 'selected="selected" ' : '';
		} else {
			echo ( basename( STYLESHEETPATH ) === $item['Stylesheet'] ) ? 'selected="selected" ' : '';
		}
		echo ( basename( STYLESHEETPATH ) === $item['Stylesheet'] ) ? 'value="' . $item['Stylesheet'] . '" style="font-weight:bold;">' . $name . '</option>' : 'value="' . $item['Stylesheet'] . '">' . $name . '</option>';
	} else {
		echo '<option ';
		if ( isset( $_POST['themename'] ) ) {
			echo ( $name === $_POST['themename'] ) ? 'selected="selected" ' : '';
		}
		echo 'value="' . $name . '">' . $item['Name'] . '</option>';
	}
}
