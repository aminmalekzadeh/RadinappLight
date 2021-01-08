<?php


namespace digiappsaz\API;


use function array_chunk;
use Radinapp_config\Config_Radinapp;
use Automattic\WooCommerce\Blocks\StoreApi\Utilities\Pagination;
use function ceil;
use function count;
use Exception;
use function intval;
use function is_numeric;
use function pathinfo;
use function register_rest_route;
use function wc_get_customer_available_downloads;
use function wc_get_customer_download_permissions;
use function wc_get_order;
use function wc_get_product;
use function wp_get_attachment_image_url;
use WP_REST_Server;

class DownloadsControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = "downloads";
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            "methods" => WP_REST_Server::READABLE,
            "callback" => array($this, 'get_items')
        ));
    }

    public function get_items($request)
    {
        global $wpdb;
        $page = $request->get_param("page");
        if (empty($page)) {
            $page = 1;
        }

        $count_d = $wpdb->get_row($wpdb->prepare("SELECT count(*) as count FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions where user_id = %s", $this->getuserid()));


        try {
            do {

                $pag = ($page - 1) * 10;
                $resutls = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions where user_id = %s limit 10 offset $pag", $this->getuserid()), 'ARRAY_A');

                $downloads = array();
                foreach ($resutls as $key => $items) {
                    $product = wc_get_product($items['product_id']);
                    $download_file = $product->get_file($items['download_id']);
                    $file_number = 0;
                    $download_name = apply_filters(
                        'woocommerce_downloadable_product_name',
                        $download_file['name'],
                        $product,
                        $items['download_id'],
                        $file_number
                    );
                    $order = wc_get_order($items['order_id']);

                    if (!$order->is_download_permitted()) {
                        continue;
                    }
                    wc_get_customer_download_permissions(1);
                    $downloads[] = [
                        'download_url' => add_query_arg(
                            array(
                                'download_file' => $items['product_id'],
                                'order' => $items['order_key'],
                                'email' => rawurlencode($items['user_email']),
                                'key' => $items['download_id'],
                            ),
                            home_url('/')
                        ),
                        'product_name' => $product->get_name(),
                        'file_name' => $download_name,
                        'order_id' => intval($items['order_id']),
                        'access_expires' => $items['access_expires'],
                        'downloads_remaining' => $items['downloads_remaining'],
                        'product_id' => intval($items['product_id']),
                        'file' => pathinfo($product->get_file_download_path($items['download_id']))['basename'],
                        'format' => pathinfo($product->get_file_download_path($items['download_id']))['extension'],
                        'product_image' => wp_get_attachment_image_url($product->get_image_id(), 'full') != false ? wp_get_attachment_image_url($product->get_image_id(), 'full') : ''
                    ];

                    $file_number++;

                }

            }
            while(count($downloads)==0&&ceil($count_d->count / 10)>=++$page);

                $pagination = [
                    'total_items' => intval($count_d->count),
                    'page' => ceil($count_d->count / 10),
                    'current_page' => intval($page)
                ];

            return [
                'downloads' => apply_filters('woocommerce_customer_available_downloads', $downloads, $this->getuserid()),
                'pagination' => $pagination
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }


}

new DownloadsControllerRadinapp();
