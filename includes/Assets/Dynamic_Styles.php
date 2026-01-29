<?php

namespace HexaGrid\Assets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Dynamic_Styles
 *
 * Generates inline CSS based on shortcode attributes.
 */
class Dynamic_Styles {

    /**
     * Generate CSS.
     *
     * @param array $atts Attributes.
     * @param string $unique_id Unique ID for the wrapper to scope styles (optional, if we add IDs to wrappers).
     * @return string CSS string.
     */
    public static function generate( $atts, $unique_id = '' ) {
        // Strictly sanitize hex color
        $theme_color = isset( $atts['theme_color'] ) ? sanitize_hex_color( $atts['theme_color'] ) : '';

        if ( empty( $theme_color ) ) {
            return '';
        }

        // Ensure ID selector format
        $scope = $unique_id ? '#' . esc_attr( $unique_id ) . ' ' : '';

        $css = '<style>';
        
        // Backgrounds (Badges, Buttons)
        $css .= "{$scope}.hexagrid-sale-badge { background-color: " . esc_attr( $theme_color ) . " !important; }";
        $css .= "{$scope}.hexagrid-add-btn>a { background-color: " . esc_attr( $theme_color ) . " !important; }";
        $css .= "{$scope}.hexagrid-product-price ins .amount { color: " . esc_attr( $theme_color ) . " !important; }";
        $css .= "{$scope}.hexagrid-product:hover .hexagrid-product-cart-btn a { background-color: " . esc_attr( $theme_color ) . " !important; border-color: " . esc_attr( $theme_color ) . " !important; }";
        
        // Text Hover (Links) - NOW USING THEME COLOR
        $css .= "{$scope}.hexagrid-product-title a:hover, {$scope}.hexagrid-product-category a:hover { color: " . esc_attr( $theme_color ) . " !important; }";
        
        // Pagination/Action buttons if needed
        $css .= "{$scope}.swiper-button-next, {$scope}.swiper-button-prev { color: " . esc_attr( $theme_color ) . " !important; }";

        $css .= '</style>';

        return $css;
    }
}
