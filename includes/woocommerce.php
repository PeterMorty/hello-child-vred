<?php
/**
 * WooCommerce customizations
 *
 * @package Nombre
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shipping prices already include tax */
add_filter( 'woocommerce_shipping_prices_include_tax', '__return_true' );
