<?php


namespace digiappsaz\API;


use Firebase\JWT\JWT;
use function get_user_by;
use function is_numeric;
use function register_rest_route;
use function trim;
use function verifyOTP;
use function wp_authenticate;
use function wp_logout;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class LoginControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base;
        $this->resource_name = [
            'login',
            'logout'
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,'/'.$this->resource_name[0],[
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this,'get_item')
        ]);

        register_rest_route($this->namespace,'/'.$this->resource_name[1],[
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this,'delete_item')
        ]);
    }

    public function get_item($request)
    {
        $user_login = $request->get_param("username");
        $password = $request->get_param("password");

        $auth = null;
            if (!empty($user_login)){
                if (strpos($user_login,'@') == true){
                    $user = get_user_by('email', $user_login);
                    if ($user != false){
                        $auth = wp_authenticate($user->user_login, $password);
                    }else{
                        $response = new WP_REST_Response([
                            'message' => 'not authenticated',
                            'status' => 401
                        ]);
                        $response->set_status(401);
                        return $response;
                    }
                }else{
                    $auth = wp_authenticate($user_login, $password);
                }
            }else{
                $response = new WP_REST_Response([
                    'message' => 'not authenticated',
                    'status' => 401
                ]);
                $response->set_status(401);
                return $response;
            }

        if (is_wp_error($auth)) {
            $response = new WP_REST_Response([
                'message' => 'not authenticated',
                'status' => 401
            ]);
            $response->set_status(401);
            return $response;
        } else {
            $issuer_claim = "THE_ISSUER";
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time();
            $notbefore_claim = $issuedat_claim;
            $expire_claim = $issuedat_claim + (24 * 15 * 60 * 60);
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "id" => $auth->ID,
                    "name" => $auth->display_name,
                    "email" => $auth->user_email,
                    "username" => $auth->user_login,
                ));

            wp_set_current_user($auth->ID);

            $SECRETKEY = get_option('dapp_SECRETKEY');
            $jwt = JWT::encode($token, trim($SECRETKEY));
            $message = array(
                "data" => array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "username" => $user_login,
                    'display_name' => $auth->first_name . ' ' . $auth->last_name,
                    "expireAt" => date('y.m.d', $expire_claim),
                    "status" => 200,
                )
            );
            $response = new WP_REST_Response($message['data']);
            $response->set_status(200);

            return $response;
        }
    }

    public function delete_item($request)
    {
        wp_logout();
    }
}
new LoginControllerRadinapp();
