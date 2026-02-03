<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Addons_Kit_Settings_Builder
 *
 * A reusable library for building professional admin settings forms.
 * Handles rendering of fields (Inputs, Cards, Switches) and their dependencies.
 */
class Addons_Kit_Settings_Builder {

    /**
     * @var string Prefix for field IDs (e.g., 'hexagrid').
     */
    private $prefix = '';

    /**
     * @var string Version for asset cache busting.
     */
    private $version = '1.0.0';

    /**
     * Addons_Kit_Settings_Builder constructor.
     *
     * @param string $prefix Optional prefix to prepend to all field IDs (e.g., 'myplugin').
     */
    public function __construct( $prefix = '' ) {
        $this->prefix = $prefix;
    }

    /**
     * Enqueue the necessary CSS and JS for the builder.
     * Should be called within 'admin_enqueue_scripts' hook.
     */
    public function enqueue_assets() {
        $base_url = plugin_dir_url( __FILE__ ); // Points to lib/addons-kit-settings/

        // Enqueue styles
        wp_enqueue_style( 
            'addons-kit-settings-style', 
            $base_url . 'assets/css/style.css', 
            [], 
            $this->version 
        );

        // Enqueue scripts
        wp_enqueue_script( 
            'addons-kit-settings-script', 
            $base_url . 'assets/js/script.js', 
            [ 'jquery', 'wp-color-picker' ], 
            $this->version, 
            true 
        );
        
        // Ensure WP Color Picker styles are loaded if used
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
     * Helper to get potentially prefixed ID.
     * If prefix is set and ID doesn't already start with it, prepend it.
     */
    public function get_id( $id ) {
        if ( empty( $this->prefix ) ) return $id;
        if ( strpos( $id, $this->prefix . '_' ) === 0 ) return $id; // Already prefixed
        return $this->prefix . '_' . $id;
    }

    /**
     * Render a Section Header.
     *
     * @param string $icon Dashicon class (e.g., 'dashicons-grid-view').
     * @param string $title Section title.
     * @param string $description Section description.
     */
    public function render_section_header( $icon, $title, $description ) {
        ?>
        <div class="aksbuilder-section-header">
            <div class="aksbuilder-section-icon">
                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
            </div>
            <div class="aksbuilder-section-info">
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><?php echo esc_html( $description ); ?></p>
            </div>
            <span class="aksbuilder-section-toggle dashicons dashicons-arrow-up-alt2"></span>
        </div>
        <?php
    }

    /**
     * Render a standard Text or Number input.
     *
     * @param array $args Field configurations.
     */
    public function render_text_field( $args ) {
        $defaults = [
            'id'          => '',
            'label'       => '',
            'value'       => '',
            'type'        => 'text',
            'desc'        => '',
            'placeholder' => '',
            'input_attr'  => '', // e.g. min="1" max="10"
            'class'       => 'aksbuilder-input',
            'dependency'  => [], // ['id' => 'layout_type', 'value' => 'slider']
        ];
        $args = wp_parse_args( $args, $defaults );
        
        $field_id = $this->get_id( $args['id'] );
        $container_attr = $this->get_dependency_attr( $args['dependency'] );
        ?>
        <div class="aksbuilder-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <input type="<?php echo esc_attr( $args['type'] ); ?>" 
                   name="<?php echo esc_attr( $field_id ); ?>" 
                   id="<?php echo esc_attr( $field_id ); ?>" 
                   value="<?php echo esc_attr( $args['value'] ); ?>" 
                   class="<?php echo esc_attr( $args['class'] ); ?>" 
                   placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" 
                   <?php echo $args['input_attr']; ?>>
            <?php if ( ! empty( $args['desc'] ) ) : ?>
                <span class="description" style="display:block; margin-top:5px; color:#666;"><?php echo esc_html( $args['desc'] ); ?></span>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a Select Dropdown.
     *
     * @param array $args Field configurations.
     */
    public function render_select_field( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => '',
            'options'    => [],
            'class'      => 'aksbuilder-select',
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );
        
        $field_id = $this->get_id( $args['id'] );
        $container_attr = $this->get_dependency_attr( $args['dependency'] );
        ?>
        <div class="aksbuilder-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <select name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>">
                <?php foreach ( $args['options'] as $val => $text ) : ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $args['value'], $val ); ?>><?php echo esc_html( $text ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    /**
     * Render a Toggle Switch.
     *
     * @param array $args Field configurations.
     */
    public function render_switcher_field( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => 'no', // 'yes' or 'no'
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );
        
        $field_id = $this->get_id( $args['id'] );
        $container_attr = $this->get_dependency_attr( $args['dependency'] );
        ?>
        <div class="aksbuilder-switch-container" <?php echo $container_attr; ?>>
            <span class="aksbuilder-switch-label"><?php echo esc_html( $args['label'] ); ?></span>
            <label class="aksbuilder-switch">
                <input type="checkbox" name="<?php echo esc_attr( $field_id ); ?>" value="yes" <?php checked( $args['value'], 'yes' ); ?>>
                <span class="aksbuilder-switch-slider"></span>
            </label>
        </div>
        <?php
    }

    /**
     * Render Color Picker.
     *
     * @param array $args Field configurations.
     */
    public function render_color_picker( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => '',
            'desc'       => '',
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );
        
        $field_id = $this->get_id( $args['id'] );
        $container_attr = $this->get_dependency_attr( $args['dependency'] );
        ?>
        <div class="aksbuilder-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" class="aksbuilder-color-picker">
            <?php if ( ! empty( $args['desc'] ) ) : ?>
                <span class="description" style="display:block; margin-top:5px; color:#666;"><?php echo esc_html( $args['desc'] ); ?></span>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render Card-based Radio/Checkbox Selector.
     *
     * @param array $args Field configurations.
     */
    public function render_card_selector( $args ) {
        $defaults = [
            'id'            => '',
            'label'         => '',
            'value'         => '',
            'options'       => [], // Array of ['value' => ['label', 'icon', 'desc']]
            'type'          => 'radio', // 'radio' or 'checkbox'
            'layout'        => 'grid', // 'grid' (icons) or 'list' (rows)
            'dependency'    => [],
            'assets_url'    => '', // Base URL for icon images
            'wrapper_class' => '', 
        ];
        $args = wp_parse_args( $args, $defaults );

        $field_id = $this->get_id( $args['id'] );
        $container_attr = $this->get_dependency_attr( $args['dependency'] );
        
        ?>
        <div class="aksbuilder-form-group <?php echo esc_attr( $args['wrapper_class'] ); ?>" <?php echo $container_attr; ?>>
            <?php if ( ! empty( $args['label'] ) ) : ?>
                <label style="margin-bottom:15px; display:block;"><?php echo esc_html( $args['label'] ); ?></label>
            <?php endif; ?>

            <div class="aksbuilder-card-grid">
                <?php foreach ( $args['options'] as $val => $data ) : 
                    $checked  = checked( $args['value'], $val, false );
                    $icon_url = isset( $data['icon'] ) ? $args['assets_url'] . $data['icon'] : '';
                    $has_skeleton = isset( $data['skeleton'] );
                    if ( $has_skeleton ) {
                         $icon_url = $args['assets_url'] . $data['skeleton'];
                    }
                ?>
                    <label class="aksbuilder-card-option">
                        <input type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php echo $checked; ?>>
                        
                        <div class="aksbuilder-card <?php echo ( $args['layout'] === 'list' ) ? 'aksbuilder-card-row' : ''; ?>">
                            
                            <?php if ( $has_skeleton ) : ?>
                                <!-- Skeleton Layout (for variations) -->
                                <div class="aksbuilder-variation-preview">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                </div>
                                <span class="aksbuilder-variation-label" style="font-weight:600; font-size:13px; color:#333; margin-top:5px; display:block;"><?php echo esc_html( $data['label'] ); ?></span>
                                <span class="aksbuilder-card-checkmark">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>

                            <?php else : ?>
                                <!-- Standard Icon Layout -->
                                <div class="aksbuilder-card-icon">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                </div>
                                
                                <div class="aksbuilder-card-info">
                                    <span class="aksbuilder-card-title"><?php echo esc_html( $data['label'] ); ?></span>
                                    <?php if ( ! empty( $data['desc'] ) ) : ?>
                                        <span class="aksbuilder-card-desc"><?php echo esc_html( $data['desc'] ); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ( $args['layout'] === 'list' ) : ?>
                                     <div class="aksbuilder-card-radio-dot"><span></span></div>
                                <?php else : ?>
                                    <span class="aksbuilder-card-checkmark">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                <?php endif; ?>

                            <?php endif; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Generate dependency data attribute.
     * Expects dependency array: ['id' => 'field_name', 'value' => 'expected_val']
     * 
     * @param array $dependency
     * @return string
     */
    private function get_dependency_attr( $dependency ) {
        if ( empty( $dependency ) || ! is_array( $dependency ) ) {
            return '';
        }
        
        // Ensure the dependency ID is also prefixed if it's not already
        if ( isset( $dependency['id'] ) ) {
            $dependency['id'] = $this->get_id( $dependency['id'] );
        }
        
        return "data-dependency='" . esc_attr( json_encode( $dependency ) ) . "'";
    }
}
