<?php
/**
 * Grid Layout Template - Style 2
 *
 * @var \WP_Query $query
 * @var int $columns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="hexagrid-layout-grid hexagrid-layout-2 hexagrid-columns-<?php echo esc_attr( isset( $columns ) ? $columns : 3 ); ?>">
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <?php 
            global $product;
            if ( ! is_object( $product ) ) {
                $product = wc_get_product( get_the_ID() );
            }
        ?>
        <article <?php post_class( 'hexagrid-product' ); ?>>
            <div class="hexagrid-product-wrapper">
                <?php if ( $product->is_on_sale() ) : ?>
                    <span class="hexagrid-badge hexagrid-sale-badge"><?php esc_html_e( 'Sale!', 'hexa-grid-product-showcase' ); ?></span>
                <?php endif; ?>

                <div class="hexagrid-product-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
                    </a>
                </div>

                <div class="hexagrid-product-content-area">
                    <h3 class="hexagrid-product-title">
                        <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( get_the_title() ); ?></a>
                    </h3>
                    
                    <div class="hexagrid-product-category">
                        <?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), ', ' ) ); ?>
                    </div>

                    <div class="hexagrid-product-price">
                        <?php echo wp_kses_post( $product->get_price_html() ); ?>
                    </div>
                    
                    <div class="hexagrid-add-btn">
                         <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                </div>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</div>