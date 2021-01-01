<?php


namespace digiappsaz\API;


use function get_home_url;
use function get_posts;
use function register_rest_route;
use function wp_insert_link;
use WP_REST_Server;

class SettingControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = 'setting_item';
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
        $posts = get_posts(
            array(
                "post_type" => "digiappsaz_setting"
            ));
        $p = array();
        foreach($posts as $post) {
            $p[] = [
                'item_title' => $post->post_title,
                'item_link' => $post->post_excerpt,
                'item_icon' => $post->post_content == get_home_url() ? null : get_home_url().$post->post_content
            ];
        }
        return $p;
    }
}

new SettingControllerRadinapp();
