<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use Exception;
use function register_rest_route;
use function WC;
use WC_Customer;
use WP_REST_Server;
use function wp_set_auth_cookie;

class PaymentMethodControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = [
            'paymentmethod',
            'detail_payment'
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,'/'.$this->rest_base[0],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_items')
        ]);
        register_rest_route($this->namespace,'/'.$this->rest_base[1],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_item')
        ]);
    }

    public function get_items($request)
    {
        $a = array();
        $this->init_cart_digiappsaz();
        $paymentgateways = WC()->payment_gateways()->get_available_payment_gateways();
        WC()->cart->calculate_totals();
        foreach ($paymentgateways as $key => $value) {
            $a[] = [
                'title' => $value->title,
                'description' => $value->description,
                'method' => $value->id,
                'icon' => $value->icon,
            ];
        }
        return $a;
    }

    public function get_item($request)
    {

        $this->init_cart_digiappsaz();
        WC()->cart->calculate_totals();
        $user_id =$this->getuserid();
        try {
            WC()->customer = new WC_Customer($user_id);
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            WC()->cart->calculate_totals();
        } catch (Exception $e) {
        }
        $selected_shipping = $request->get_param("shipping_selected");


        $packages = WC()->shipping()->get_packages();
        $first    = true;
        WC()->cart->calculate_totals();
        WC()->session->set('chosen_shipping_methods', array($selected_shipping));


        $shipping_methods = array();
        foreach ( $packages as $i => $package ) {
            $chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
            $product_names = array();

            if ( count( $packages ) > 1 ) {
                foreach ( $package['contents'] as $item_id => $values ) {
                    $product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
                }
                $product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
            }

            $shipping_methods[] = array(
                'package'                  => $package,
                'available_methods'        => $package['rates'],
                'show_package_details'     => count( $packages ) > 1,
                'show_shipping_calculator' => is_cart() && apply_filters( 'woocommerce_shipping_show_shipping_calculator', $first, $i, $package ),
                'package_details'          => implode( ', ', $product_names ),
                'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
                'index'                    => $i,
                'chosen_method'            => $chosen_method,
                'formatted_destination'    => WC()->countries->get_formatted_address( $package['destination'], ', ' ),
                'has_calculated_shipping'  => WC()->customer->has_calculated_shipping(),
            );

            $first = false;
        }
        WC()->cart->calculate_shipping();
        WC()->cart->calculate_totals();
        $totals = WC()->cart->get_totals();
        if ($totals == null){
            return [
                'cart_contents_total' => "0",
                'cart_contents_tax' => "0",
                'shipping_total' => "0",
                'total' => "0",
                'total_tax' => "0",
                'currency' => Config_Radinapp::digiappsaz_current_currency()
            ];
        }
        return $arr = [
            'cart_contents_total' => (new Config_Radinapp())->priceWithFormat($totals['cart_contents_total']),
            'cart_contents_tax' => (new Config_Radinapp())->priceWithFormat($totals['cart_contents_tax']),
            'shipping_total' => (new Config_Radinapp())->priceWithFormat($totals['shipping_total']),
            'total' => (new Config_Radinapp())->priceWithFormat($totals['total']),
            'total_tax' => (new Config_Radinapp())->priceWithFormat($totals['total_tax']),
            'currency' => Config_Radinapp::digiappsaz_current_currency()
        ];
    }
}
new PaymentMethodControllerRadinapp();
