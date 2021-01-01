<?php


namespace digiappsaz\API;


use function add_user_meta;
use function digits_process_register;
use function email_exists;
use function getUserCountryCode;
use function register_rest_route;
use function trim;
use function username_exists;
use function verifyOTP;
use WP_REST_Response;
use WP_REST_Server;

class RegisterControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->resource_name = 'register';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name, [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'create_item')
        ]);
    }

    public function create_item($request)
    {
        $username = $request->get_param('username');
        $email = $request->get_param("email");
        $password = $request->get_param("password");
        $user = wp_create_user($username, $password, $email);
        if (is_wp_error($user) == true) {
            $response = new WP_REST_Response([
                'message' => 'Unsuccessful in Register user.',
                'user_id' => 0,
                'error' => $user->get_error_message(),
                'status' => 401
            ]);
            $response->set_status(401);
            return $response;
        } else {
            $response = new WP_REST_Response([
                'message' => 'Successfully Regitster User.',
                'user_id' => $user,
                'error' => null,
                'status' => 200
            ]);
            $this->user_id = $user;
            $response->set_status(200);
            return $response;
        }
    }
}

new RegisterControllerRadinapp();
