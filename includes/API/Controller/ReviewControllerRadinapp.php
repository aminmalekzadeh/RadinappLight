<?php


namespace digiappsaz\API;


use function register_rest_route;
use WP_REST_Response;
use WP_REST_Server;

class ReviewControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            "review",
            "submit_review",
            "reviewme"
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,"/".$this->resource_name[0],[
            "methods" => WP_REST_Server::READABLE,
            'callback' => array($this,'get_items')
        ]);
        register_rest_route($this->namespace,"/".$this->resource_name[1],[
            "methods" => WP_REST_Server::CREATABLE,
            'callback' => array($this,'create_item')
        ]);
        register_rest_route($this->namespace,"/".$this->resource_name[2],[
            "methods" => WP_REST_Server::READABLE,
            'callback' => array($this,'get_item')
        ]);
    }

    public function get_items($request)
    {
        $reviews = array();
        $product_id = $request->get_param("id");
        $page = $request->get_param("page");
        $args = array(
            'post_id' => $product_id,
            'number' => 10,
            'paged' => $page
        );
        $comments = get_comments($args);

        foreach ($comments as $comment) {
            if ($comment->comment_approved == "1") {
                $reviews[] = array(
                    'id' => intval($comment->comment_ID),
                    'created_at' => date_i18n(get_option('date_format'), strtotime($comment->comment_date_gmt)),
                    'review_content' => $comment->comment_content,
                    'rating' => get_comment_meta($comment->comment_ID, 'rating', true) == "" ? "0" : get_comment_meta($comment->comment_ID, 'rating', true),
                    'reviewer_name' => $comment->comment_author,
                    'reviewer_email' => $comment->comment_author_email,
                    'verified' => wc_review_is_from_verified_owner($comment->comment_ID),
                    'review_parent' => $comment->comment_parent,
                    'user_id' => $comment->user_id,
                    'approved' => $comment->comment_approved,
                );
            }
        }

        return [
            'reviews' => $reviews,
            'total_page' => ceil(get_comments_number($product_id) / 10)
        ];
    }

    public function create_item($request)
    {
        $rating = $request->get_param("star");
        $content = $request->get_param("content");
        $product_id = $request->get_param("product_id");
        $comment_id = wp_insert_comment(array(
            'comment_post_ID' => $product_id, // <=== The product ID where the review will show up
            'comment_author' => $this->getdisplayname(),
            'comment_author_email' => $this->getemail(), // <== Important
            'comment_author_url' => '',
            'comment_content' => $content,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $this->getuserid(), // <== Important
            'comment_author_IP' => '',
            'comment_agent' => '',
            'comment_date' => date('Y-m-d H:i:s'),
            'comment_approved' => 0,
        ));

        return (new WP_REST_Response([
                'comment_id' => update_comment_meta($comment_id, 'rating', $rating)
            ]
        ));
    }

    public function get_item($request)
    {
        $reviews = array();
        $page = $request->get_param("page");
        $args = array(
            'user_id' => $this->getuserid(),
            'number' => 10,
            'paged' => $page
        );
        $args2 = array(
            'user_id' => $this->getuserid(),
            'paged' => $page
        );
        $comments = get_comments($args);
        $comments2 = get_comments($args2);

        foreach ($comments as $comment) {
            if ($comment->comment_post_ID != 0){
                $product = wc_get_product($comment->comment_post_ID);
                $arr_image = array();
                foreach ($product->get_gallery_image_ids() as $product_image_id) {
                    $Original_image_url = wp_get_attachment_url($product_image_id);
                    $arr_image[] = $Original_image_url;
                    if ($arr_image == array(false)) {
                        return $arr_image[] = null;
                    }
                }
                $reviews[] = array(
                    'id' => intval($comment->comment_ID),
                    'created_at' => $comment->comment_date_gmt,
                    'review_content' => $comment->comment_content,
                    'rating' => get_comment_meta($comment->comment_ID, 'rating', true),
                    'reviewer_name' => $comment->comment_author,
                    'reviewer_email' => $comment->comment_author_email,
                    'verified' => wc_review_is_from_verified_owner($comment->comment_ID),
                    'review_parent' => $comment->comment_parent,
                    'product_id' => $comment->comment_post_ID,
                    'image_product' => wp_get_attachment_url($product->get_image_id()),
                    'product_name' => $product->get_name(),
                    'approved' => $comment->comment_approved,
                );
            }
        }
        return [
            'reviews' => $reviews,
            'total_page' => ceil(count($comments2) / 10)
        ];
    }
}
new ReviewControllerRadinapp();
