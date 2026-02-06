<?php

namespace HexaGrid\Shortcode;

use HexaGrid\Query\Query_Builder;
use HexaGrid\Layout\Grid_Layout;
use HexaGrid\Layout\List_Layout;
use HexaGrid\Layout\Slider_Layout;
use HexaGrid\Layout\Table_Layout;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Shortcode_Handler
 *
 * Registers and processes the [hexagrid_product_showcase] shortcode.
 */
class Shortcode_Handler {

    /**
     * Initialize hooks.
     */
    public function init() {
        add_shortcode( 'hexagrid_product_showcase', [ $this, 'render_shortcode' ] );
    }

    /**
     * Render the shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string Rendered HTML.
     */
    public function render_shortcode( $atts ) {
        // 0. Handle Preset ID
        if ( isset( $atts['preset_id'] ) && ! empty( $atts['preset_id'] ) ) {
            $preset_id = intval( $atts['preset_id'] );
            
            // Map meta keys to shortcode attributes
            $meta_map = [
                'layout'       => '_hexagrid_layout_type',
                'style'        => '_hexagrid_layout_style',
                'content_type' => '_hexagrid_content_type',
                'limit'        => '_hexagrid_query_limit',
                'columns'      => '_hexagrid_columns',
                'category'     => '_hexagrid_categories', // This is an array
                'ids'          => '_hexagrid_include_ids',
                'exclude_ids'  => '_hexagrid_exclude_ids',
                'orderby'      => '_hexagrid_orderby',
                'order'        => '_hexagrid_order',
                'theme_color'     => '_hexagrid_theme_color',
                'slider_nav'      => '_hexagrid_slider_nav',
                'slider_dots'     => '_hexagrid_slider_dots',
                'slider_autoplay' => '_hexagrid_slider_autoplay',
            ];

            foreach ( $meta_map as $att_key => $meta_key ) {
                $value = get_post_meta( $preset_id, $meta_key, true );
                if ( ! empty( $value ) ) {
                    // Handle array for category logic if passed to shortcode
                    // If shortcode handles category as string (commas), we might need to implode if it's an array
                    if ( is_array( $value ) && 'category' === $att_key ) {
                         // Query Builder handles array directly, but shortcode_atts expects all to be scaler ideally. 
                         // But we can pass array if QueryBuilder handles it.
                         // For simplicity and compatibility, let's keep it as is, but be mindful of shortcode_atts
                    }
                    $atts[ $att_key ] = $value;
                }
            }
        }

        $atts = shortcode_atts( [
            'layout'          => 'grid',
            'style'           => 'product-grid-1',
            'content_type'    => 'product',
            'limit'           => 12,
            'columns'         => 3,
            'category'        => '',
            'ids'             => '',
            'exclude_ids'     => '',
            'orderby'         => 'date',
            'order'           => 'DESC',
            'theme_color'     => '#3291b6',
            'slider_nav'      => 'yes',
            'slider_dots'     => 'no',
            'slider_autoplay' => 'no',
            'preset_id'       => '',
        ], $atts, 'hexagrid_product_showcase' );

        // 1. Build Query
        $query_builder = new Query_Builder();
        $query_builder->set_limit( $atts['limit'] )
                      ->set_order( $atts['orderby'], $atts['order'] );

        if ( ! empty( $atts['category'] ) ) {
            $query_builder->set_category( $atts['category'] );
        }

        if ( ! empty( $atts['ids'] ) ) {
             // If array, it's from meta; if string, it's from shortcode param
            $query_builder->set_ids( $atts['ids'] );
        }
        
        if ( ! empty( $atts['exclude_ids'] ) ) {
            $query_builder->set_exclude_ids( $atts['exclude_ids'] );
        }

        $query = $query_builder->get_query();

        // 2. Generate Unique ID for Scoping
        $unique_id = 'hexagrid-preset-' . $preset_id . '-' . uniqid();
        $atts['wrapper_id'] = $unique_id;

        // 3. Render Layout
        $renderer = null;
        switch ( $atts['layout'] ) {
            case 'list':
                $renderer = new List_Layout();
                break;
            case 'slider':
                $renderer = new Slider_Layout();
                break;
            case 'table':
                $renderer = new Table_Layout();
                break;
            case 'grid':
            default:
                $renderer = new Grid_Layout();
                break;
        }

        // 3. Render
        if ( $renderer ) {
            return $renderer->render( $query, $atts );
        }

        return '';
    }
}
