<?php

namespace HexaGrid;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Product_Showcase
 *
 * Main plugin class to bootstrap the application.
 */
class Product_Showcase {

    /**
     * The single instance of the class.
     *
     * @var Product_Showcase
     */
    protected static $_instance = null;

    /**
     * Main Product_Showcase Instance.
     *
     * Ensures only one instance of Product_Showcase is loaded or can be loaded.
     *
     * @return Product_Showcase - Main instance.
     */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        // Since we are instantiated on 'plugins_loaded', we can run immediately.
        $this->on_plugins_loaded();
    }

    /**
     * Plugins loaded action.
     */
    public function on_plugins_loaded() {
        
        // Register services early so hooks (like init for CPT) can be added correctly.
        $this->register_services();
    }

    /**
     * Register core services.
     */
    private function register_services() {
        // We will initialize Query_Builder, Shortcodes, and Assets here
        $asset_manager = new \HexaGrid\Assets\Asset_Manager();
        $asset_manager->init();

        $shortcode_handler = new \HexaGrid\Shortcode\Shortcode_Handler();
        $shortcode_handler->init();

        // CPT must be registered globally
        $post_type = new \HexaGrid\Admin\Post_Type();
        $post_type->init();

        if ( is_admin() ) {
            $admin_manager = new \HexaGrid\Admin\Admin_Manager();
            $admin_manager->init();
        }
    }
}
