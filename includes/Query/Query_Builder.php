<?php

namespace HexaGrid\Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Query_Builder
 *
 * Handles fetching products based on various criteria.
 */
class Query_Builder {

    /**
     * Arguments for WP_Query.
     *
     * @var array
     */
    protected $args = [];

    /**
     * Constructor.
     */
    public function __construct() {
        $this->args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 12, // Default limit
        ];
    }

    /**
     * Set the number of products to retrieve.
     *
     * @param int $limit Number of products.
     * @return self
     */
    public function set_limit( $limit ) {
        $this->args['posts_per_page'] = intval( $limit );
        return $this;
    }

    /**
     * Set specific product IDs to include.
     *
     * @param array $ids Array of product IDs.
     * @return self
     */
    public function set_ids( $ids ) {
        if ( ! empty( $ids ) ) {
            if ( is_string( $ids ) ) {
                $ids = array_map( 'intval', explode( ',', $ids ) );
            }
            $this->args['post__in'] = $ids;
        }
        return $this;
    }

    /**
     * Set specific product IDs to exclude.
     *
     * @param string|array $ids Array or comma-separated list of product IDs.
     * @return self
     */
    public function set_exclude_ids( $ids ) {
        if ( ! empty( $ids ) ) {
            if ( is_string( $ids ) ) {
                $ids = array_map( 'intval', explode( ',', $ids ) );
            }
            $this->args['post__not_in'] = $ids;
        }
        return $this;
    }



    /**
     * Set ordering arguments.
     *
     * @param string $orderby Order by parameter.
     * @param string $order Order direction (ASC/DESC).
     * @return self
     */
    public function set_order( $orderby = 'date', $order = 'DESC' ) {
        // Allowed orderby values
        $allowed_orderby = [ 'date', 'price', 'rand', 'title', 'popularity', 'ID', 'menu_order' ];
        if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
            $orderby = 'date';
        }

        // Allowed order values
        $allowed_order = [ 'ASC', 'DESC' ];
        $order = strtoupper( $order );
        if ( ! in_array( $order, $allowed_order, true ) ) {
            $order = 'DESC';
        }

        $this->args['orderby'] = $orderby;
        $this->args['order']   = $order;
        return $this;
    }

    /**
     * Execute the query and return the results.
     *
     * @return \WP_Query
     */
    public function get_query() {
        return new \WP_Query( $this->args );
    }
}
