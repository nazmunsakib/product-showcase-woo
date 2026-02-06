    <?php
    /**
     * Slider Layout Template
     *
     * @var \WP_Query $query
     */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    ?>

    <div class="hexagrid-layout-slider hexagrid-<?php echo esc_attr( $style ) ?> hexagrid-product-grid-1 swiper" role="list">
        <div class="swiper-wrapper">
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
                ?>

                <article <?php post_class( 'swiper-slide hexagrid-product' ); ?> role="listitem">
                    <!-- <div class="hexagrid-product"> -->
                        <div class="hexagrid-product-wrapper">

                            <div class="hexagrid-product-image-area">
                                <?php 
                                    echo \HexaGrid\Helper::get_product_image( $product, 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); 
                                    echo wp_kses_post( \HexaGrid\Helper::get_product_badge( $product ) ); 
                                ?>
                            </div>

                            <div class="hexagrid-product-content-area">
                                <?php 
                                    echo wp_kses_post( \HexaGrid\Helper::get_product_title( $product, 10, 'words' ) );
                                    echo wp_kses_post( \HexaGrid\Helper::get_product_rating( $product, array( 'show_count' => false, 'show_average' => false ) ) ); 
                                ?>

                                <div class="hexagrid-product-footer">
                                    <?php 
                                        echo wp_kses_post( \HexaGrid\Helper::get_product_price( $product ) ); 
                                        echo wp_kses_post( \HexaGrid\Helper::get_add_to_cart_button( $product, 'icon' ) ); 
                                    ?>
                                </div>  
                            </div>

                        </div>
                    <!-- </div> -->
                </article>

            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <p class="hexagrid-no-products"><?php esc_html_e( 'No products found.', 'hexa-grid-product-showcase' ); ?></p>
        <?php endif; ?>
        </div>
        
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>


    