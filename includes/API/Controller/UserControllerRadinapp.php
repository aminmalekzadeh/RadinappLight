<?php


namespace digiappsaz\API;


use Firebase\JWT\JWT;
use function get_avatar_url;
use function get_user_by;
use function register_rest_route;
use WP_Error;
use WP_REST_Server;


class UserControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            "updateprofile",
            "getaccount",
            "checkpass"
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name[0], [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'update_item')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[1], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_item')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[2], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'checkpassword')
        ]);
    }



    public function update_item($request)
    {
        $data = $request->get_json_params();

        wp_update_user(array(
            'ID' => $this->getuserid(),
            'user_email' => $data['email'] == "" ? $this->getemail() : $data['email'],
            'display_name' => $data['first_name'] . ' ' . $data['last_name'],
            'user_pass' => $data['password'],
            'first_name' => $data['first_name'] == "" ? $this->first_name() : $data['first_name'],
            'last_name' => $data['last_name'] == "" ? $this->last_name() : $data['last_name']
        ));
        return [
            'data' => "update_data"
        ];
    }

    public function get_item($request)
    {
        $user = get_user_by("ID", $this->getuserid());
        $account = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
            'username' => $user->user_login,
            'avatar' => get_avatar_url($user->ID)
        );
        return $account;
    }

    public function checkpassword($request)
    {
        $bearertoken = $this->getBearertoken();
        $password = $request->get_param("password");
        $SECRETKEY = get_option('dapp_SECRETKEY');
        $decode = JWT::decode($bearertoken, trim($SECRETKEY), ['HS256']);
        $user = get_user_by('login', $decode->data->username);
        if (wp_check_password($password, $user->data->user_pass, $user->ID) == true) {
            return [
                'status_password' => true
            ];
        } else {
            return [
                'status_password' => false
            ];
        }

    }
}

new UserControllerRadinapp();
