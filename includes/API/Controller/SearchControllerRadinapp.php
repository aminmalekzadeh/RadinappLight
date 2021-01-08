<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use function register_rest_route;
use WC_Product_Query;
use WP_REST_Server;

class SearchControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = 'search';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,'/'.$this->rest_base,[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_items')
        ]);
    }

    public function get_items($request)
    {
        $search = $request->get_param("search");
        $query = new WC_Product_Query();
        $query->set('s', $search);
        $products = $query->get_products();
        $items = array();
        foreach ($products as $key => $product) {
            $items[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => (new Config_Radinapp())->priceWithFormat($product->get_price()),
                'image' => wp_get_attachment_image_url($product->get_image_id()),
                'currency' => get_woocommerce_currency_symbol(get_woocommerce_currency())
            ];
        }
        return $items;
    }
}
new SearchControllerRadinapp();
