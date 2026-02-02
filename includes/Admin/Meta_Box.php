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
        if ( 'hexagrid_show_preset' === $post_type ) {
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

        $theme_color  = get_post_meta( $post->ID, '_hexagrid_theme_color', true ) ?: '#0984e3';
        $content_type = get_post_meta( $post->ID, '_hexagrid_content_type', true ) ?: 'product';

        

        ?>
        
        <div class="hexagrid-meta-box-wrapper">
            <!-- Main Header -->
            <!-- <div class="hexagrid-main-header">
                <h2><?php esc_html_e( 'Customize your showcase', 'hexa-grid-product-showcase' ); ?></h2>
                <p><?php esc_html_e( 'Configure your product showcase display', 'hexa-grid-product-showcase' ); ?></p>
            </div> -->
            
            <div class="hexagrid-meta-box-content">
                
                <!-- Section 1: Layout Settings -->
                <div class="hexagrid-section">
                    <div class="hexagrid-section-header">
                        <div class="hexagrid-section-icon">
                            <span class="dashicons dashicons-grid-view"></span>
                        </div>
                        <div class="hexagrid-section-info">
                            <h3><?php esc_html_e( 'Layout Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Configure content display and layout options', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>
                    
                    <div class="hexagrid-section-body">
                        <!-- Content Type Selector -->
                        <div class="hexagrid-form-group hexagrid-content-type-wrapper">
                            <label class="hexagrid-content-type-label"><?php esc_html_e( 'Content Type', 'hexa-grid-product-showcase' ); ?></label>
                            <div class="hexagrid-content-type-selector">
                                <?php
                                $content_types = [
                                    'product' => [
                                        'label' => __( 'Products', 'hexa-grid-product-showcase' ),
                                        'icon' => 'product.svg',
                                        'description' => __( 'Display WooCommerce products', 'hexa-grid-product-showcase' )
                                    ],
                                    'category' => [
                                        'label' => __( 'Categories', 'hexa-grid-product-showcase' ),
                                        'icon' => 'category.svg',
                                        'description' => __( 'Display product categories', 'hexa-grid-product-showcase' )
                                    ]
                                ];
                                
                                $plugin_root_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
                                
                                foreach ( $content_types as $value => $data ) :
                                    $checked = checked( $content_type, $value, false );
                                    $icon_url = $plugin_root_url . 'assets/admin/icons/' . $data['icon'];
                                ?>
                                    <label class="hexagrid-content-type-option">
                                        <input type="radio" name="hexagrid_content_type" value="<?php echo esc_attr( $value ); ?>" <?php echo $checked; ?>>
                                        <div class="hexagrid-content-type-card">
                                            <div class="hexagrid-content-type-icon">
                                                <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                            </div>
                                            <div class="hexagrid-content-type-info">
                                                <span class="hexagrid-content-type-title"><?php echo esc_html( $data['label'] ); ?></span>
                                                <span class="hexagrid-content-type-desc"><?php echo esc_html( $data['description'] ); ?></span>
                                            </div>
                                            <span class="hexagrid-content-type-radio">
                                                <span class="hexagrid-radio-dot"></span>
                                            </span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="hexagrid-form-group">
                            <label><?php esc_html_e( 'Layout Type', 'hexa-grid-product-showcase' ); ?></label>
                            <div class="hexagrid-layout-type-grid">
                                <?php
                                $layout_types = [
                                    'grid' => [
                                        'label' => __( 'Grid', 'hexa-grid-product-showcase' ),
                                        'icon' => 'grid.svg'
                                    ],
                                    'list' => [
                                        'label' => __( 'List', 'hexa-grid-product-showcase' ),
                                        'icon' => 'list.svg'
                                    ],
                                    'slider' => [
                                        'label' => __( 'Carousel', 'hexa-grid-product-showcase' ),
                                        'icon' => 'slider.svg'
                                    ],
                                    'table' => [
                                        'label' => __( 'Table', 'hexa-grid-product-showcase' ),
                                        'icon' => 'table.svg'
                                    ]
                                ];
                                
                                foreach ( $layout_types as $value => $data ) :
                                    $checked = checked( $layout, $value, false );
                                    $icon_url = $plugin_root_url . 'assets/admin/icons/' . $data['icon'];
                                ?>
                                    <label class="hexagrid-layout-option">
                                        <input type="radio" name="hexagrid_layout_type" value="<?php echo esc_attr( $value ); ?>" <?php echo $checked; ?>>
                                        <div class="hexagrid-layout-card">
                                            <div class="hexagrid-layout-icon">
                                                <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                            </div>
                                            <span class="hexagrid-layout-label"><?php echo esc_html( $data['label'] ); ?></span>
                                            <span class="hexagrid-layout-checkmark">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="hexagrid-form-group">
                            <label><?php esc_html_e( 'Layout Style', 'hexa-grid-product-showcase' ); ?></label>
                            
                            <?php
                            // Define variations for each layout type
                            $all_variations = [
                                'grid' => [
                                    'grid-1' => [
                                        'label'    => __( 'Grid Modern', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'skeleton-1.svg'
                                    ],
                                    'grid-2' => [
                                        'label'    => __( 'Grid Classic', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'skeleton-2.svg'
                                    ],
                                ],
                                'list' => [
                                    'list-1' => [
                                        'label'    => __( 'List Minimal', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'list.svg' 
                                    ],
                                    'list-2' => [
                                        'label'    => __( 'List Detailed', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'list.svg' 
                                    ],
                                ],
                                'slider' => [
                                    'slider-1' => [
                                        'label'    => __( 'Carousel Standard', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'slider.svg' 
                                    ],
                                    'slider-2' => [
                                        'label'    => __( 'Carousel Coverflow', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'slider.svg' 
                                    ],
                                ],
                                'table' => [
                                    'table-1' => [
                                        'label'    => __( 'Table Simple', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'table.svg' 
                                    ],
                                    'table-2' => [
                                        'label'    => __( 'Table Advanced', 'hexa-grid-product-showcase' ),
                                        'skeleton' => 'table.svg' 
                                    ],
                                ],
                            ];
                            ?>

                            <!-- Loop through each Layout Type to create its variation group -->
                            <?php foreach ( $all_variations as $parent_layout => $variations ) : ?>
                                <div class="hexagrid-layout-variation-group" data-parent-layout="<?php echo esc_attr( $parent_layout ); ?>" style="display: none;">
                                    <div class="hexagrid-layout-variation-grid">
                                        <?php
                                        foreach ( $variations as $value => $data ) :
                                            $checked = checked( $style, $value, false );
                                            $skeleton_url = $plugin_root_url . 'assets/admin/icons/' . $data['skeleton'];
                                        ?>
                                            <label class="hexagrid-variation-option">
                                                <input type="radio" name="hexagrid_layout_style" value="<?php echo esc_attr( $value ); ?>" <?php echo $checked; ?>>
                                                <div class="hexagrid-variation-card">
                                                    <div class="hexagrid-variation-preview">
                                                        <img src="<?php echo esc_url( $skeleton_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                                    </div>
                                                    <span class="hexagrid-variation-label"><?php echo esc_html( $data['label'] ); ?></span>
                                                    <span class="hexagrid-variation-checkmark">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                            <circle cx="12" cy="12" r="10"/>
                                                            <path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="hexagrid-no-variations" style="display:none; color: #666; font-style: italic; padding: 10px;">
                                <?php esc_html_e( 'No variations available for this layout.', 'hexa-grid-product-showcase' ); ?>
                            </div>

                        </div>
                        
                        <div class="hexagrid-row">
                            <p class="hexagrid-form-group hexagrid-col-6">
                                <label for="hexagrid_columns"><?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></label>
                                <select name="hexagrid_columns" id="hexagrid_columns" class="widefat">
                                    <option value="1" <?php selected( $columns, 1 ); ?>>1 <?php esc_html_e( 'Column', 'hexa-grid-product-showcase' ); ?></option>
                                    <option value="2" <?php selected( $columns, 2 ); ?>>2 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                                    <option value="3" <?php selected( $columns, 3 ); ?>>3 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                                    <option value="4" <?php selected( $columns, 4 ); ?>>4 <?php esc_html_e( 'Columns', 'hexa-grid-product-showcase' ); ?></option>
                                </select>
                            </p>
                            
                        </div>
                    </div>
                </div>

                <!-- Section 2: Query Settings -->
                <div class="hexagrid-section">
                     <div class="hexagrid-section-header">
                        <div class="hexagrid-section-icon">
                            <span class="dashicons dashicons-filter"></span>
                        </div>
                        <div class="hexagrid-section-info">
                            <h3><?php esc_html_e( 'Query Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Filter and sort your products', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>

                    <div class="hexagrid-section-body">
                        <p class="hexagrid-form-group">
                            <label for="hexagrid_query_limit"><?php esc_html_e( 'Product Limit', 'hexa-grid-product-showcase' ); ?></label>
                            <input type="number" name="hexagrid_query_limit" id="hexagrid_query_limit" value="<?php echo esc_attr( $limit ); ?>" class="widefat" min="1">
                        </p>

                        <p class="hexagrid-form-group">
                            <label for="hexagrid_exclude_ids"><?php esc_html_e( 'Exclude Products (IDs)', 'hexa-grid-product-showcase' ); ?></label>
                            <input type="text" name="hexagrid_exclude_ids" id="hexagrid_exclude_ids" value="<?php echo esc_attr( $exclude_ids ); ?>" class="widefat" placeholder="e.g. 101, 105, 200">
                             <span class="description" style="display:block; margin-top:5px; color:#666;"><?php esc_html_e( 'Enter product IDs to exclude', 'hexa-grid-product-showcase' ); ?></span>
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
                </div>

                <!-- Section 3: Style Settings -->
                <div class="hexagrid-section">
                    <div class="hexagrid-section-header">
                        <div class="hexagrid-section-icon">
                            <span class="dashicons dashicons-art"></span>
                        </div>
                        <div class="hexagrid-section-info">
                            <h3><?php esc_html_e( 'Style Settings', 'hexa-grid-product-showcase' ); ?></h3>
                            <p><?php esc_html_e( 'Customize appearance and colors', 'hexa-grid-product-showcase' ); ?></p>
                        </div>
                        <span class="hexagrid-section-toggle dashicons dashicons-arrow-up-alt2"></span>
                    </div>

                    <div class="hexagrid-section-body">
                        <p class="hexagrid-form-group">
                            <label for="hexagrid_theme_color"><?php esc_html_e( 'Theme Color', 'hexa-grid-product-showcase' ); ?></label>
                            <input type="text" name="hexagrid_theme_color" id="hexagrid_theme_color" value="<?php echo esc_attr( $theme_color ); ?>" class="hexagrid-color-picker">
                             <span class="description" style="display:block; margin-top:5px; color:#666;"><?php esc_html_e( 'Select your primary brand color', 'hexa-grid-product-showcase' ); ?></span>
                        </p>
                    </div>
                </div>

                <?php if ( $post->ID ) : ?>
                    <div class="hexagrid-section">
                        <div class="hexagrid-section-body" style="border-top:none;">
                            <label style="font-weight:600; font-size:14px; margin-bottom:10px; display:block;"><?php esc_html_e( 'Shortcode', 'hexa-grid-product-showcase' ); ?></label>
                            <div class="hexagrid-shortcode-container">
                                <code id="hexagrid-shortcode-text">[hexagrid_product_showcase preset_id="<?php echo esc_attr( $post->ID ); ?>"]</code>
                                <button type="button" class="button hexagrid-copy-btn" data-clipboard-target="#hexagrid-shortcode-text">
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
        if ( isset( $_POST['hexagrid_content_type'] ) ) {
            update_post_meta( $post_id, '_hexagrid_content_type', sanitize_text_field( wp_unslash( $_POST['hexagrid_content_type'] ) ) );
        }
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
