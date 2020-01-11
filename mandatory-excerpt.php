<?php
/**
Plugin Name:       Mandatory Excerpt
Plugin URI:        https://wordpress.org/plugins/mandatory excerpt/
Description:       Causes the excerpt to be required before a post can be published. Filters are provided for customization.
Version:           1.0.0
Requires at least: 4.4.0
Requires PHP:      7.0
Author:            theMikeD
Author URI:        https://www.codenamemiked.com/plugins/mandatory-excerpt/
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Based on the community work found here https://gist.github.com/swalkinshaw/2695510

@package cnmd
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CNMD_ME_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The class autoloader.
 */
spl_autoload_register(
	function( $class ) {
		// Standard WP class name format:
		//   class-<class_name>.php
		$filepath = str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';
		$parts    = pathinfo( $filepath );

		// If the files are sorted into folders mirroring the namespace prepended with 'classes/'.
		$filename = CNMD_ME_DIR . 'classes/' . $parts['dirname'] . '/class-' . strtolower( str_replace( '_', '-', $parts['basename'] ) );
		if ( file_exists( $filename ) ) {
			include_once $filename;
			return;
		}
	}
);


/**
 * Initialize the plugin, but only for admin.
 */
function cnmd_init_mandatory_excerpt() {
	if ( is_admin() ) {
		$me = new \cnmd\Mandatory_Excerpt();
	}
}

cnmd_init_mandatory_excerpt();
