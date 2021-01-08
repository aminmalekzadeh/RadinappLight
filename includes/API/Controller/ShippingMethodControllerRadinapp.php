<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use Exception;
use function register_rest_route;
use function trim;
use function WC;
use WC_Customer;
use function woocommerce_cart_totals;
use function woocommerce_cart_totals_shipping_html;
use function woocommerce_shipping_calculator;
use function wp_authenticate;
use function wp_clear_auth_cookie;
use function wp_get_current_user;
use WP_REST_Server;

class ShippingMethodControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            'shippingmethods',
            'getshipping'
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name[0], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_items')
        ]);
        register_rest_route($this->namespace, '/' . $this->resource_name[1], [
            "methods" => WP_REST_Server::READABLE,
            'callback' => array($this, 'update_item')
        ]);
    }

    public function get_items($request)
    {
        $this->init_cart_digiappsaz();

        WC()->cart->calculate_totals();
        $user_id = $this->getuserid();
        try {
            WC()->customer = new WC_Customer($user_id);
            wp_set_current_user($user_id);
            WC()->cart->calculate_totals();
        } catch (Exception $e) {
        }

        $packages = WC()->shipping()->get_packages();
        $first = true;

        WC()->cart->calculate_totals();


        $shipping_methods = array();
        foreach ($packages as $i => $package) {
            $chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
            $product_names = array();

            if (count($packages) > 1) {
                foreach ($package['contents'] as $item_id => $values) {
                    $product_names[$item_id] = $values['data']->get_name() . ' &times;' . $values['quantity'];
                }
                $product_names = apply_filters('woocommerce_shipping_package_details_array', $product_names, $package);
            }
            $shipping = array();
            if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) {
                foreach (WC()->session->get('shipping_for_package_' . $i)['rates'] as $shipping_rate_id => $shipping_rate) {
                    $rate_id = $shipping_rate->get_id(); // same thing that $shipping_rate_id variable (combination of the shipping method and instance ID)
                    $method_id = $shipping_rate->get_method_id(); // The shipping method slug
                    $instance_id = $shipping_rate->get_instance_id(); // The instance ID
                    $label_name = $shipping_rate->get_label(); // The label name of the method
                    $cost = $shipping_rate->get_cost(); // The cost without tax
                    $tax_cost = $shipping_rate->get_shipping_tax(); // The tax cost
                    $taxes = $shipping_rate->get_taxes();// The taxes details (array)
                    $shipping[] = [
                        'rate_id' => $rate_id,
                        'method_id' => $method_id,
                        'instance_id' => $instance_id,
                        'lable_name' => $label_name,
                        'cost' => (new Config_Radinapp())->priceWithFormat($cost),
                        'tax_cost' => $tax_cost,
                        'taxes' => $taxes,
                        'currency' => Config_Radinapp::digiappsaz_current_currency()
                    ];
                }
            } else {
                return [];
            }


            $shipping_methods[] = array(
                'package' => $package,
                'available_methods' => $package['rates'],
                'show_package_details' => count($packages) > 1,
                'show_shipping_calculator' => is_cart() && apply_filters('woocommerce_shipping_show_shipping_calculator', $first, $i, $package),
                'package_details' => implode(', ', $product_names),
                'package_name' => apply_filters('woocommerce_shipping_package_name', (($i + 1) > 1) ? sprintf(_x('Shipping %d', 'shipping packages', 'woocommerce'), ($i + 1)) : _x('Shipping', 'shipping packages', 'woocommerce'), $i, $package),
                'index' => $i,
                'chosen_method' => $chosen_method,
                'formatted_destination' => WC()->countries->get_formatted_address($package['destination'], ', '),
                'has_calculated_shipping' => WC()->customer->has_calculated_shipping(),
            );

            $first = false;
            if ($shipping == null){
                return [];
            }
            return $shipping;
        }
        return [];
    }

    public function update_item($request)
    {
        $this->init_cart_digiappsaz();

        WC()->cart->calculate_totals();
        $user_id = $this->getuserid();
        try {
            WC()->customer = new WC_Customer($user_id);
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            WC()->cart->calculate_totals();
        } catch (Exception $e) {
        }
        $selected_shipping = $request->get_param("shipping_selected");


        $packages = WC()->shipping()->get_packages();
        $first = true;
        WC()->session->set('chosen_shipping_methods', array($selected_shipping));
        WC()->cart->calculate_totals();


        $shipping_methods = array();
        foreach ($packages as $i => $package) {
            $chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
            $product_names = array();

            if (count($packages) > 1) {
                foreach ($package['contents'] as $item_id => $values) {
                    $product_names[$item_id] = $values['data']->get_name() . ' &times;' . $values['quantity'];
                }
                $product_names = apply_filters('woocommerce_shipping_package_details_array', $product_names, $package);
            }

            $shipping_methods[] = array(
                'package' => $package,
                'available_methods' => $package['rates'],
                'show_package_details' => count($packages) > 1,
                'show_shipping_calculator' => is_cart() && apply_filters('woocommerce_shipping_show_shipping_calculator', $first, $i, $package),
                'package_details' => implode(', ', $product_names),
                'package_name' => apply_filters('woocommerce_shipping_package_name', (($i + 1) > 1) ? sprintf(_x('Shipping %d', 'shipping packages', 'woocommerce'), ($i + 1)) : _x('Shipping', 'shipping packages', 'woocommerce'), $i, $package),
                'index' => $i,
                'chosen_method' => $chosen_method,
                'formatted_destination' => WC()->countries->get_formatted_address($package['destination'], ', '),
                'has_calculated_shipping' => WC()->customer->has_calculated_shipping(),
            );

            $first = false;
        }
        WC()->cart->calculate_shipping();
        WC()->cart->calculate_totals();
        $totals = WC()->cart->get_totals();
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

new ShippingMethodControllerRadinapp();
