<?php
/**
 * Elementor editor cleanup
 *
 * Hides Elementor Free upsells, Pro widgets and promotional panels
 * from the editor interface.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stop outside admin */
if ( ! is_admin() ) {
	return;
}

/** Disable Elementor AI option */
add_filter( 'get_user_option_elementor_enable_ai', '__return_zero' );

/** Hide Elementor Pro banner details */
add_filter( 'elementor/editor/panel/get_pro_details', function ( $details ) {
	$details['show_banner'] = false;

	return $details;
}, 999 );

/** Remove Elementor promotion widgets from editor config */
add_filter( 'elementor/editor/localize_settings', function ( $settings ) {
	$settings['promotionWidgets'] = [];
	$settings['elementPromotionURL'] = '';
	$settings['dynamicPromotionURL'] = '';

	return $settings;
}, 999 );

/** Hide Elementor editor promotions */
add_action( 'elementor/editor/footer', function () {
	?>
	<style id="vred-elementor-editor-cleanup">
		#elementor-panel-get-pro-elements,
		#elementor-panel-get-pro-elements-sticky,
		#elementor-panel-category-pro-elements,
		#elementor-panel-category-theme-elements,
		#elementor-panel-category-theme-elements-single,
		#elementor-panel-category-theme-elements-archive,
		#elementor-panel-category-woocommerce-elements,
		#elementor-panel-category-woocommerce-elements-single,
		#elementor-panel-category-woocommerce-elements-archive,
		#elementor-panel-category-atomic-form,
		#elementor-panel-category-custom-widgets,
		.elementor-panel-editor-sticky-promotion,
		.elementor-get-pro-sticky-message,
		.elementor-panel-heading-promotion,
		.elementor-element--promotion,
		.elementor-element:has(.eicon-lock),
		.elementor-element:has(.eicon-pro-icon),
		.elementor-control[class*="promotion"],
		.elementor-control[class*="promo"] {
			display: none !important
		}
	</style>

	<script id="vred-elementor-editor-cleanup-js">
		(function () {
			function cleanElementorPromotions() {
				if (!window.elementor || !window.elementor.config) {
					return;
				}

				window.elementor.config.promotionWidgets = [];
				window.elementor.config.elementPromotionURL = '';
				window.elementor.config.dynamicPromotionURL = '';
			}

			cleanElementorPromotions();

			window.addEventListener('elementor:init', cleanElementorPromotions);
		})();
	</script>
	<?php
}, 999 );
