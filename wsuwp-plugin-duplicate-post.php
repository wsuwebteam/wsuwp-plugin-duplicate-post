<?php
/**
 * Plugin Name: WSUWP Plugin | Duplicate Post
 * Plugin URI: https://github.com/wsuwebteam/wsuwp-plugin-duplicate-post
 * Description: Adds a duplicate post button to the admin bar.
 * Version:    0.0.2
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
require_once __DIR__ . '/includes/plugin.php';
