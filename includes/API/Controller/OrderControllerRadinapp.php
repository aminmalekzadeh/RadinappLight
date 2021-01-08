<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use Cart;
use Exception;
use function register_rest_route;
use function WC;
use WC_Customer;
use WC_Data_Exception;
use function wc_get_order;
use WC_Order_Item_Product;
use WC_Order_Item_Shipping;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

class OrderControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            "getorder",
            "setorder",
            "statusesorder",
            "payment_order_pending"
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name[0], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_items')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[1], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'create_item')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[2], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_item')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[3], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'payment_order_pending')
        ]);
    }

    public function get_items($request)
    {
        $status = $request->get_param("status");

        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'orderby' => 'date',
            'HandleOrder' => 'DESC',
            'meta_value' => $this->getuserid(),
            'post_type' => wc_get_order_types(),
            'post_status' => $status,
        ));

        $Order_Array = [];
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);
            $order_product = wc_get_order($orderq->get_id());
            $items = $order_product->get_items();
            $data_prodcut = array();
            foreach ($items as $item) {
                $data_prodcut[] = $item->get_data();
                $imgs = array();
                foreach ($data_prodcut as $key) {
                    $product = wc_get_product($key['product_id']);
                    if (method_exists($product,'get_image_id')){
                        $imgs[] = wp_get_attachment_image_url($product->get_image_id());
                    }
                }
            }
            $item_prodcut = new WC_Order_Item_Product($item->get_id());
            $args = array();
            $args = apply_filters(
                'wc_price_args',
                wp_parse_args(
                    $args,
                    array(
                        'ex_tax_label' => false,
                        'currency' => '',
                        'decimal_separator' => wc_get_price_decimal_separator(),
                        'thousand_separator' => wc_get_price_thousand_separator(),
                        'decimals' => wc_get_price_decimals(),
                        'price_format' => get_woocommerce_price_format(),
                    )
                )
            );


            $Order_Array[] = [
                "ID" => $orderq->get_id(),
                'product_line' => $data_prodcut,
                'status' => 'wc-' . $orderq->get_status(),
                'shipping_rate' => (new Config_Radinapp())->priceWithFormat($orderq->get_shipping_total()),
                'total_tax' => (new Config_Radinapp())->priceWithFormat($orderq->get_total_tax()),
                "Value" => apply_filters('formatted_woocommerce_price', number_format($orderq->get_total(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator']), $orderq->get_total(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator']),
                "Date" => $orderq->get_date_created()->date_i18n('Y/m/d'),
                'phone' => $orderq->get_billing_phone(),
                'product_image' => $imgs,
                'payment_method' => $orderq->get_payment_method_title(),
                'payment_id' => $orderq->get_payment_method(),
                'shipping_method' => $orderq->get_shipping_method(),
                'currency' => Config_Radinapp::digiappsaz_current_currency(),
                'address' => $orderq->get_billing_address_1(),
                'customer_name' => $orderq->get_billing_first_name() . " " . $orderq->get_billing_last_name(),
                'need_payment' => $orderq->needs_payment(),
            ];
        }
        return $Order_Array;
    }

    public function create_item($request)
    {
        global $wpdb;
        $this->init_cart_digiappsaz();
        try {
            wp_set_current_user($this->getuserid());
            WC()->customer = new WC_Customer($this->getuserid());
            $products = $this->ProductsCartDigiappsaz();
            $shipping = WC()->session->get('chosen_shipping_methods');
            $paymentmethod = $request->get_param('payment_method');
            $selected_shipping = $request->get_param("shipping_selected");

            $packages = WC()->shipping()->get_packages();
            $first    = true;
            WC()->cart->calculate_totals();
            WC()->session->set('chosen_shipping_methods', array($selected_shipping));
            $address = array(
                'first_name' => WC()->customer->get_billing_first_name(),
                'last_name' => WC()->customer->get_billing_last_name(),
                'company' => WC()->customer->get_billing_company(),
                'email' => WC()->customer->get_billing_email(),
                'phone' => WC()->customer->get_billing_phone(),
                'address_1' => WC()->customer->get_billing_address_1(),
                'address_2' => WC()->customer->get_billing_address_2(),
                'city' => WC()->customer->get_billing_city(),
                'state' => WC()->customer->get_billing_state(),
                'postcode' => WC()->customer->get_billing_postcode(),
                'country' => WC()->customer->get_billing_country()
            );
            $err = new WP_Error();
            $err->add('not_found_payment_method', 'not found payment method');
            if (isset($paymentmethod)) {
                if (WC()->cart->get_totals()['total'] != 0) {
                    // Now we create the order

                    $order = wc_create_order();
                    $order->update_status("pending", 'Imported order', TRUE);
                    try {
                        foreach ($products as $key => $value) {
                            if ($value['variation_id'] != 0) {
                                $order->add_product(get_product($value['variation_id']), $value['quantity']);
                            } else {
                                $order->add_product(get_product($value['product_id']), $value['quantity']);
                            }
                        }


                        $order->set_cart_hash(WC()->cart->get_cart_hash());
                        $item_shipping = new WC_Order_Item_Shipping();
                        $item_shipping->set_method_id($shipping[0]);
                        $item_shipping->set_name('حمل و نقل');
                        $item_shipping->set_method_title('');
                        $item_shipping->set_total(WC()->cart->get_shipping_total());

                        $order->add_item($item_shipping);
                        WC()->cart->calculate_totals();
                        $payment_gateway = WC()->payment_gateways()->get_available_payment_gateways();
                        if (isset($payment_gateway)) {
                            $order->set_payment_method($payment_gateway[$paymentmethod]);
                        } else {
                            return $err->errors;
                        }
                        $order->set_address($address, 'billing');
                        $order->set_customer_id($this->getuserid());

                        if (isset($paymentmethod, $payment_gateway)) {
                            $result = $payment_gateway[$paymentmethod]->process_payment($order->get_id());
                            if ($result['result'] == 'success') {
                                $result = apply_filters('woocommerce_payment_successful_result', $result, $order->get_id());
                                $message = [
                                    'address' => $order->get_address(),
                                    'tax' => $order->calculate_taxes(),
                                    'total' => $order->calculate_totals(),
                                    'shipping_total' => $order->calculate_shipping(),
                                    'status' => $order->get_status(),
                                    'payment_title' => $order->get_payment_method_title(),
                                    'needs_payment' => $order->needs_payment(),
                                    'currency' => $order->get_currency(),
                                    'user_id' => $order->get_customer_id(),
                                    'payment_token' => $order->get_payment_tokens(),
                                    'payment_gateway' => $order->get_payment_method(),
                                    'redirect' => $result['redirect'],
                                ];
                                WC()->cart->empty_cart();
                                $response = new WP_REST_Response($message);
                                $response->set_status(200);
                                return $response;
                            } else {
                                return $err->errors;
                            }
                        } else {
                            return $err->errors;
                        }
                    } catch (WC_Data_Exception $e) {
                        return $e->getMessage();
                    }
                } else {
                    return [
                        'message' => 'not_found_product',
                        'redirect' => ''
                    ];
                }
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function get_item($request)
    {
        $statuses = array();
        foreach (wc_get_order_statuses() as $key => $value) {
            $userid = $this->getuserid();
            $args = array(
                'customer_id' => $userid,
                'post_status' => $key,
                'post_type' => 'shop_order',
                'return' => 'ids',
            );
            $count = count(wc_get_orders($args));
            $statuses[] = [
                'status_code' => $key,
                'status_name' => $value,
                'count' => $count
            ];
        }
        return $statuses;
    }

    protected function ProductsCartDigiappsaz()
    {
        $products = array();
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
            $products[] = [
                'product_id' => $value['product_id'],
                'variation_id' => $value['variation_id'],
                'variation' => $value['variation'],
                'quantity' => (int)$value['quantity'],
            ];

        }
        return $products;
    }

    public function payment_order_pending($request)
    {
        $payment_method = $request->get_param("method");
        $order_id = $request->get_param("id");
        $this->init_cart_digiappsaz();
        $order = wc_get_order($order_id);
        $order->set_status("pending");
        $payment_gateway = WC()->payment_gateways()->get_available_payment_gateways();
        if ($order->needs_payment()) {
            try {
                $order->set_payment_method($payment_gateway[$payment_method]);
            } catch (WC_Data_Exception $e) {
                return $e->getMessage();
            }
            $order->update_status("pending");
            $result = $payment_gateway[$payment_method]->process_payment($order->get_id());
        }
        if ($result['result'] == 'success') {
            $result = apply_filters('woocommerce_payment_successful_result', $result, $order->get_id());
            return $result['redirect'];
        }
        return "";
    }
}

new OrderControllerRadinapp();
