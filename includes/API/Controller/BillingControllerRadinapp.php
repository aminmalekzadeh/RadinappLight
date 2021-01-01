<?php

namespace digiappsaz\API;


use function add_user_meta;
use Exception;
use function explode;
use function get_user_meta;
use function register_rest_route;
use function serialize;
use function unserialize;
use function update_user_meta;
use WC_Customer;
use WP_REST_Server;

class BillingControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = [
            "setbilling",
            "hasbilling",
            "getbilling",
            'add_location',
            'location'
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,'/'.$this->rest_base[0],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'create_item')
        ]);

        register_rest_route($this->namespace,'/'.$this->rest_base[1],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_item')
        ]);

        register_rest_route($this->namespace,'/'.$this->rest_base[2],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_items')
        ]);

        register_rest_route($this->namespace,'/'.$this->rest_base[3],[
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this,'save_location')
        ]);

        register_rest_route($this->namespace,'/'.$this->rest_base[4],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'get_location')
        ]);
    }

    public function create_item($request)
    {
        try {
            $this->init_cart_digiappsaz();
            WC()->customer = new WC_Customer($this->getuserid());
            WC()->customer->set_billing_first_name($request->get_param("first_name"));
            WC()->customer->set_billing_last_name($request->get_param("last_name"));
            WC()->customer->set_billing_company($request->get_param("company"));
            WC()->customer->set_billing_email($request->get_param("email"));
            WC()->customer->set_billing_phone($request->get_param("phone"));
            WC()->customer->set_billing_address_1($request->get_param("address_1"));
            WC()->customer->set_billing_address_2($request->get_param("address_2"));
            WC()->customer->set_billing_city($request->get_param("city"));
            WC()->customer->set_billing_state($request->get_param("state"));
            WC()->customer->set_billing_postcode($request->get_param("postcode"));
            WC()->customer->set_billing_country($request->get_param("country"));
            WC()->customer->set_shipping_country($request->get_param("country"));
            WC()->customer->set_shipping_state($request->get_param("state"));
            WC()->customer->save_data();

            WC()->cart->calculate_totals();

            return "saved data";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_item($request)
    {
        try {
            $customer = new WC_Customer($this->getuserid());
            if (($customer->get_address() && $customer->get_billing_phone() && $customer->get_billing_postcode()) != "") {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_items($request)
    {
        try {

            WC()->customer = new WC_Customer($this->getuserid());
            $address_detail = array(
                'address_biling' => WC()->customer->get_billing(),
                'name_customer' => WC()->customer->get_billing_first_name() . " " . WC()->customer->get_billing_last_name(),
            );
            return $address_detail;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function save_location($request){
       $locationx = $request->get_param("locationx");
       $locationy = $request->get_param("locationy");
       $location = [
         $locationx,
         $locationy
       ];

        if (get_user_meta($this->getuserid(),'dpp_location_user',true) == ""){
            add_user_meta($this->getuserid(),'dpp_location_user',$location,true);
        }else{
            update_user_meta($this->getuserid(),'dpp_location_user',$location);
        }
       return true;
    }

    public function get_location(){
        $user_location = get_user_meta($this->getuserid(),'dpp_location_user',true);
        return unserialize($user_location);
    }

}
new BillingControllerRadinapp();
