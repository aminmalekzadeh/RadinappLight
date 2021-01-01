<?php


namespace digiappsaz\API;


use function get_user_by;
use function get_user_meta;
use function register_rest_route;
use function WC;
use WC_Coupon;
use WP_REST_Server;
use digiappsaz\API\MessageControllerRadinapp;

class CouponControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = [
            'set_coupon',
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base[0], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'create_item')
        ]);

    }

    public function create_item($request)
    {
        $this->init_cart_digiappsaz();
        $coupon_code = $request->get_param('code');
        WC()->cart->apply_coupon($coupon_code);
        return WC()->cart->get_totals();

    }


}

new CouponControllerRadinapp();
