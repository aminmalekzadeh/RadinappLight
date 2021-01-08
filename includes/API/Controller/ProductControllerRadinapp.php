<?php

namespace digiappsaz\API;


use function array_fill;
use function array_filter;
use function array_keys;
use function array_push;
use function array_unique;
use Radinapp_config\Config_Radinapp;
use Radinapp_config\dokan_dpp;
use function class_exists;
use function count;
use DateTime;
use Exception;
use function get_post_field;
use function get_post_meta;
use function get_taxonomies;
use function get_taxonomy;
use function get_term;
use function get_terms;
use function get_the_terms;
use function json_decode;
use Product;
use function register_rest_route;
use function time;
use function urldecode;
use function wc_attribute_label;
use function wc_attribute_taxonomy_name;
use function wc_attribute_taxonomy_slug;
use function wc_get_attribute;
use function wc_get_attribute_taxonomies;
use function wc_get_attribute_taxonomy_labels;
use function wc_get_attribute_taxonomy_names;
use function wc_get_attribute_type_label;
use function wc_get_text_attributes;
use WC_Product;
use WC_Product_Data_Store_CPT;
use function wp_date;
use WP_Query;
use WP_REST_Response;
use WP_REST_Server;

class ProductControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resource_name = [
            "product",
            "products",
            "getvariationid",
        ];
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name[0], [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_item')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[1], [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'get_items')
        ]);

        register_rest_route($this->namespace, '/' . $this->resource_name[2], [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'get_fields_for_response')
        ]);

    }

    public function get_item($request)
    {
        $product_id = $request->get_param("product_id");
        try {
            if ($product_id == null) {
                return "Null id!";
            }
            $product = wc_get_product($product_id);
            if (!$product) {
                return [];
            }
            $orginal_image = wp_get_attachment_image_url($product->get_image_id(), 'full');
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
            $attributes = $product->get_attributes();
            foreach ($attributes as $key => $attribute) {
                $arr_var = array();
                if (isset($attributes[$key])) {
                    foreach (wc_get_product_terms($product->get_id(), $key) as $slug => $value) {
                        $arr_var[] = array(
                            'lable' => $value->name,
                            'slug' => wc_attribute_taxonomy_slug($value->slug),
                        );
                    }
                    $attr[] = array(
                        'name' => wc_attribute_label($key),
                        'slug' => 'attribute_' . $key,
                        'option' => $arr_var,
                    );
                }
            }
            $attribute_read = array();
            foreach ($attributes as $key => $value) {
                $attribute_read[] = [
                    'name' => wc_attribute_label(wc_sanitize_taxonomy_name($key)),
                    'option' => $product->get_attribute($key)
                ];
            }


            $categories = array();
            foreach ($product->get_category_ids() as $category_id) {
                if ($term = get_term_by('id', $category_id, 'product_cat')) {
                    $categories[] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'count' => $term->count
                    ];
                }
            }


            if ($product->get_dimensions() != "نامعلوم") {
                $dimension = str_replace('&times;', ',', $product->get_dimensions());
                $dimensions = explode(',', trim($dimension));
                $dimensions = [
                    'length' => $dimensions[0],
                    'width' => $dimensions[1],
                    'height' => $dimensions[2]
                ];
            } else {
                $dimensions = [
                    'length' => 'نامعلوم',
                    'width' => null,
                    'height' => null
                ];
            }

            $terms = get_the_terms($product->get_id(), 'product_cat');
            foreach ($terms as $term) {
                $product_cat_id = $term->term_id;
            }

            $download = array();
            foreach ($product->get_downloads() as $key => $download) {
                $download[] = [
                    'key' => $key,
                    'download' => $download
                ];
            }


            $product_data1 = array(
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'parent_id' => $product->get_parent(),
                'status' => $product->get_status(),
                'featured' => $product->is_featured(),
                'description' => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'images' => $arr_image,
                'price' => (new Config_Radinapp())->priceWithFormat($product->get_price()),
                'regular_price' => $product->is_on_sale() == true ? (new Config_Radinapp())->priceWithFormat($product->get_regular_price()) : $product->get_regular_price(),
                'on_sale' => $product->is_on_sale(),
                'sale_price' => $product->is_on_sale() == true ? (new Config_Radinapp())->priceWithFormat($product->get_sale_price()) : $product->get_sale_price(),
                'total_sale' => $product->get_total_sales(),
                'permlink' => $product->get_permalink(),
                'is_sold_indivdually' => $product->is_sold_individually(),
                'purchasable' => $product->is_purchasable(),
                'downloadable' => $product->is_downloadable(),
                'is_virtual' => $product->is_virtual(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
                'manage_stock' => $product->get_manage_stock(),
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' => $product->get_stock_status(),
                'single_sell' => $product->get_sold_individually(),
                'backorders' => $product->get_backorders(),
                'backorders_allowed' => $product->backorders_allowed(),
                'weight' => $product->get_weight(),
                'dimensions' => $dimensions,
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
                'cat_id' => $product_cat_id,
                'is_review' => $product->get_reviews_allowed(),
                'count_review' => $product->get_review_count(),
                'average_rating' => $product->get_average_rating(),
                'children' => $product->get_children(),
                'price_tax' => $product->get_price_including_tax(),
                'currency' => Config_Radinapp::digiappsaz_current_currency(),
                'attribute' => $attribute_read,
            );

            if ($product_data1['images'] == array(false)) {
                $product_data1['images'] = [];
            }
            $response = new WP_REST_Response($product_data1);
            $response->set_status(200);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_items($request)
    {
        $data = $request->get_json_params();
        if ($data != null) {
            $args = array(
                'number' => 1,
                'orderby' => 'title',
                'order' => 'ASC',
                'hide_empty' => false,
                'include' => $data['cat_id'],
            );
            $product_categories = get_terms('product_cat', $args);
            $count = count($product_categories);
            if ($count > 0) {
                $product_cat = array();
                if (!$data['cat_id']) {
                    return [
                        "products" => [],
                        "pagination" => array(
                            "total_page" => 0,
                            "total_product" => 0,
                        )
                    ];
                } else {
                    foreach ($product_categories as $product_category) {
                        $catid = array(
                            'cat_id' => $product_category->term_id
                        );
                        if ($product_category->count == 0) {
                            return [
                                "products" => [],
                                "pagination" => array(
                                    "total_page" => 0,
                                    "total_product" => 0,
                                )
                            ];
                        }
                        $args = array(
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => $product_category->slug,
                                    'operator' => 'IN'
                                ),
                            ),
                            'post_type' => 'product',
                            'orderby' => 'title,'
                        );


                        $products = new WP_Query($args);

                        while ($products->have_posts()) {
                            $products->the_post();
                            $product_cat[] = $products->post->ID;
                        }
                    }


                    array_push(array_unique($product_cat), $catid);
                    $products = wc_get_products(
                        array(
                            'status' => 'publish',
                            'paginate' => true,
                            'orderby' => $data['orderby'] == null ? 'none' : $data['orderby'],
                            'order' => $data['order'],
                            'limit' => 10,
                            'page' => $data['page'] == null ? 1 : $data['page'],
                            'include' => $product_cat,
                            'stock_status' => $data['stock_status'] == null ? '' : $data['stock_status'],
                        ));
                }

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
                    $attributes = $product->get_attributes();
                    foreach ($attributes as $key => $attribute) {
                        $arr_var = array();
                        if (isset($attributes[$key])) {
                            foreach (wc_get_product_terms($product->get_id(), $key) as $slug => $value) {
                                $arr_var[] = array(
                                    'lable' => $value->name,
                                    'slug' => wc_attribute_taxonomy_slug($value->slug),
                                );
                            }
                            $attr[] = array(
                                'name' => wc_attribute_label($key),
                                'slug' => 'attribute_' . $key,
                                'option' => $arr_var,
                            );
                        }
                    }
                    $categories = array();
                    foreach ($product->get_category_ids() as $category_id) {
                        if ($term = get_term_by('id', $category_id, 'product_cat')) {
                            $categories[] = [
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'count' => $term->count
                            ];
                        }
                    }

                    $date_one_sale_to = get_post_meta($product->get_id(), '_digiappsaz_sale_price_dates_to', true);
                    $date_one_sale_from = get_post_meta($product->get_id(), '_digiappsaz_sale_price_dates_from', true);

                    $date1 = new DateTime(wp_date("Y-m-d H:i:s"));
                    $date2 = new DateTime(wp_date('Y-m-d H:i:s', $date_one_sale_to));
                    $diff = $date1->diff($date2);

                    if ($date_one_sale_from != false and $date_one_sale_to != false) {
                        $offtime = (($diff->days) . '_' . $diff->h . ':' . $diff->i . ':' . $diff->s);
                        if ($date_one_sale_to <= time()) {
                            $offtime = null;
                        }
                    } else {
                        $offtime = null;
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
                        'regular_price' => $product->is_on_sale() == true ? (new Config_Radinapp())->priceWithFormat($product->get_regular_price()) : (new Config_Radinapp())->priceWithFormat($product->get_regular_price()),
                        'on_sale' => $product->is_on_sale(),
                        'off_time' => $offtime,
                        'from_sale' => wp_date('Y-m-d H:t:s', $date_one_sale_from),
                        'to_sale' => wp_date('Y-m-d H:t:s', $date_one_sale_to),
                        'time_now' => wp_date("Y-m-d H:i:s"),
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
                        'is_fact_addcart' => get_post_meta($product->get_id(), '_is_enable_add_to_cart_fast', true) == '' ? 'disable' : get_post_meta($product->get_id(), '_is_enable_add_to_cart_fast', true),
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
        }

    }

    public function get_fields_for_response($request)
    {
        $productID = $request->get_param('product_id');
        $attribute1 = $request->get_param('attribute');
        return (new WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new WC_Product($productID),
            json_decode($attribute1, true));
    }

}

new ProductControllerRadinapp();
