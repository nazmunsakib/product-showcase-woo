<?php

namespace HexaGrid\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Form_Builder
 *
 * Helper class to render standardized form fields for the Admin UI.
 */
class Form_Builder {

    /**
     * Render a Section Header.
     *
     * @param string $icon Dashicon class (e.g., 'dashicons-grid-view').
     * @param string $title Section title.
     * @param string $description Section description.
     */
    public static function render_section_header( $icon, $title, $description ) {
        ?>
        <div class="hexagrid-section-header">
            <div class="hexagrid-section-icon">
                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
            </div>
            <div class="hexagrid-section-info">
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><?php echo esc_html( $description ); ?></p>
            </div>
            <span class="hexagrid-section-toggle dashicons dashicons-arrow-up-alt2"></span>
        </div>
        <?php
    }

    /**
     * Render a standard Text or Number input.
     *
     * @param array $args Field configurations.
     */
    public static function render_text_field( $args ) {
        $defaults = [
            'id'          => '',
            'label'       => '',
            'value'       => '',
            'type'        => 'text',
            'desc'        => '',
            'placeholder' => '',
            'input_attr'  => '', // e.g. min="1" max="10"
            'class'       => 'widefat',
            'dependency'  => [], // ['id' => 'hexagrid_layout_type', 'value' => 'slider']
        ];
        $args = wp_parse_args( $args, $defaults );

        $container_attr = self::get_dependency_attr( $args['dependency'] );
        ?>
        <p class="hexagrid-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <input type="<?php echo esc_attr( $args['type'] ); ?>" 
                   name="<?php echo esc_attr( $args['id'] ); ?>" 
                   id="<?php echo esc_attr( $args['id'] ); ?>" 
                   value="<?php echo esc_attr( $args['value'] ); ?>" 
                   class="<?php echo esc_attr( $args['class'] ); ?>" 
                   placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" 
                   <?php echo $args['input_attr']; ?>>
            <?php if ( ! empty( $args['desc'] ) ) : ?>
                <span class="description" style="display:block; margin-top:5px; color:#666;"><?php echo esc_html( $args['desc'] ); ?></span>
            <?php endif; ?>
        </p>
        <?php
    }

    /**
     * Render a Select Dropdown.
     *
     * @param array $args Field configurations.
     */
    public static function render_select_field( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => '',
            'options'    => [],
            'class'      => 'widefat',
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );

        $container_attr = self::get_dependency_attr( $args['dependency'] );
        ?>
        <p class="hexagrid-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <select name="<?php echo esc_attr( $args['id'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>">
                <?php foreach ( $args['options'] as $val => $text ) : ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $args['value'], $val ); ?>><?php echo esc_html( $text ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Render a Toggle Switch.
     *
     * @param array $args Field configurations.
     */
    public static function render_switcher_field( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => 'no', // 'yes' or 'no'
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );

        $container_attr = self::get_dependency_attr( $args['dependency'] );
        ?>
        <div class="hexagrid-switch-container" <?php echo $container_attr; ?>>
            <span class="hexagrid-switch-label"><?php echo esc_html( $args['label'] ); ?></span>
            <label class="hexagrid-switch">
                <input type="checkbox" name="<?php echo esc_attr( $args['id'] ); ?>" value="yes" <?php checked( $args['value'], 'yes' ); ?>>
                <span class="hexagrid-slider-round"></span>
            </label>
        </div>
        <?php
    }

    /**
     * Render Color Picker.
     *
     * @param array $args Field configurations.
     */
    public static function render_color_picker( $args ) {
        $defaults = [
            'id'         => '',
            'label'      => '',
            'value'      => '',
            'desc'       => '',
            'dependency' => [],
        ];
        $args = wp_parse_args( $args, $defaults );

        $container_attr = self::get_dependency_attr( $args['dependency'] );
        ?>
        <p class="hexagrid-form-group" <?php echo $container_attr; ?>>
            <label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
            <input type="text" name="<?php echo esc_attr( $args['id'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" class="hexagrid-color-picker">
            <?php if ( ! empty( $args['desc'] ) ) : ?>
                <span class="description" style="display:block; margin-top:5px; color:#666;"><?php echo esc_html( $args['desc'] ); ?></span>
            <?php endif; ?>
        </p>
        <?php
    }

    /**
     * Render Card-based Radio Selector (for Layouts, Content Types, etc.).
     *
     * @param array $args Field configurations.
     */
    public static function render_card_selector( $args ) {
        $defaults = [
            'id'            => '',
            'label'         => '',
            'value'         => '',
            'options'       => [], // Array of ['value' => ['label', 'icon', 'desc']]
            'type'          => 'layout', // 'layout' or 'content_type' or 'variation'
            'dependency'    => [],
            'assets_url'    => '', // Base URL for icons
            'wrapper_class' => '', 
        ];
        $args = wp_parse_args( $args, $defaults );

        $container_attr = self::get_dependency_attr( $args['dependency'] );
        
        // Define specific classes based on type
        $wrapper_class = 'hexagrid-form-group';
        $grid_class    = 'hexagrid-layout-type-grid';
        if ( $args['type'] === 'content_type' ) {
            $wrapper_class .= ' hexagrid-content-type-wrapper';
            $grid_class    = 'hexagrid-content-type-selector';
        }
        if ( $args['type'] === 'variation' ) {
             $grid_class = 'hexagrid-layout-variation-grid';
        }

        ?>
        <div class="<?php echo esc_attr( $wrapper_class . ' ' . $args['wrapper_class'] ); ?>" <?php echo $container_attr; ?>>
            <?php if ( ! empty( $args['label'] ) ) : ?>
                <label class="hexagrid-content-type-label"><?php echo esc_html( $args['label'] ); ?></label>
            <?php endif; ?>

            <div class="<?php echo esc_attr( $grid_class ); ?>">
                <?php foreach ( $args['options'] as $val => $data ) : 
                    $checked  = checked( $args['value'], $val, false );
                    $icon_url = isset( $data['icon'] ) ? $args['assets_url'] . $data['icon'] : '';
                ?>
                    <?php if ( $args['type'] === 'content_type' ) : ?>
                        <!-- Content Type Card Style -->
                        <label class="hexagrid-content-type-option">
                            <input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php echo $checked; ?>>
                            <div class="hexagrid-content-type-card">
                                <div class="hexagrid-content-type-icon">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                </div>
                                <div class="hexagrid-content-type-info">
                                    <span class="hexagrid-content-type-title"><?php echo esc_html( $data['label'] ); ?></span>
                                    <span class="hexagrid-content-type-desc"><?php echo esc_html( $data['desc'] ); ?></span>
                                </div>
                                <span class="hexagrid-content-type-radio">
                                    <span class="hexagrid-radio-dot"></span>
                                </span>
                            </div>
                        </label>
                    
                    <?php elseif ( $args['type'] === 'variation' ) : ?>
                        <!-- Variation Card Style -->
                         <label class="hexagrid-variation-option">
                            <input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php echo $checked; ?>>
                            <div class="hexagrid-variation-card">
                                <div class="hexagrid-variation-preview">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                </div>
                                <span class="hexagrid-variation-label"><?php echo esc_html( $data['label'] ); ?></span>
                                <span class="hexagrid-variation-checkmark">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </label>

                    <?php else : ?>
                        <!-- Layout Card Style -->
                        <label class="hexagrid-layout-option">
                            <input type="radio" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php echo $checked; ?>>
                            <div class="hexagrid-layout-card">
                                <div class="hexagrid-layout-icon">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $data['label'] ); ?>">
                                </div>
                                <span class="hexagrid-layout-label"><?php echo esc_html( $data['label'] ); ?></span>
                                <span class="hexagrid-layout-checkmark">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="white" d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Generate dependency data attribute.
     * Expects dependency array: ['id' => 'field_name', 'value' => 'expected_val', 'operator' => '==']
     * 
     * @param array $dependency
     * @return string
     */
    private static function get_dependency_attr( $dependency ) {
        if ( empty( $dependency ) || ! is_array( $dependency ) ) {
            return '';
        }
        
        // Simple JSON encoding to pass configuration to JS
        // JS will parse: data-dependency='{"id":"hexagrid_layout_type","value":"slider"}'
        return "data-dependency='" . esc_attr( json_encode( $dependency ) ) . "'";
    }
}
