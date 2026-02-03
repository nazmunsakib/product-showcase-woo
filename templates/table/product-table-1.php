<?php
/**
 * Table Layout Template - Style 1
 *
 * @var \WP_Query $query
 * @var int $columns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="hexagrid-layout-table">
    <div class="hexagrid-table-responsive">
        <table class="hexagrid-product-table">
            <thead>
                <tr>
                    <th class="hexagrid-th-image"><?php esc_html_e( 'Image', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-name"><?php esc_html_e( 'Product Name', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-price"><?php esc_html_e( 'Price', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-rating"><?php esc_html_e( 'Rating', 'hexa-grid-product-showcase' ); ?></th>
                    <th class="hexagrid-th-action"><?php esc_html_e( 'Action', 'hexa-grid-product-showcase' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <?php 
                        global $product;
                        if ( ! is_object( $product ) ) {
                            $product = wc_get_product( get_the_ID() );
                        }
                    ?>
                    <tr>
                        <td class="hexagrid-td-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
                            </a>
                        </td>
                        <td class="hexagrid-td-name">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo wp_kses_post( get_the_title() ); ?>
                            </a>
                        </td>
                        <td class="hexagrid-td-price">
                             <?php echo wp_kses_post( $product->get_price_html() ); ?>
                        </td>
                        <td class="hexagrid-td-rating">
                            <?php 
                            if ( $average = $product->get_average_rating() ) :
                                echo wp_kses_post( wc_get_rating_html( $average ) );
                            endif;
                            ?>
                        </td>
                        <td class="hexagrid-td-action">
                            <?php 
                                echo sprintf( '<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
                                    esc_url( $product->add_to_cart_url() ),
                                    esc_attr( 'button product_type_' . $product->get_type() . ' add_to_cart_button ajax_add_to_cart' ),
                                    sprintf( 'data-product_id="%s" data-product_sku="%s" aria-label="%s" rel="nofollow"', 
                                        esc_attr( $product->get_id() ), 
                                        esc_attr( $product->get_sku() ),
                                        esc_attr( $product->add_to_cart_description() )
                                    ),
                                    esc_html( $product->add_to_cart_description() )
                                );
                            ?>
                        </td>
                    </tr>
                <?php endwhile; wp_reset_postdata(); ?>
            </tbody>
        </table>
    </div>
</div>
