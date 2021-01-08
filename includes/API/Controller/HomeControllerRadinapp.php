<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use DateTime;
use function get_home_url;
use function json_decode;
use function register_rest_route;
use WC_Product_Data_Store_CPT;
use WP_REST_Response;
use WP_REST_Server;

class HomeControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = [
            'sliders',
            'gethome',
            "listproducthome",
            "listproductcustom",
            'getcartproductshome'
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

       register_rest_route($this->namespace,'/'.$this->rest_base[2],[
           'methods' => WP_REST_Server::READABLE,
           'callback' => array($this,'GetProductHome')
       ]);

       register_rest_route($this->namespace,'/'.$this->rest_base[3],[
           'methods' => WP_REST_Server::READABLE,
           'callback' => array($this,'ListProductCustom')
       ]);

       register_rest_route($this->namespace,'/'.$this->rest_base[4],[
           'methods' => WP_REST_Server::READABLE,
           'callback' => array($this,'getCartProdutshome')
       ]);
    }

    public function get_items($request)
    {
        $posts = get_posts(['post_type' => "slider_app"]);
        $sliders = array();
        foreach ($posts as $post) {
            $sliders[] = [
                'title' => $post->post_title,
                'type' => $post->post_type,
                'image_url' => ($post->guid == '') || ($post->guid == null) || ($post->guid== get_home_url())  ? null: get_home_url().$post->guid,
                'link' => $post->post_content
            ];
        }
        return $sliders;
    }

    public function get_item($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'digiappsaz_home_page';
        $data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY item_order");
        $results = array();


        foreach ($data as $item) {

            $results[] = [
                'id' => $item->id,
                'post_title' => $item->post_title,
                'post_type' => $item->post_type,
                'image_url' => ($item->image_url == null)  || ($item->image_url == '') ? null : get_home_url().$item->image_url,
                'image_small' => ($item->image_small == null) || ($item->image_url == '') ? null : get_home_url().$item->image_small,
                'content_type' => $item->content_type,
                'content' => (array)json_decode($item->content),
            ];
        }
        return $results;
    }


    public function GetProductHome($request){
        $product_ids = $request->get_param("cat");
        $page = $request->get_param('page');
        $query1 = array();
        foreach (json_decode($product_ids) as $key => $value) {
            $query = get_term_by('id', $value, 'product_cat');
            $query1[] = $query->slug;
        }

        $products = wc_get_products(
            array(
                'status' => 'publish',
                'paginate' => true,
                'limit' => 10,
                'page' => $page,
                'category' => $query1,
            ));
        $pagination = array(
            'total_page' => $products->max_num_pages,
            'total_product' => (int)$products->total,
        );
        $product_data1 = array();
        foreach ($products->products as $product) {
            $a = new WC_Product_Data_Store_CPT();
            $orginal_image = wp_get_attachment_image_url($product->get_image_id());
            $arr_image = array();
            foreach ($product->get_gallery_image_ids() as $product_image_id) {
                $Original_image_url = wp_get_attachment_url($product_image_id);
                $arr_image[] = $Original_image_url;
                if ($arr_image == array(false)) {
                    return $arr_image[] = null;
                }
            }
            array_push($arr_image, $orginal_image);

            $attr = array();
            foreach ($product->get_attributes() as $key => $attribute) {
                $arr_var = array();
                foreach (get_terms($key) as $slug => $value) {
                    $arr_var[] = array(
                        'lable' => $value->name,
                        'slug' => $value->slug,
                    );
                }
                $attr[] = array(
                    'name' => wc_attribute_label($key),
                    'slug' => 'attribute_' . $key,
                    'option' => $arr_var,
                );
            }
            $categories = array();
            foreach ($product->get_category_ids() as $category_id) {
                if ($term = get_term_by('id', $category_id, 'product_cat')) {
                    $categories[] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                    ];
                }
            }


            $product_data1[] = array(
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'parent_id' => $product->get_parent(),
                'status' => $product->get_status(),
                'featured' => $product->is_featured(),
                'description' => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'images' => $arr_image == array(false) ? [] : $arr_image,
                'price' => (string)(new Config_Radinapp())->priceWithFormat($product->get_price()),
                'regular_price' => $product->is_on_sale() == true ? (new Config_Radinapp())->priceWithFormat($product->get_regular_price()): (new Config_Radinapp())->priceWithFormat($product->get_regular_price()),
                'on_sale' => $product->is_on_sale(),
                'sale_price' => $product->get_sale_price(),
                'total_sale' => $product->get_total_sales(),
                'permlink' => $product->get_permalink(),
                'purchasable' => $product->is_purchasable(),
                'downloadable' => $product->is_downloadable(),
                'downloads' => $product->get_downloads(),
                'download_limit' => $product->get_download_limit(),
                'download_expiry' => $product->get_download_expiry(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
                'manage_stock' => $product->get_manage_stock(),
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' => $product->get_stock_status(),
                'backorders' => $product->get_backorders(),
                'backorders_allowed' => $product->backorders_allowed(),
                'weight' => $product->get_weight(),
                'dimensions' => $product->get_dimensions(),
                'shipping_required' => $product->get_shipping_class(),
                'shipping_taxable' => $product->is_shipping_taxable(),
                'shipping_class_id' => $product->get_shipping_class_id(),
                'type' => $product->get_type(),
                'attributes' => $attr,
                'grouped_products' => $product->grouped_product_sync(),
                'defualt_attrbiute' => $product->get_default_attributes(),
                'reviews_count' => $product->get_review_count(),
                'reviews' => $product->get_reviews_allowed(),
                'categories' => $categories,
                'is_review' => $product->get_reviews_allowed(),
                'count_review' => $product->get_review_count(),
                'average_rating' => $product->get_average_rating(),
                'is_fact_addcart' => get_post_meta($product->get_id(),'_is_enable_add_to_cart_fast',true) == '' ? 'disable' : get_post_meta($product->get_id(),'_is_enable_add_to_cart_fast',true),
                'children' => $product->get_children(),
                'currency' => Config_Radinapp::digiappsaz_current_currency()
            );

            $response = new WP_REST_Response($product_data1);
            $response->set_status(200);
        }
        return [
            'products' => $product_data1,
            'pagination' => $pagination
        ];
    }

    public function ListProductCustom($request){
        $product_ids = $request->get_param("product_ids");
        $page = $request->get_param('page');


        $products = wc_get_products(
            array(
                'status' => 'publish',
                'paginate' => true,
                'limit' => 10,
                'page' => $page,
                'include' => json_decode($product_ids),
            ));
        $pagination = array(
            'total_page' => $products->max_num_pages,
            'total_product' => (int)$products->total,
        );
        $product_data1 = array();
        foreach ($products->products as $product) {
            $a = new WC_Product_Data_Store_CPT();
            $orginal_image = wp_get_attachment_image_url($product->get_image_id());
            $arr_image = array();
            foreach ($product->get_gallery_image_ids() as $product_image_id) {
                $Original_image_url = wp_get_attachment_url($product_image_id);
                $arr_image[] = $Original_image_url;
                if ($arr_image == array(false)) {
                    return $arr_image[] = null;
                }
            }
            array_push($arr_image, $orginal_image);

            $attr = array();
            foreach ($product->get_attributes() as $key => $attribute) {
                $arr_var = array();
                foreach (get_terms($key) as $slug => $value) {
                    $arr_var[] = array(
                        'lable' => $value->name,
                        'slug' => $value->slug,
                    );
                }
                $attr[] = array(
                    'name' => wc_attribute_label($key),
                    'slug' => 'attribute_' . $key,
                    'option' => $arr_var,
                );
            }
            $categories = array();
            foreach ($product->get_category_ids() as $category_id) {
                if ($term = get_term_by('id', $category_id, 'product_cat')) {
                    $categories[] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                    ];
                }
            }


            $product_data1[] = array(
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'parent_id' => $product->get_parent(),
                'status' => $product->get_status(),
                'featured' => $product->is_featured(),
                'description' => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'images' => $arr_image == array(false) ? [] : $arr_image,
                'price' => (string)(new Config_Radinapp())->priceWithFormat($product->get_price()),
                'regular_price' => $product->is_on_sale() == true ? (new Config_Radinapp())->priceWithFormat($product->get_regular_price()): (new Config_Radinapp())->priceWithFormat($product->get_regular_price()),
                'on_sale' => $product->is_on_sale(),
                'sale_price' => $product->get_sale_price(),
                'total_sale' => $product->get_total_sales(),
                'permlink' => $product->get_permalink(),
                'purchasable' => $product->is_purchasable(),
                'downloadable' => $product->is_downloadable(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
                'manage_stock' => $product->get_manage_stock(),
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' => $product->get_stock_status(),
                'backorders' => $product->get_backorders(),
                'backorders_allowed' => $product->backorders_allowed(),
                'weight' => $product->get_weight(),
                'dimensions' => $product->get_dimensions(),
                'shipping_required' => $product->get_shipping_class(),
                'shipping_taxable' => $product->is_shipping_taxable(),
                'shipping_class_id' => $product->get_shipping_class_id(),
                'type' => $product->get_type(),
                'attributes' => $attr,
                'grouped_products' => $product->grouped_product_sync(),
                'defualt_attrbiute' => $product->get_default_attributes(),
                'reviews_count' => $product->get_review_count(),
                'reviews' => $product->get_reviews_allowed(),
                'categories' => $categories,
                'is_review' => $product->get_reviews_allowed(),
                'count_review' => $product->get_review_count(),
                'average_rating' => $product->get_average_rating(),
                'is_fact_addcart' => get_post_meta($product->get_id(),'_is_enable_add_to_cart_fast',true) == '' ? 'disable' : get_post_meta($product->get_id(),'_is_enable_add_to_cart_fast',true),
                'children' => $product->get_children(),
                'currency' => Config_Radinapp::digiappsaz_current_currency()
            );

            $response = new WP_REST_Response($product_data1);
            $response->set_status(200);
        }
        return [
            'products' => $product_data1,
            'pagination' => $pagination
        ];
    }

    public function getCartProdutshome(){
        try {
            $data = array();
            $this->init_cart_digiappsaz();
            foreach (WC()->cart->get_cart() as $key => $value) {
                $in_cart = WC()->cart->find_product_in_cart($value['key']);
                $data[] = [
                    'product_id' => WC()->cart->get_cart()[$in_cart]['product_id'],
                    'quantity' => WC()->cart->get_cart()[$in_cart]['quantity'],
                ];

            }
            if ($in_cart) {
                return $data;
            } else {
                return array();
            }


        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
new HomeControllerRadinapp();
