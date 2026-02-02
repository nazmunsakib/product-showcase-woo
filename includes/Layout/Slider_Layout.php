<?php

namespace HexaGrid\Layout;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Slider_Layout
 */
class Slider_Layout implements Layout_Interface {

    /**
     * Render the slider layout.
     *
     * @param \WP_Query $query The product query.
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render( $query, $atts ) {
        if ( ! $query->have_posts() ) {
            return '<p class="hexagrid-no-products">No products found.</p>';
        }

        $style = isset( $atts['style'] ) ? sanitize_file_name( $atts['style'] ) : 'slider-1';
        $template_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/templates/slider/' . $style . '.php';

        if ( ! file_exists( $template_path ) ) {
             $template_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/templates/slider/slider-1.php';
        }
        
        ob_start();
        echo wp_kses( \HexaGrid\Assets\Dynamic_Styles::generate( $atts, $atts['wrapper_id'] ), array( 'style' => array() ) );
        echo '<div id="' . esc_attr( $atts['wrapper_id'] ) . '" class="hexagrid-layout-container hexagrid-slider-container hexagrid-' . esc_attr( $style ) . '">';
        include $template_path;
        echo '</div>';
        return ob_get_clean();
    }
}
