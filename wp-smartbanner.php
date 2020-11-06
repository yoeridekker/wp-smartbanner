<?php
/**
 * Plugin Name: WP Smart Banner
 * Plugin URI: https://www.3eighty.nl/smartbanner
 * Description: A customisable WordPress smart app banner for iOS and Android.
 * Version: 1.0.0
 * Author: Yoeri Dekker
 * Author URI: https://www.3eighty.nl
 * Text Domain: wp-smartbanner
 * Domain Path: /languages
 *
 * @package WP_Smartbanner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Require the main class.
require_once 'class-wp-smartbanner.php';

/**
 * WP_Smartbanner
 *
 * The main function responsible for returning the one true acf Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $wp_smartbanner = wp_smartbanner(); ?>
 *
 * @date    03/11/2020
 * @since   1.0.0
 *
 * @return  WP_Smartbanner
 */
function wp_smartbanner() {
	global $wp_smartbanner;

	// Instantiate only once.
	if ( ! isset( $wp_smartbanner ) ) {
		$wp_smartbanner = new WP_Smartbanner();
		$wp_smartbanner->initialize();
	}
	return $wp_smartbanner;
}

// Instantiate.
wp_smartbanner();
