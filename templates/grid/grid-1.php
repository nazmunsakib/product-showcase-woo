<?php
/**
 * Grid Layout Template - Style 1 (Modern Card)
 *
 * @var \WP_Query $query
 * @var int $columns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="hexagrid-layout-grid hexagrid-columns-<?php echo esc_attr( $columns ); ?>">
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <?php 
            global $product;
            if ( ! is_object( $product ) ) {
                $product = wc_get_product( get_the_ID() );
            }
        ?>
        <article <?php post_class( 'hexagrid-product' ); ?>>
            <div class="hexagrid-product-wrapper">
                <div class="hexagrid-product-image-area">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
                    </a>
                    <?php if ( $product->is_on_sale() ) : ?>
                        <span class="hexagrid-badge hexagrid-sale-badge"><?php esc_html_e( 'Sale!', 'hexa-grid-product-showcase' ); ?></span>
                    <?php endif; ?>
                </div>
            
                <div class="hexagrid-product-content-area">
                    <h3 class="hexagrid-product-title">
                        <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( get_the_title() ); ?></a>
                    </h3>
                    
                    <div class="hexagrid-product-rating">
                        <?php 
                        if ( $average = $product->get_average_rating() ) :
                            echo wp_kses_post( wc_get_rating_html( $average ) );
                        endif;
                        ?>
                    </div>

                    <div class="hexagrid-product-footer">
                        <div class="hexagrid-product-price">
                            <?php echo wp_kses_post( $product->get_price_html() ); ?>
                        </div>
                        
                        <div class="hexagrid-product-cart-btn">
                            <?php 
                            // Custom Add to Cart Button (Simple Icon style)
                            $args = array(); 
                            echo sprintf( '<a href="%s" data-quantity="1" class="%s" %s aria-label="%s" rel="nofollow"><span class="dashicons dashicons-cart"></span></a>',
                                    esc_url( $product->add_to_cart_url() ),
                                    esc_attr( 'button product_type_' . $product->get_type() . ' add_to_cart_button ajax_add_to_cart' ),
                                    sprintf( 'data-product_id="%s" data-product_sku="%s" aria-label="%s"', 
                                        esc_attr( $product->get_id() ), 
                                        esc_attr( $product->get_sku() ),
                                        esc_attr( $product->add_to_cart_description() )
                                    ),
                                    esc_html( $product->add_to_cart_description() ) 
                                );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</div>
