<?php
/**
 * Plugin Name: Hexa Grid
 * Plugin URI: https://addonskit.com
 * Description: Beautiful Product & Category Showcase with Unlimited Grid, List, Slider and Table Layouts.
 * Version: 1.0.0
 * Author: Nazmun Sakib
 * Author URI: https://nazmunsakib.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hexa-grid-product-showcase
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 *
 * @package HexaGrid
 * @author Nazmun Sakib
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Autoload dependencies.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * Declare WooCommerce HPOS compatibility
 *
 * @since 1.0.0
 * @return void
 */
function hexagrid_before_woocommerce_init_render(){
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
add_filter('before_woocommerce_init', 'hexagrid_before_woocommerce_init_render');

/**
 * Initialize the plugin.
 */
function hexagrid_product_showcase_init() {
	if ( class_exists( 'HexaGrid\\Product_Showcase' ) ) {
		\HexaGrid\Product_Showcase::get_instance();
	}
}
add_action( 'plugins_loaded', 'hexagrid_product_showcase_init' );
