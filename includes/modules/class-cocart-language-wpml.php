<?php
/**
 * Handles language support for WPML.
 *
 * @author  Sébastien Dumont
 * @package CoCart\Language\Modules
 * @since   1.0.0
 */

namespace CoCart\Language\Modules;

// If WPML doesn't exist then exit.
if ( ! class_exists( 'SitePress' ) ) {
	return;
}

use \WPML\Core\ISitePress;
use \WCML\Rest\Hooks;
//use \WCML\Rest\Wrapper\Factory;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If WooCommerce Multilingual isn't active then exit.
if ( ! class_exists( '\WCML\Rest\Hooks' ) ) {
	return;
}

class WPML extends Hooks {

	var $wpml;
	var $sitepress;

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		$this->wpml = new \SitePress();

		$this->sitepress = function_exists( 'getSitePress' ) ? getSitePress() : null;

		// Product
		//$restObject = Factory::create( 'product' );

		//add_filter( "cocart_product_query", array( $restObject, 'query' ), 10, 2 );
		/*add_filter( "cocart_prepare_objects_query", array( $restObject, 'query' ), 10, 2 );
		add_action( "cocart_prepare_product_object", array( $restObject, 'prepare' ), 10, 3 );
		add_action( "cocart_prepare_product_object_v2", array( $restObject, 'prepare' ), 10, 3 );*/

		// Product Variation
		//$restObjectV = Factory::create( 'product_variation' );

		/*add_filter( "cocart_product_variation_query", array( $restObjectV, 'query' ), 10, 2 );
		add_filter( "cocart_product_variation_object_query", array( $restObjectV, 'query' ), 10, 2 );
		add_action( "cocart_prepare_product_variation_object", array( $restObjectV, 'prepare' ), 10, 3 );*/

		// Cart
		add_filter( 'cocart_cart_item_name', array( $this, 'adjust_cart_item_name' ), 10, 2 );

		// Store
		add_filter( 'cocart_store_index', array( $this, 'add_store_info' ) );
	}

	/**
	 * @param array $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		$data = $request->get_params();

		if ( isset( $data['lang'] ) && $data['lang'] === 'all' ) {
			remove_filter( 'posts_join', [ $this->wpmlQueryFilter, 'posts_join_filter' ] );
			remove_filter( 'posts_where', [ $this->wpmlQueryFilter, 'posts_where_filter' ] );
		}

		return $args;
	}

	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param \WP_REST_Response $response
	 * @param object $object
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		$response->data['translations'] = [];

		$trid = $this->wpmlPostTranslations->get_element_trid( $response->data['id'] );

		if ( $trid ) {
			$translations = $this->wpmlPostTranslations->get_element_translations( $response->data['id'], $trid );
			foreach ( $translations as $translation ) {
				$response->data['translations'][ $this->wpmlPostTranslations->get_element_lang_code( $translation ) ] = $translation;
			}
			$response->data['lang'] = $this->wpmlPostTranslations->get_element_lang_code( $response->data['id'] );
		}

		return $response;
	}

	/**
	 * Adjusts the cart item product name.
	 *
	 * @access public
	 *
	 * @param string     $name The cart item product name.
	 * @param WC_Product $product The cart item product.
	 *
	 * @return WC_Product
	 */
	public function adjust_cart_item_name( $name, $product ) {
		$product_id = $product->get_id();

		$current_product_id = wpml_object_id_filter( $product_id, get_post_type( $product_id ) );

		if ( $current_product_id ) {
			return wc_get_product( $current_product_id )->get_name();
		}

		return $name;
	} // END adjust_cart_item_name()

	/**
	 * Adds the store info to the response.
	 *
	 * @access public
	 *
	 * @param array $response The response data.
	 *
	 * @return WP_REST_Response $response The response data.
	 */
	public function add_store_info( $response ) {
		$data = maybe_unserialize( $response->data );

		$isMultiCurrencyOn = function_exists( 'wcml_is_multi_currency_on' ) ? wcml_is_multi_currency_on() : 'off';

		$data['is_multi_currency_on'] = $isMultiCurrencyOn;
		$data['current_language']     = $this->wpml->get_current_language() ? $this->wpml->get_current_language() : get_bloginfo( 'language' );

		$response->set_data( $data );

		return $response;
	} // END add_store_info()

} // END class.

return new WPML();