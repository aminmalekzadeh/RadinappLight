<?php


namespace digiappsaz\API;


use function add_action;
use function urldecode;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;


class CategoryControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->resource_name = 'categories';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name, [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_collection_params')
        ]);

    }

    public function get_collection_params()
    {
        $prod_categories = get_terms('product_cat', array(
            'orderby' => 'name',
            'HandleOrder' => 'ASC',
            'hide_empty' => false
        ));

        foreach ($prod_categories as $prod_cat) {
            $cat_thumb_id = get_woocommerce_term_meta($prod_cat->term_id, 'thumbnail_id', true);
            $shop_catalog_img = wp_get_attachment_image_src($cat_thumb_id, 'shop_catalog');
            $term_link = get_term_link($prod_cat, 'product_cat');

            $categories[] = [
                'id' => $prod_cat->term_id,
                'name' => $prod_cat->name,
                'count' => $prod_cat->count,
                'parent' => $prod_cat->parent,
                'slug' => urldecode($prod_cat->slug),
                'image' => $shop_catalog_img[0]
            ];
            $response = new WP_REST_Response($categories);
        }
        $response->set_status(200);
        return $response;
    }

}
new CategoryControllerRadinapp();



