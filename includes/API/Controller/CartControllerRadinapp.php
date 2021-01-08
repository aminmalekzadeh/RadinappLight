<?php


namespace digiappsaz\API;


use function add_action;
use Radinapp_config\Config_Radinapp;
use function count;
use Exception;
use function intval;
use function register_rest_route;
use function urldecode;
use function WC;
use WC_Cart;
use WC_Customer;
use WC_Session_Handler;
use WP_REST_Server;

class CartControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            'addtocart',
            'cart',
            'update_cart',
            'emptycart',
            'cart_count_product',
            "total_cart",
            'is_product_cart',
            'remove_cart_item'
        ];
    }


    public function register_routes()
    {
        register_rest_route($this->namespace, "/" . $this->resource_name[0], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'create_item')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[1], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_items')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[2], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'update_item')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[3], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'delete_item')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[4], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'cart_count_product')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[5], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_total_cart')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[6], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'is_product_cart')
        ]);

        register_rest_route($this->namespace, "/" . $this->resource_name[7], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'removeCartProduct')
        ]);
    }

    public function create_item($request)
    {

        $product_id = $request->get_param("product_id");
        $quantity = $request->get_param("quantity");
        $variation_id = $request->get_param("variation_id");
        $variation = $request->get_param("variation");
        $data_item = $request->get_param("data_item");


        try {
            $this->init_cart_digiappsaz();
            $generat_cart_id = WC()->cart->generate_cart_id($product_id, $variation_id);
            return WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $data_item);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_items($request)
    {
        $this->init_cart_digiappsaz();
        try {
            $cart_content = array();
            if (WC()->cart->get_cart() == null){
                return [];
            }
            foreach (WC()->cart->get_cart() as $key => $value) {
                $product = wc_get_product($value['product_id']);
                $arr_image = array();
                foreach ($product->get_gallery_image_ids() as $product_image_id) {
                    $Original_image_url = wp_get_attachment_url($product_image_id);
                    $arr_image[] = $Original_image_url;
                    if ($arr_image == array(false)) {
                        return $arr_image[] = null;
                    }
                }
                $cart_content[] = [
                    'key' => $value['key'],
                    'product_id' => $value['product_id'],
                    'variation_id' => $value['variation_id'],
                    'variation' => $value['variation'],
                    'quantity' => $value['quantity'],
                    'data' => $value['data'],
                    'data_hash' => $value['data_hash'],
                    'line_tax_data' => $value['line_tax_data'],
                    'line_subtotal' => $value['line_subtotal'],
                    'line_subtotal_tax' => $value['line_subtotal_tax'],
                    'line_total' => (new Config_Radinapp())->priceWithFormat($value['line_total']),
                    'line_tax' => $value['line_tax'],
                    'product_line' => array(
                        'name' => $product->get_name(),
                        'image' => wp_get_attachment_url($product->get_image_id()),
                        'price' => $product->get_price(),
                        'currency' => Config_Radinapp::digiappsaz_current_currency(),
                        'stock_manage' => $product->get_manage_stock(),
                        'quantity_stock' => $product->get_stock_quantity(),
                        'stock_status' => $product->get_stock_status(),
                        'single_sale' => $product->get_sold_individually(),
                        'product_type' => $product->get_type(),
                        'is_downloadable' => $product->is_downloadable(),
                        'is_virtual' => $product->is_virtual()
                    )
                ];

            }
            return $cart_content;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update_item($request)
    {
        $this->init_cart_digiappsaz();
        $key = $request->get_param("key");
        $quantity = $request->get_param("quantity");
        try {
            WC()->cart->set_quantity($key, $quantity, true);
            return WC()->cart->get_cart_item($key);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete_item($request)
    {
        $this->init_cart_digiappsaz();
        return WC()->cart->empty_cart();
    }

    public function cart_count_product()
    {
        $this->init_cart_digiappsaz();
        if (count(WC()->cart->get_cart_contents()) == 0){
            return 0;
        }
        return (int) count(WC()->cart->get_cart_contents());
    }

    public function get_total_cart()
    {
        $this->init_cart_digiappsaz();
        WC()->cart->calculate_totals();
        $totals = WC()->cart->get_totals();
        WC()->cart->calculate_totals();

        WC()->cart->tax_display_cart;
        WC()->cart->get_total_tax();
        WC()->cart->get_cart_tax();

        if($totals == null){
            return [
                'calculate' => "0",
                "tax_total" => "0",
                "total_product" => "0",
                'currency' => Config_Radinapp::digiappsaz_current_currency(),
                "discount" => "0"
            ];
        }
        $totalsarr = array(
            'calculate' => (new Config_Radinapp())->priceWithFormat($totals['cart_contents_total']),
            'tax_total' => (String)(new Config_Radinapp())->priceWithFormat($totals['cart_contents_tax']),
            'total_product' => (new Config_Radinapp())->priceWithFormat($totals['cart_contents_total'] + $totals['cart_contents_tax']),
            'currency' => Config_Radinapp::digiappsaz_current_currency(),
            'discount' => (new Config_Radinapp())->priceWithFormat($totals['discount_total'])
        );
        return $totalsarr;
    }

    public function is_product_cart($request)
    {
        $this->init_cart_digiappsaz();
        $product_id = $request->get_param("product_id");
        $variation_id = $request->get_param("variation_id");
        $generate_cart_id = WC()->cart->generate_cart_id($product_id, $variation_id);
        $incart = WC()->cart->find_product_in_cart($generate_cart_id);
        if ($incart == null){
            return [];
        }
        if ($incart) {
            return [
                'quantity' => intval(WC()->cart->get_cart()[$incart]['quantity']),
                'key' => WC()->cart->get_cart()[$incart]['key']
            ];
        } else {
            return 0;
        }
    }

    public function removeCartProduct($request)
    {
        $this->init_cart_digiappsaz();
        $key = $request->get_param("key");
        return WC()->cart->remove_cart_item($key);
    }


}

new CartControllerRadinapp();
