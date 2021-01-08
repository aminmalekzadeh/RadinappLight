<?php


namespace digiappsaz\API;


use Radinapp_config\Config_Radinapp;
use function register_rest_route;
use WP_REST_Server;

class RelatedProductControllerRadinapp extends BaseController_Radinapp
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = "related_product";
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
        $product = wc_get_related_products($request->get_param("product_id"));
        $p = array();
        foreach ($product as $value) {
            $product = wc_get_product($value);
            $arr_image = array();
            $orginal_image = wp_get_attachment_image_url($product->get_image_id(), 'full');
            foreach ($product->get_gallery_image_ids() as $product_image_id) {
                $Original_image_url = wp_get_attachment_url($product_image_id);
                $arr_image[] = $Original_image_url;
                if ($arr_image == array(false)) {
                    return $arr_image[] = null;
                }
            }
            array_push($arr_image, $orginal_image);

            $args = array();
            $args = apply_filters(
                'wc_price_args',
                wp_parse_args(
                    $args,
                    array(
                        'ex_tax_label' => false,
                        'currency' => '',
                        'decimal_separator' => wc_get_price_decimal_separator(),
                        'thousand_separator' => wc_get_price_thousand_separator(),
                        'decimals' => wc_get_price_decimals(),
                        'price_format' => get_woocommerce_price_format(),
                    )
                )
            );
            if ($product->get_dimensions() != "نامعلوم"){
                $dimension = str_replace('&times;',',',$product->get_dimensions());
                $dimensions = explode(',',trim($dimension));
                $dimensions = [
                    'length' => $dimensions[0],
                    'width' => $dimensions[1],
                    'height' => $dimensions[2]
                ];
            }else{
                $dimensions = null;
            }
            $p[] = array(
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'parent_id' => $product->get_parent(),
                'status' => $product->get_status(),
                'featured' => $product->is_featured(),
                'description' => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'images' => $arr_image == array(false) ? [] : $arr_image,
                'price' => apply_filters('formatted_woocommerce_price', number_format($product->get_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator']), $product->get_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator']),
                'regular_price' => $product->get_regular_price(),
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
                'dimensions' => $dimensions,
                'shipping_required' => $product->get_shipping_class(),
                'shipping_taxable' => $product->is_shipping_taxable(),
                'shipping_class_id' => $product->get_shipping_class_id(),
                'type' => $product->get_type(),
                'grouped_products' => $product->grouped_product_sync(),
                'defualt_attrbiute' => $product->get_default_attributes(),
                'reviews_count' => $product->get_review_count(),
                'reviews' => $product->get_reviews_allowed(),
                'is_review' => $product->get_reviews_allowed(),
                'count_review' => $product->get_review_count(),
                'average_rating' => $product->get_average_rating(),
                'children' => $product->get_children(),
                'currency' => Config_Radinapp::digiappsaz_current_currency(),
            );
        }
        return $p;
    }

}
new RelatedProductControllerRadinapp();
