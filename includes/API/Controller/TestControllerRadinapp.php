<?php


namespace Radinapp\API;


use function add_action;
use function add_user_meta;
use function apply_filters;
use function array_push;
use Radinapp_config\Config_Radinapp;
use Radinapp_config\dokan_dpp;
use function date;
use DateTime;
use function dig_authenticate;
use function dig_gatewayToUse;
use function dig_get_checkout_otp_verification;
use function dig_get_otp;
use function dig_getOtpTime;
use function dig_is_gatewayEnabled;
use function dig_show_login_captcha;
use function dig_validate_login_captcha;
use digiappsaz\API\BaseController_Radinapp;
use function digit_create_otp;
use function digit_send_otp;
use function digits_create_user;
use function digits_load_gateways;
use function digits_login;
use function digits_process_login;
use function digits_process_register;
use function digits_resendotp;
use function do_action;
use function dokan_get_seller_rating;
use Exception;
use function explode;
use function floatval;
use function GenerateChar_Radinapp;
use function get_current_user;
use function get_current_user_id;
use function get_terms;
use function get_user_meta;
use function get_woocommerce_currency;
use function get_woocommerce_currency_symbol;
use function getCountry;
use function getCountryCode;
use function getCountryList;
use function getGateWayArray;
use function getTranslatedCountryName;
use function getUserCountryCode;
use function getUserFromID;
use function getUserFromPhone;
use function getWhatsAppGateWayArray;
use function html_entity_decode;
use function implode;
use function intval;
use function is_user_logged_in;
use function jdate_dpp;
use function md5;
use function mt_rand;
use const PHP_EOL;
use function preg_grep;
use function preg_replace;
use function register_rest_route;
use function sanitize_email;
use function sanitize_title;
use function show_digcaptcha;
use function time;
use function trim;
use function unserialize;
use function var_export;
use function verifyOTP;
use function WC;
use WC_Cart;
use function wc_create_order;
use WC_Customer;
use WC_Data_Exception;
use WC_Data_Store;
use function wc_get_cart_item_data_hash;
use function wc_get_orders;
use function wc_price;
use WC_Shipping;
use WC_Shipping_Zone;
use function wp_create_nonce;
use function wp_cron;
use function wp_date;
use function wp_get_ready_cron_jobs;
use WP_REST_Server;
use function wp_set_auth_cookie;
use function wp_set_current_user;
use function wp_timezone;


class TestControllerRadinapp extends BaseController_Radinapp
{
    protected static $instance;

    public function __construct($version = 'v1')
    {
        parent::__construct();
        $this->rest_base = 'test';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_items')
        ]);
    }

    public function get_items($request)
    {
       return 'he';

    }

}

new TestControllerRadinapp();
