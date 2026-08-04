<?php
/**
 * Elementor custom fonts
 *
 * @package Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Add custom font group to Elementor */
function child_theme_add_elementor_font_group( $font_groups ) {
	$font_groups['custom_fonts'] = __( 'Custom Fonts', 'child-theme' );

	return $font_groups;
}
add_filter( 'elementor/fonts/groups', 'child_theme_add_elementor_font_group' );

/** Add custom fonts to Elementor */
function child_theme_add_elementor_fonts( $fonts ) {
	$fonts['Bebas Neue'] = 'custom_fonts';
	$fonts['Mona Sans'] = 'custom_fonts';

	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'child_theme_add_elementor_fonts' );