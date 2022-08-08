<?php
/**
 * This file is designed to be used to load as package NOT a WP plugin!
 *
 * @version 1.0.0
 * @package CoCart Language Package
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

	cocart_language();
}
