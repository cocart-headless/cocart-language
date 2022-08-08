<?php
/*
 * Plugin Name: CoCart Language
 * Plugin URI:  https://cocart.xyz
 * Description: Experimental plugin to add language support for CoCart.
 * Author:      Sébastien Dumont
 * Author URI:  https://sebastiendumont.com
 * Version:     1.0.0
 * Text Domain: cocart-lang
 * Domain Path: /languages/
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * WC requires at least: 5.4
 * WC tested up to: 6.7
 *
 * @package CoCart Language
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'COCART_LANGUAGE_FILE' ) ) {
	define( 'COCART_LANGUAGE_FILE', __FILE__ );
}

// Include the main CoCart Language Package class.
if ( ! class_exists( 'CoCart\Language\Package', false ) ) {
	include_once untrailingslashit( plugin_dir_path( COCART_LANGUAGE_FILE ) ) . '/includes/class-cocart-language.php';
}

/**
 * Returns the main instance of cocart_language and only runs if it does not already exists.
 *
 * @return cocart_language
 */
if ( ! function_exists( 'cocart_language' ) ) {
	function cocart_language() {
		return CoCart\Language\Package::init();
	}

	add_action( 'plugins_loaded', 'cocart_language', 0 );
}
