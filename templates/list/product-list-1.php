<?php
/**
 * List Layout Template - Style 1 (Minimal)
 *
 * @var \WP_Query $query
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="hexagrid-layout-list" role="list">
    <?php if ( $query->have_posts() ) : update_post_thumbnail_cache( $query ); ?>
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>

            <?php 
                $product = wc_get_product( get_the_ID() );
                if ( ! $product ) {
                    continue; 
                }
            ?>

            <article <?php post_class( 'hexagrid-product' ); ?> role="listitem">
                <div class="hexagrid-product-wrapper">

                    <div class="hexagrid-product-image-area">
                        <?php 
                            echo HexaGrid\Helper::get_product_image( $product, 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); 
                            echo wp_kses_post( HexaGrid\Helper::get_product_badge( $product ) ); 
                        ?>
                    </div>

                    <div class="hexagrid-product-content-area">
                        <div class="hexagrid-product-content-header">
                            <?php 
                                echo wp_kses_post( HexaGrid\Helper::get_product_categories( $product ) ); 
                                echo wp_kses_post( HexaGrid\Helper::get_product_title( $product, 0 ) ); 
                            ?>
                        </div>

                        <?php 
                            echo wp_kses_post( HexaGrid\Helper::get_product_rating( $product ) ); 
                            echo wp_kses_post( HexaGrid\Helper::get_product_price( $product ) ); 
                            echo wp_kses_post( HexaGrid\Helper::get_product_excerpt( $product, 20, 'words' ) ); 
                            echo wp_kses_post( HexaGrid\Helper::get_add_to_cart_button( $product, 'text' ) ); 
                        ?>

                    </div>

                </div>
            </article>

        <?php endwhile; wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="hexagrid-no-products"><?php esc_html_e( 'No products found.', 'hexa-grid-product-showcase' ); ?></p>
    <?php endif; ?>
</div>
