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
     * @var \Addons_Kit_Settings_Builder
     */
    private $builder;

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
        if ( 'hexagrid_show_preset' === $post_type ) {
            // Instantiate builder merely to access its enqueue method (prefix irrelevant here)
            // Ideally we might just use a static method or instantiate once, but this is fine.
            $builder = new \Addons_Kit_Settings_Builder();
            $builder->enqueue_assets();

            $plugin_root_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
            
            // Layout specific styles (wrapper, shortcode, variations)
            wp_enqueue_style( 'hexagrid-admin-style', $plugin_root_url . 'assets/admin/css/admin.css', [], '1.0.0' );
            
            // Plugin specific logic (variations, tabs)
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
            __( 'Customize your showcase preset', 'hexa-grid-product-showcase' ),
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

        $theme_color  = get_post_meta( $post->ID, '_hexagrid_theme_color', true ) ?: '#3291b6';
        $content_type = get_post_meta( $post->ID, '_hexagrid_content_type', true ) ?: 'product';

        // Slider specific settings
        $slider_nav      = get_post_meta( $post->ID, '_hexagrid_slider_nav', true ) !== 'no' ? 'yes' : 'no'; // Default yes
        $slider_dots     = get_post_meta( $post->ID, '_hexagrid_slider_dots', true ) === 'yes' ? 'yes' : 'no'; // Default no
        $slider_autoplay = get_post_meta( $post->ID, '_hexagrid_slider_autoplay', true ) === 'yes' ? 'yes' : 'no'; // Default no

        $plugin_root_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
        $assets_url      = $plugin_root_url . 'assets/admin/icons/';

        // Initialize Settings Builder with Prefix
        $builder = new \Addons_Kit_Settings_Builder( 'hexagrid' );

        ?>
        <div class="hexagrid-settings-meta-box-wrapper">
            
            <div class="hexagrid-settings-meta-box-content">
                
                <!-- Section 1: Layout Settings -->
                <div class="hexagrid-settings-section">
                    <div class="hexagrid-settings-section-header">
                        <div class="hexagrid-settings-section-icon">
                            <span class="dashicons dashicons-grid-view"></span>
                        </div>
                        <div class="hexagrid-settings-section-info">
                            <h3><?php esc_html_e( 'Layout Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Configure content display and layout options', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-settings-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>
                    
                    <div class="hexagrid-settings-section-body">
                        <!-- Content Type Selector -->
                        <?php 
                        $builder->render_card_selector([
                            'id'         => 'content_type',
                            'label'      => __( 'Content Type', 'hexa-grid-product-showcase' ),
                            'value'      => $content_type,
                            'type'       => 'radio',
                            'layout'     => 'list', // content type uses list/row style
                            'grid_min_width' => '300px',
                            'assets_url' => $assets_url,
                            'options'    => [
                                'product' => [
                                    'label' => __( 'Products', 'hexa-grid-product-showcase' ),
                                    'icon' => 'product.svg',
                                    'desc' => __( 'Display WooCommerce products', 'hexa-grid-product-showcase' )
                                ],
                                'category' => [
                                    'label' => __( 'Categories', 'hexa-grid-product-showcase' ),
                                    'icon' => 'category.svg',
                                    'desc' => __( 'Display product categories', 'hexa-grid-product-showcase' )
                                ]
                            ]
                        ]);
                        
                        // Layout Type Selector
                        $builder->render_card_selector([
                            'id'         => 'layout_type',
                            'label'      => __( 'Layout Type', 'hexa-grid-product-showcase' ),
                            'value'      => $layout,
                            'type'       => 'radio',
                            'layout'     => 'grid',
                            'grid_columns' => 7,
                            'assets_url' => $assets_url,
                            'options'    => [
                                'grid'   => [ 'label' => __( 'Grid', 'hexa-grid-product-showcase' ), 'icon' => 'grid.svg' ],
                                'list'   => [ 'label' => __( 'List', 'hexa-grid-product-showcase' ), 'icon' => 'list.svg' ],
                                'slider' => [ 'label' => __( 'Carousel', 'hexa-grid-product-showcase' ), 'icon' => 'slider.svg' ],
                                'table'  => [ 'label' => __( 'Table', 'hexa-grid-product-showcase' ), 'icon' => 'table.svg' ]
                            ]
                        ]);
                        
                        // Layout Variations - Grouped by Content Type + Layout Type
                        $all_variations = [
                            // Product variations
                            'product-grid' => [
                                'product-grid-1' => [ 'label' => __( 'Product Grid Modern', 'hexa-grid-product-showcase' ), 'skeleton' => 'skeleton-1.svg' ],
                                'product-grid-2' => [ 'label' => __( 'Product Grid Classic', 'hexa-grid-product-showcase' ), 'skeleton' => 'skeleton-2.svg' ],
                            ],
                            'product-list' => [
                                'product-list-1' => [ 'label' => __( 'Product List Minimal', 'hexa-grid-product-showcase' ), 'skeleton' => 'list.svg' ]
                            ],
                            'product-slider' => [
                                'product-slider-1' => [ 'label' => __( 'Product Carousel Standard', 'hexa-grid-product-showcase' ), 'skeleton' => 'slider.svg' ],
                            ],
                            'product-table' => [
                                'product-table-1' => [ 'label' => __( 'Product Table Simple', 'hexa-grid-product-showcase' ), 'skeleton' => 'table.svg' ],
                            ],
                            
                            // Category variations
                            'category-grid' => [
                                'category-grid-1' => [ 'label' => __( 'Category Grid Modern', 'hexa-grid-product-showcase' ), 'skeleton' => 'skeleton-1.svg' ],
                            ],
                            'category-list' => [
                                'category-list-1' => [ 'label' => __( 'Category List Minimal', 'hexa-grid-product-showcase' ), 'skeleton' => 'list.svg' ],
                            ],
                            'category-slider' => [
                                'category-slider-1' => [ 'label' => __( 'Category Carousel Standard', 'hexa-grid-product-showcase' ), 'skeleton' => 'slider.svg' ],
                            ],
                            'category-table' => [
                                'category-table-1' => [ 'label' => __( 'Category Table Simple', 'hexa-grid-product-showcase' ), 'skeleton' => 'table.svg' ],
                            ],
                        ];
                        
                        $builder->render_grouped_card_selector([
                            'id'            => 'layout_style',
                            'label'         => __( 'Layout Style', 'hexa-grid-product-showcase' ),
                            'value'         => $style,
                            'parent_fields' => ['content_type', 'layout_type'], // Multiple parent fields
                            'groups'        => $all_variations,
                            'type'          => 'radio',
                            'layout'        => 'grid',
                            'grid_columns'  => 4,
                            'assets_url'    => $assets_url,
                            'no_match_text' => __( 'No variations available for this combination.', 'hexa-grid-product-showcase' )
                        ]);
                        ?>

                        <div class="hexagrid-settings-row">
                            <div class="hexagrid-settings-col-6">
                                <?php 
                                $builder->render_select_field([
                                    'id'      => 'columns',
                                    'label'   => __( 'Columns', 'hexa-grid-product-showcase' ),
                                    'value'   => $columns,
                                    'options' => [
                                        '1' => '1 ' . __( 'Column', 'hexa-grid-product-showcase' ),
                                        '2' => '2 ' . __( 'Columns', 'hexa-grid-product-showcase' ),
                                        '3' => '3 ' . __( 'Columns', 'hexa-grid-product-showcase' ),
                                        '4' => '4 ' . __( 'Columns', 'hexa-grid-product-showcase' ),
                                    ],
                                    // Dependency: Show if Grid or Slider
                                    'dependency' => [
                                        'id' => 'layout_type',
                                        'value' => ['grid', 'slider']
                                    ]
                                ]); 
                                ?>
                            </div>
                        </div>

                        <!-- Slider Specific Settings Wrapper -->
                        <!-- 
                            Note: We use a manual wrapper for the group dependency. 
                            We must include the full ID prefix in the data-dependency here since we are writing raw HTML.
                        -->
                         <div id="hexagrid-slider-settings-wrapper" style="display:none; margin-top: 20px; border-top: 1px solid var(--aksbuilder-border); padding-top: 20px;"
                              data-dependency='{"id":"hexagrid_layout_type","value":"slider"}'>
                            <h4 style="margin-top:0; margin-bottom:15px; color: var(--aksbuilder-text); font-weight: 600;"><?php esc_html_e( 'Slider Configuration', 'hexa-grid-product-showcase' ); ?></h4>
                            
                            <div class="hexagrid-settings-row">
                                <div class="hexagrid-settings-col-4">
                                    <?php $builder->render_switcher_field([ 'id' => 'slider_nav', 'label' => __( 'Navigation', 'hexa-grid-product-showcase' ), 'value' => $slider_nav ]); ?>
                                </div>
                                <div class="hexagrid-settings-col-4">
                                    <?php $builder->render_switcher_field([ 'id' => 'slider_dots', 'label' => __( 'Pagination Dots', 'hexa-grid-product-showcase' ), 'value' => $slider_dots ]); ?>
                                </div>
                                <div class="hexagrid-settings-col-4">
                                    <?php $builder->render_switcher_field([ 'id' => 'slider_autoplay', 'label' => __( 'Auto Play', 'hexa-grid-product-showcase' ), 'value' => $slider_autoplay ]); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Section 2: Query Settings -->
                <div class="hexagrid-settings-section">
                    <div class="hexagrid-settings-section-header">
                        <div class="hexagrid-settings-section-icon">
                            <span class="dashicons dashicons-filter"></span>
                        </div>
                        <div class="hexagrid-settings-section-info">
                            <h3><?php esc_html_e( 'Query Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Filter and sort your products', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-settings-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>

                    <div class="hexagrid-settings-section-body">
                        <?php 
                        $builder->render_text_field([
                            'id'         => 'query_limit',
                            'label'      => __( 'Product Limit', 'hexa-grid-product-showcase' ),
                            'value'      => $limit,
                            'type'       => 'number',
                            'input_attr' => 'min="1"'
                        ]);
                        
                        $builder->render_text_field([
                            'id'          => 'exclude_ids',
                            'label'       => __( 'Exclude Products (IDs)', 'hexa-grid-product-showcase' ),
                            'value'       => $exclude_ids,
                            'placeholder' => 'e.g. 101, 105, 200',
                            'desc'        => __( 'Enter product IDs to exclude', 'hexa-grid-product-showcase' )
                        ]);
                        ?>

                        <div class="hexagrid-settings-row">
                            <div class="hexagrid-settings-col-6">
                                <?php 
                                $builder->render_select_field([
                                    'id'      => 'orderby',
                                    'label'   => __( 'Order By', 'hexa-grid-product-showcase' ),
                                    'value'   => $orderby,
                                    'options' => [
                                        'date' => __( 'Date', 'hexa-grid-product-showcase' ),
                                        'price' => __( 'Price', 'hexa-grid-product-showcase' ),
                                        'ID' => __( 'ID', 'hexa-grid-product-showcase' ),
                                        'title' => __( 'Title', 'hexa-grid-product-showcase' ),
                                        'popularity' => __( 'Popularity (Sales)', 'hexa-grid-product-showcase' ),
                                    ]
                                ]); 
                                ?>
                            </div>

                            <div class="hexagrid-settings-col-6">
                                <?php 
                                $builder->render_select_field([
                                    'id'      => 'order',
                                    'label'   => __( 'Order', 'hexa-grid-product-showcase' ),
                                    'value'   => $order,
                                    'options' => [
                                        'DESC' => __( 'Descending (Z-A, Newest)', 'hexa-grid-product-showcase' ),
                                        'ASC' => __( 'Ascending (A-Z, Oldest)', 'hexa-grid-product-showcase' ),
                                    ]
                                ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Style Settings -->
                <div class="hexagrid-settings-section">
                    <div class="hexagrid-settings-section-header">
                        <div class="hexagrid-settings-section-icon">
                            <span class="dashicons dashicons-art"></span>
                        </div>
                        <div class="hexagrid-settings-section-info">
                            <h3><?php esc_html_e( 'Style Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Customize appearance and colors', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-settings-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>

                    <div class="hexagrid-settings-section-body">
                        <?php 
                        $builder->render_color_picker([
                            'id'    => 'theme_color',
                            'label' => __( 'Theme Color', 'hexa-grid-product-showcase' ),
                            'value' => $theme_color,
                            'desc'  => __( 'Select your primary brand color', 'hexa-grid-product-showcase' )
                        ]); 
                        ?>
                    </div>
                </div>

                <?php if ( $post->ID ) : ?>
                    <div class="hexagrid-settings-section">
                        <div class="hexagrid-settings-section-body" style="border-top:none;">
                            <label style="font-weight:600; font-size:14px; margin-bottom:10px; display:block;"><?php esc_html_e( 'Shortcode', 'hexa-grid-product-showcase' ); ?></label>
                            <div class="hexagrid-settings-shortcode-container">
                                <code id="hexagrid-shortcode-text">[hexagrid_product_showcase preset_id="<?php echo esc_attr( $post->ID ); ?>"]</code>
                                <button type="button" class="button hexagrid-settings-copy-btn" data-clipboard-target="#hexagrid-shortcode-text">
                                    <span class="dashicons dashicons-admin-page"></span> <?php esc_html_e( 'Copy', 'hexa-grid-product-showcase' ); ?>
                                </button>
                            </div>
                             <p class="description" style="margin-top:10px; color:#666;"><?php esc_html_e( 'Use this shortcode in your pages or posts', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get settings map for the showcase.
     * Centralized definition of all fields, types, and sanitization.
     */
    private function get_settings_map() {
        return [
            // Layout Settings
            'hexagrid_content_type' => [ 'sanitize' => 'sanitize_text_field' ],
            'hexagrid_layout_type'  => [ 'sanitize' => 'sanitize_text_field', 'default' => 'grid' ],
            'hexagrid_layout_style' => [ 'sanitize' => 'sanitize_text_field', 'default' => 'grid-1' ],
            'hexagrid_columns'      => [ 'sanitize' => 'intval', 'default' => 3 ],
            
            // Slider Specific
            'hexagrid_slider_nav'   => [ 'type' => 'checkbox', 'default' => 'yes' ],
            'hexagrid_slider_dots'  => [ 'type' => 'checkbox', 'default' => 'no' ],
            'hexagrid_slider_autoplay' => [ 'type' => 'checkbox', 'default' => 'no' ],

            // Query Settings
            'hexagrid_query_limit'  => [ 'sanitize' => 'intval', 'default' => 12 ],
            'hexagrid_include_ids'  => [ 'sanitize' => 'sanitize_text_field' ],
            'hexagrid_exclude_ids'  => [ 'sanitize' => 'sanitize_text_field' ],
            'hexagrid_orderby'      => [ 'sanitize' => 'sanitize_text_field', 'default' => 'date' ],
            'hexagrid_order'        => [ 'sanitize' => 'sanitize_text_field', 'default' => 'DESC' ],

            // Style Settings
            'hexagrid_theme_color'  => [ 'sanitize' => 'sanitize_hex_color', 'default' => '#3291b6' ],
        ];
    }

    /**
     * Save meta box data.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta_box_data( $post_id ) {
        // Security checks
        if ( ! isset( $_POST['hexagrid_showcase_settings_nonce'] ) || 
             ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hexagrid_showcase_settings_nonce'] ) ), 'hexagrid_save_showcase_settings' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Processing Loop
        $settings = $this->get_settings_map();

        foreach ( $settings as $field_key => $config ) {
            $meta_key = '_' . $field_key; // Standardize meta key as _field_name
            $type     = isset( $config['type'] ) ? $config['type'] : 'text';
            
            if ( $type === 'checkbox' ) {
                // Checkbox Logic: if set in POST, value is 'yes', otherwise 'no'
                $value = isset( $_POST[ $field_key ] ) ? 'yes' : 'no';
                update_post_meta( $post_id, $meta_key, $value );
            } else {
                // Standard Input Logic
                if ( isset( $_POST[ $field_key ] ) ) {
                    $sanitize_func = isset( $config['sanitize'] ) ? $config['sanitize'] : 'sanitize_text_field';
                    $raw_value     = wp_unslash( $_POST[ $field_key ] );
                    
                    // Apply sanitization
                    if ( function_exists( $sanitize_func ) ) {
                        $value = $sanitize_func( $raw_value );
                    } else {
                        $value = sanitize_text_field( $raw_value );
                    }

                    update_post_meta( $post_id, $meta_key, $value );
                }
            }
        }
    }
}
