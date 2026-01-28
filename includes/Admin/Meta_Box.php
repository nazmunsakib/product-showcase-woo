<?php

namespace HexaGrid\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Meta_Box
 *
 * Handles configuration meta boxes for Presets.
 */
class Meta_Box {

    /**
     * Initialize hooks.
     */
    public function init() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_meta_box_data' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        

    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets() {
        global $post_type;
        if ( 'product_show_preset' === $post_type ) {
            // WP Color Picker
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );



            $plugin_root_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
            wp_enqueue_style( 'hexagrid-admin-style', $plugin_root_url . 'assets/admin/css/admin.css', [], '1.0.0' );
            wp_enqueue_script( 'hexagrid-admin-script', $plugin_root_url . 'assets/admin/js/admin.js', [ 'jquery', 'wp-color-picker' ], '1.0.0', true );
            
            wp_localize_script( 'hexagrid-admin-script', 'hexagridAdmin', [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'hexagrid_admin_nonce' ),
            ]);
        }
    }



    /**
     * Add meta box.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'hexagrid_showcase_settings',
            __( 'HexaGrid Showcase Settings', 'hexa-grid-product-showcase' ),
            [ $this, 'render_meta_box' ],
            'hexagrid_show_preset',
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box.
     *
     * @param \WP_Post $post Post object.
     */
    public function render_meta_box( $post ) {
        // Add nonces for security
        wp_nonce_field( 'hexagrid_save_showcase_settings', 'hexagrid_showcase_settings_nonce' );

        // Retrieve existing values
        $layout       = get_post_meta( $post->ID, '_hexagrid_layout_type', true ) ?: 'grid';
        $style        = get_post_meta( $post->ID, '_hexagrid_layout_style', true ) ?: 'layout-1';
        $columns      = get_post_meta( $post->ID, '_hexagrid_columns', true ) ?: 3;
        $limit        = get_post_meta( $post->ID, '_hexagrid_query_limit', true ) ?: 12;
        
        $include_ids  = get_post_meta( $post->ID, '_hexagrid_include_ids', true );
        $exclude_ids  = get_post_meta( $post->ID, '_hexagrid_exclude_ids', true );

        $orderby      = get_post_meta( $post->ID, '_hexagrid_orderby', true ) ?: 'date';
        $order        = get_post_meta( $post->ID, '_hexagrid_order', true ) ?: 'DESC';

        $theme_color  = get_post_meta( $post->ID, '_hexagrid_theme_color', true ) ?: '#0984e3';

        

        ?>
        <div class="hexagrid-meta-box-wrapper">
            <div class="hexagrid-meta-header">
                <span class="dashicons dashicons-sliders"></span>
                <h2><?php esc_html_e( 'Showcase Settings', 'hexa-grid-product-showcase' ); ?></h2>
            </div>
            
            <div class="hexagrid-meta-box-content">
                
                <!-- Section 1: Layout Settings -->
                <div class="hexagrid-section">
                    <h3 class="hexagrid-section-title">
                        <span class="hexagrid-step-number">1</span> <?php esc_html_e( 'Layout Settings', 'hexa-grid-product-showcase' ); ?>
                    </h3>
                    
                    <p class="hexagrid-form-group">
                        <label for="hexagrid_layout_type"><?php esc_html_e( 'Layout Type', 'hexa-grid-product-showcase' ); ?></label>
                        <select name="hexagrid_layout_type" id="hexagrid_layout_type" class="widefat">
                            <option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Grid', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'List', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="slider" <?php selected( $layout, 'slider' ); ?>><?php esc_html_e( 'Carousel (Slider)', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="table" <?php selected( $layout, 'table' ); ?>><?php esc_html_e( 'Table', 'hexa-grid-product-showcase' ); ?></option>
                        </select>
                    </p>

                     <p class="hexagrid-form-group">
                        <label for="hexagrid_layout_style"><?php esc_html_e( 'Layout Variation', 'hexa-grid-product-showcase' ); ?></label>
                        <select name="hexagrid_layout_style" id="hexagrid_layout_style" class="widefat">
                            <option value="layout-1" <?php selected( $style, 'layout-1' ); ?>><?php esc_html_e( 'Style 1 (Modern)', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="layout-2" <?php selected( $style, 'layout-2' ); ?>><?php esc_html_e( 'Style 2 (Classic)', 'hexa-grid-product-showcase' ); ?></option>
                        </select>
                    </p>

                    <p class="hexagrid-form-group">
                        <label for="hexagrid_columns"><?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></label>
                        <select name="hexagrid_columns" id="hexagrid_columns" class="widefat">
                            <option value="1" <?php selected( $columns, 1 ); ?>>1 <?php esc_html_e( 'Column', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="2" <?php selected( $columns, 2 ); ?>>2 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="3" <?php selected( $columns, 3 ); ?>>3 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                            <option value="4" <?php selected( $columns, 4 ); ?>>4 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                        </select>
                    </p>

                    <p class="hexagrid-form-group">
                        <label for="hexagrid_query_limit"><?php esc_html_e( 'Product Limit', 'hexa-grid-product-showcase' ); ?></label>
                        <input type="number" name="hexagrid_query_limit" id="hexagrid_query_limit" value="<?php echo esc_attr( $limit ); ?>" class="widefat" min="1">
                    </p>
                </div>

                <!-- Section 2: Query Settings -->
                <div class="hexagrid-section">
                    <h3 class="hexagrid-section-title">
                        <span class="hexagrid-step-number">2</span> <?php esc_html_e( 'Query Settings', 'hexa-grid-product-showcase' ); ?>
                    </h3>

                    <p class="hexagrid-form-group">
                        <label for="hexagrid_include_ids"><?php esc_html_e( 'Include Products (IDs)', 'hexa-grid-product-showcase' ); ?></label>
                        <input type="text" name="hexagrid_include_ids" id="hexagrid_include_ids" value="<?php echo esc_attr( $include_ids ); ?>" class="widefat" placeholder="e.g. 101, 105, 200">
                    </p>

                     <p class="hexagrid-form-group">
                        <label for="hexagrid_exclude_ids"><?php esc_html_e( 'Exclude Products (IDs)', 'hexa-grid-product-showcase' ); ?></label>
                        <input type="text" name="hexagrid_exclude_ids" id="hexagrid_exclude_ids" value="<?php echo esc_attr( $exclude_ids ); ?>" class="widefat" placeholder="e.g. 101, 105, 200">
                    </p>

                    <div class="hexagrid-row">
                        <p class="hexagrid-form-group hexagrid-col-6">
                            <label for="hexagrid_orderby"><?php esc_html_e( 'Order By', 'hexa-grid-product-showcase' ); ?></label>
                            <select name="hexagrid_orderby" id="hexagrid_orderby" class="widefat">
                                <option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'hexa-grid-product-showcase' ); ?></option>
                                <option value="price" <?php selected( $orderby, 'price' ); ?>><?php esc_html_e( 'Price', 'hexa-grid-product-showcase' ); ?></option>
                                <option value="ID" <?php selected( $orderby, 'ID' ); ?>><?php esc_html_e( 'ID', 'hexa-grid-product-showcase' ); ?></option>
                                <option value="title" <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'hexa-grid-product-showcase' ); ?></option>
                                <option value="popularity" <?php selected( $orderby, 'popularity' ); ?>><?php esc_html_e( 'Popularity (Sales)', 'hexa-grid-product-showcase' ); ?></option>
                            </select>
                        </p>

                         <p class="hexagrid-form-group hexagrid-col-6">
                            <label for="hexagrid_order"><?php esc_html_e( 'Order', 'hexa-grid-product-showcase' ); ?></label>
                            <select name="hexagrid_order" id="hexagrid_order" class="widefat">
                                <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending (Z-A, Newest)', 'hexa-grid-product-showcase' ); ?></option>
                                <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending (A-Z, Oldest)', 'hexa-grid-product-showcase' ); ?></option>
                            </select>
                        </p>
                    </div>
                </div>

                <!-- Section 3: Style Settings -->
                <div class="hexagrid-section">
                    <h3 class="hexagrid-section-title">
                        <span class="hexagrid-step-number">3</span> <?php esc_html_e( 'Style Settings', 'hexa-grid-product-showcase' ); ?>
                    </h3>

                    <p class="hexagrid-form-group">
                        <label for="hexagrid_theme_color"><?php esc_html_e( 'Theme Color', 'hexa-grid-product-showcase' ); ?></label>
                        <input type="text" name="hexagrid_theme_color" id="hexagrid_theme_color" value="<?php echo esc_attr( $theme_color ); ?>" class="hexagrid-color-picker">
                    </p>
                </div>

                <?php if ( $post->ID ) : ?>
                    <div class="hexagrid-shortcode-preview-wrapper">
                        <label><?php esc_html_e( 'Shortcode', 'hexa-grid-product-showcase' ); ?></label>
                        <div class="hexagrid-shortcode-container">
                            <code id="hexagrid-shortcode-text">[hexagrid_product_showcase preset_id="<?php echo esc_attr( $post->ID ); ?>"]</code>
                            <button type="button" class="button hexagrid-copy-btn" data-clipboard-target="#hexagrid-shortcode-text">
                                <span class="dashicons dashicons-admin-page"></span> <?php esc_html_e( 'Copy', 'hexa-grid-product-showcase' ); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['hexagrid_showcase_settings_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hexagrid_showcase_settings_nonce'] ) ), 'hexagrid_save_showcase_settings' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Layout Tab
        if ( isset( $_POST['hexagrid_layout_type'] ) ) {
            update_post_meta( $post_id, '_hexagrid_layout_type', sanitize_text_field( wp_unslash( $_POST['hexagrid_layout_type'] ) ) );
        }
        if ( isset( $_POST['hexagrid_layout_style'] ) ) {
            update_post_meta( $post_id, '_hexagrid_layout_style', sanitize_text_field( wp_unslash( $_POST['hexagrid_layout_style'] ) ) );
        }
        if ( isset( $_POST['hexagrid_columns'] ) ) {
            update_post_meta( $post_id, '_hexagrid_columns', intval( wp_unslash( $_POST['hexagrid_columns'] ) ) );
        }
        if ( isset( $_POST['hexagrid_query_limit'] ) ) {
            update_post_meta( $post_id, '_hexagrid_query_limit', intval( wp_unslash( $_POST['hexagrid_query_limit'] ) ) );
        }

        // Query Tab
        if ( isset( $_POST['hexagrid_include_ids'] ) ) {
            update_post_meta( $post_id, '_hexagrid_include_ids', sanitize_text_field( wp_unslash( $_POST['hexagrid_include_ids'] ) ) );
        }
        if ( isset( $_POST['hexagrid_exclude_ids'] ) ) {
            update_post_meta( $post_id, '_hexagrid_exclude_ids', sanitize_text_field( wp_unslash( $_POST['hexagrid_exclude_ids'] ) ) );
        }
        


        if ( isset( $_POST['hexagrid_orderby'] ) ) {
            update_post_meta( $post_id, '_hexagrid_orderby', sanitize_text_field( wp_unslash( $_POST['hexagrid_orderby'] ) ) );
        }
        if ( isset( $_POST['hexagrid_order'] ) ) {
            update_post_meta( $post_id, '_hexagrid_order', sanitize_text_field( wp_unslash( $_POST['hexagrid_order'] ) ) );
        }

        // Style Tab
        if ( isset( $_POST['hexagrid_theme_color'] ) ) {
            update_post_meta( $post_id, '_hexagrid_theme_color', sanitize_hex_color( wp_unslash( $_POST['hexagrid_theme_color'] ) ) );
        }

    }
}
