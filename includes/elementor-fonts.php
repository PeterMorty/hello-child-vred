<?php
/**
 * Elementor font customizations
 *
 * @package Monkraf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Add Monkraf font group to Elementor font selector */
function monkraf_child_add_elementor_font_group( $font_groups ) {
	$font_groups['monkraf_fonts'] = __( 'Monkraf Fonts', 'monkraf' );

	return $font_groups;
}
add_filter( 'elementor/fonts/groups', 'monkraf_child_add_elementor_font_group' );

/** Add Monkraf fonts to Elementor font selector */
function monkraf_child_add_elementor_fonts( $fonts ) {
	$fonts['Vollkorn'] = 'monkraf_fonts';
	$fonts['Mulish']   = 'monkraf_fonts';

	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'monkraf_child_add_elementor_fonts' );