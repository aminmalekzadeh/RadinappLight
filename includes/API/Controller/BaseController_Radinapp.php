<?php


namespace digiappsaz\API;


use Exception;
use Firebase\JWT\JWT;
use function WC;
use WC_Cart;
use WC_Customer;
use WC_REST_Authentication;
use WC_Session_Handler;
use WC_Shipping;
use WP_REST_Controller;
use WP_REST_Response;

class BaseController_Radinapp extends WP_REST_Controller
{
    public $version;

    public function __construct($version = 'v1')
    {
        $this->version = $version;
        $this->namespace = 'digiappsaz/' . $version;
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('wp_loaded',array($this,'rest_api_includes'));
    }

    public function rest_api_includes()
    {
        if (defined('WC_ABSPATH')){
            require_once(WC_ABSPATH . 'includes/wc-cart-functions.php');
            require_once(WC_ABSPATH . 'includes/wc-notice-functions.php');
        }

    }

    public function init_cart_digiappsaz(){
        global $woocommerce;
        if (floatval($woocommerce->version) >= floatval('4.3.0')){
            $woocommerce = WC();
            $woocommerce->session = new WC_Session_Handler();
            $woocommerce->session->init();
            try {
                $woocommerce->customer = new WC_Customer();
            } catch (Exception $e) {
                return $e->getMessage();
            }
            $woocommerce->cart = new WC_Cart();
        }else{
            return null;
        }
        WC()->cart->calculate_totals();
    }

    public function getuserid()
    {
        try {
            $bearertoken = $this->getBearertoken();
            $SECRETKEY = get_option('dapp_SECRETKEY');
            $decode = JWT::decode($bearertoken, trim($SECRETKEY), ['HS256']);
            $auth = new WC_REST_Authentication();
            return $auth->authenticate($decode->data->id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    public function getAuthorizationHeader()
    {
        $headers = false;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        } else {
            $headers = "please login.";
        }

        return $headers;
    }

    public function runfiles()
    {
        return __FILE__;
    }

    public function getBearertoken()
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        $response = new WP_REST_Response("Not Found Token");
        $response->set_status(200);
        return $response;
    }

    public function getdisplayname()
    {
        $userdisplayname = get_userdata($this->getuserid());
        return $userdisplayname->first_name . ' ' . $userdisplayname->last_name;
    }

    public function getemail()
    {
        $useremail = get_userdata($this->getuserid());
        return $useremail->user_email;
    }

    public function first_name()
    {

        $user_firstname = get_userdata($this->getuserid());
        return $user_firstname->first_name;
    }

    public function last_name()
    {
        $user_lastname = get_userdata($this->getuserid());
        return $user_lastname->last_name;
    }

    public function authorization_status_code()
    {
        $status = 401;

        if (is_user_logged_in()) {
            $status = 403;
        }
        return $status;
    }

}

