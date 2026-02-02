<?php
/**
 * List Layout Template
 *
 * @var \WP_Query $query
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="hexagrid-layout-list">
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <?php 
            global $product;
            if ( ! is_object( $product ) ) {
                $product = wc_get_product( get_the_ID() );
            }
        ?>
        <article <?php post_class( 'hexagrid-product' ); ?>>
            <div class="hexagrid-product-wrapper">
                <div class="hexagrid-lproduct-image-area">
                    <a href="<?php the_permalink(); ?>" class="hexagrid-product-image-link">
                        <?php echo wp_kses_post( $product->get_image( 'woocommerce_medium' ) ); ?>
                    </a>
                    <?php if ( $product->is_on_sale() ) : ?>
                        <span class="hexagrid-badge hexagrid-sale-badge"><?php esc_html_e( 'Sale!', 'hexa-grid-product-showcase' ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="hexagrid-product-content-area">
                    <div class="hexagrid-product-content-header">
                        <?php 
                        $categories = wc_get_product_category_list( $product->get_id(), ', ' );
                        if ( $categories ) : ?>
                            <div class="hexagrid-product-category">
                                <?php echo wp_kses_post( $categories ); ?>
                            </div>
                        <?php endif; ?>

                        <h3 class="hexagrid-product-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo wp_kses_post( get_the_title() ); ?>
                            </a>
                        </h3>
                    </div>

                    <div class="hexagrid-product-rating">
                        <?php 
                        $average = $product->get_average_rating();
                        if ( $average = $product->get_average_rating() ) :
                            echo wp_kses_post( wc_get_rating_html( $average ) );
                            ?>
                            <span class="hexagrid-rating-average">
                                <?php echo esc_html( number_format( $average, 1 ) ); ?>
                            </span>
                            <?php
                            $review_count = $product->get_review_count();
                            if ( $review_count > 0 ) : ?>
                                <span class="hexagrid-rating-separator">&bull;</span>
                                <span class="hexagrid-review-count">
                                    <?php
                                    // translators: %d: Number of reviews.
                                    echo esc_html( sprintf( __( '%d reviews', 'hexa-grid-product-showcase' ), $review_count ) );
                                    ?>
                                </span>
                            <?php endif;
                        endif;
                        ?>
                    </div>
                    <div class="hexagrid-product-price">
                        <?php echo wp_kses_post( $product->get_price_html() ); ?>
                    </div>
                    <div class="hexagrid-product-short-desc">
                        <p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                    </div>
                    <div class="hexagrid-add-btn">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                </div>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</div>
