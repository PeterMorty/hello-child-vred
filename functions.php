<?php
/**
 * Nombre child theme functions
 *
 * @package Nombre
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child theme styles
 */
function nombre_enqueue_styles() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'nombre',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		$theme_version
	);
	wp_enqueue_style(
		'nombre-woocommerce',
		get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
		array( 'nombre' ),
		$theme_version
	);
}
add_action( 'wp_enqueue_scripts', 'nombre_enqueue_styles', 20 );

/** Load child theme includes */
require_once get_stylesheet_directory() . '/includes/elementor-editor-cleanup.php';
require_once get_stylesheet_directory() . '/includes/elementor-fonts.php';