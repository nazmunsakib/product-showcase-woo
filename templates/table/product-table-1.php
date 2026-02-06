<?php
/**
 * Table Layout Template - Style 1
 *
 * @var \WP_Query $query
 * @var int $columns
 * @var string $style
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="hexagrid-layout-table hexagrid-<?php echo esc_attr( $style ) ?>">
    <div class="hexagrid-table-responsive">
        <table class="hexagrid-product-table">
            <thead>
                <tr>
                    <th class="hexagrid-th-product"><?php esc_html_e( 'Product', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-category"><?php esc_html_e( 'Category', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-price"><?php esc_html_e( 'Price', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-rating"><?php esc_html_e( 'Rating', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-action"><?php esc_html_e( 'Actions', 'hexa-grid-product-showcase' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $query->have_posts() ) : update_post_thumbnail_cache( $query ); ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php 
                            if ( ! function_exists( 'wc_get_product' ) ) {
                                continue;
                            }
                            $product = wc_get_product( get_the_ID() );
                            if ( ! $product ) {
                                continue; 
                            }

                            $stock_status = $product->get_stock_status();
                            $stock_label  = \HexaGrid\Helper::get_product_stock_status( $product );

                            // Check for low stock
                            if ( $product->is_in_stock() && $product->managing_stock() ) {
                                $stock_qty = $product->get_stock_quantity();
                                $low_stock_amount = (int) get_option( 'woocommerce_notify_low_stock_amount', 2 );
                                if ( null !== $stock_qty && $stock_qty <= $low_stock_amount ) {
                                    $stock_status = 'lowstock';
                                    $stock_label  = __( 'Low Stock', 'hexa-grid-product-showcase' );
                                }
                            }
                        ?>
                        <tr class="hexagrid-product-row hexagrid-product">
                            <td class="hexagrid-td-product" data-label="<?php esc_attr_e( 'Product', 'hexa-grid-product-showcase' ); ?>">
                                <div class="hexagrid-product-info-wrapper">
                                    <div class="hexagrid-product-image">
                                        <?php echo \HexaGrid\Helper::get_product_image( $product ); ?>
                                        <span class="hexagrid-status-pill status-<?php echo esc_attr( $stock_status ); ?>">
                                            <?php echo esc_html( $stock_label ); ?>
                                        </span>
                                    </div>
                                    <div class="hexagrid-product-details">
                                        <?php echo \HexaGrid\Helper::get_product_title( $product ); ?>
                                        <?php echo \HexaGrid\Helper::get_product_excerpt( $product, 10 ); ?>
                                    </div>
                                </div>
                            </td>
                            <td class="hexagrid-td-category" data-label="<?php esc_attr_e( 'Category', 'hexa-grid-product-showcase' ); ?>">
                                <?php echo \HexaGrid\Helper::get_product_categories( $product ); ?>
                            </td>
                            <td class="hexagrid-td-price" data-label="<?php esc_attr_e( 'Price', 'hexa-grid-product-showcase' ); ?>">
                                 <?php echo \HexaGrid\Helper::get_product_price( $product ); ?>
                            </td>
                            <td class="hexagrid-td-rating" data-label="<?php esc_attr_e( 'Rating', 'hexa-grid-product-showcase' ); ?>">
                                <?php echo \HexaGrid\Helper::get_product_rating( $product, array( 'show_average' => false, 'show_count' => false ) ); ?>
                            </td>
                            <td class="hexagrid-td-action" data-label="<?php esc_attr_e( 'Actions', 'hexa-grid-product-showcase' ); ?>">
                                <div class="hexagrid-action-buttons">
                                    <div class="hexagrid-quantity-stepper">
                                        <button type="button" class="hexagrid-qty-btn hexagrid-qty-minus">-</button>
                                        <input type="number" class="hexagrid-qty-input" value="1" min="1">
                                        <button type="button" class="hexagrid-qty-btn hexagrid-qty-plus">+</button>
                                    </div>
                                    <?php echo wp_kses_post( \HexaGrid\Helper::get_add_to_cart_button( $product, 'icon' )); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">
                            <p class="hexagrid-no-products"><?php esc_html_e( 'No products found.', 'hexa-grid-product-showcase' ); ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
