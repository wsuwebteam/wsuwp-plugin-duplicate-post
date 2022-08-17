<?php
/**
 * Plugin Name: WSUWP Plugin | Duplicate Post
 * Plugin URI: https://github.com/wsuwebteam/wsuwp-plugin-duplicate-post
 * Description: Adds a duplicate post button to the admin bar.
 * Version:    1.0.2.1
 * Requires PHP: 7.3
 * Author: Washington State University, Dan White
 * Author URI: https://web.wsu.edu/
 * Text Domain: wsuwp-plugin-duplicate-post
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Initiate plugin
add_action( 'after_setup_theme', 'wsuwp_plugin_init' );

function wsuwp_plugin_init() {

	if ( is_admin() && defined( 'ISWDS' ) ) {

		// Initiate plugin
		require_once __DIR__ . '/includes/plugin.php';

	}

}
