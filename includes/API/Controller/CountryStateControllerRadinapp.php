<?php

namespace digiappsaz\API;


use function register_rest_route;
use WC_Countries;
use WP_REST_Server;

class CountryStateControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = [
            'getcountry',
            'getstate'
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
        $country = new WC_Countries();
        $data = array();
        foreach ($country->get_allowed_countries() as $key => $value) {
            $data[] = [
                'country_name' => $value,
                'country_code' => $key,
            ];
        }
        return $data;
    }

    public function get_item($request)
    {
        $state = new WC_Countries();
        $country = $request->get_param("country");
        $arr = array();
        foreach ($state->get_states($country) as $key => $value) {
            $arr[] = [
                'name_state' => $value,
                'code_state' => $key
            ];
        }
        return $arr;
    }
}
new CountryStateControllerRadinapp();
