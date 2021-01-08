<?php
/*
  Plugin Name: RadinApp
  Plugin URI: https://digiappsaz.com
  Description: Create Woocommerce App in 5 minutes with radinapp.
  Version: 1.0.1
  Author: digiappsaz
  Text Domain: digiappsaz
  Domain Path: /lang/
  WC requires at least: 3.3
  WC tested up to: 4.8.0
 */

define('APP_Radinapp_DIR', plugin_dir_url(__FILE__));
define('SITE_COMPILE_Radinapp', 'https://digiappsaz.com/');
define('APP_Radinapp_IMG', trailingslashit('APP_Radinapp_DIR' . 'img'));
define('APP_Radinapp_ASSETS', trailingslashit('APP_Radinapp_DIR' . 'assets'));
define('APP_Radinapp_ADMIN', trailingslashit('APP_Radinapp_DIR' . 'admin'));

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once('admin/function.php');
require_once 'admin/Loadjs.php';

require_once plugin_dir_path(dirname(__FILE__)) . 'radinapplight/vendor/autoload.php';
$files = glob(plugin_dir_path(dirname(__FILE__)) . 'radinapplight/includes/API/Controller' . '/*.php');
foreach ($files as $file) {
    require($file);
}


$file_class = glob(plugin_dir_path(dirname(__FILE__)) . 'radinapplight/includes/API/Class' . '/*.php');
foreach ($file_class as $file) {
    require($file);
}

add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'plugin_action_links_radinapp');

register_activation_hook(__FILE__, 'plugin_activate_radinapp');
register_deactivation_hook(__FILE__, 'plugin_deactivate_radinapp');

function plugin_activate_radinapp()
{
    global $wpdb;
    $create_table_home = "
            CREATE TABLE IF NOT EXISTS {$wpdb->prefix}digiappsaz_home_page (
            id int(11) NOT NULL AUTO_INCREMENT,
            post_title varchar(512) COLLATE utf8_persian_ci NULL,
            post_type varchar(5000) COLLATE utf8_persian_ci NOT NULL,
            image_url varchar(5000) COLLATE utf8_persian_ci  NULL,
            content_type varchar(5000) COLLATE utf8_persian_ci  NULL,
            content text NULL,
            item_order int(11) NOT NULL default 0,
            image_small varchar(5000) COLLATE utf8_persian_ci  NULL,
            PRIMARY KEY (id)
            ) CHARSET=utf8 COLLATE=utf8_persian_ci;";


    dbDelta($create_table_home);
    add_option('dapp_SECRETKEY', GenerateChar_Radinapp(10), '', 'no');
}


add_action('init', 'Radinapp_init');
function Radinapp_init()
{
    register_post_type('slider_app');
}


function plugin_action_links_radinapp($links)
{
    $links[] = '<a href="http://digiappsaz.com/" target="_blank" style="font-weight: bold;color: #0b9e28;background-color: #EEEEEE;border-radius: 5px;padding: 10px">' . __('دریافت نسخه پرو', 'digiappsaz') . '</a>';
    return $links;
}


function load_Radinapp()
{
    load_plugin_textdomain('digiappsaz', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

add_action('plugins_loaded', 'load_Radinapp');

add_action('admin_menu', 'menu_admin_Radinapp');
function menu_admin_Radinapp()
{

    add_menu_page(
        __('رادین اپ', 'digiappsaz'),
        __('رادین اپ', 'digiappsaz'),
        null,
        'appsaz',
        'home_plugin_app',
        plugin_dir_url(__FILE__) . 'img/digiappsaz.png',
        'test_init'
    );

    add_submenu_page(
        'appsaz',
        __('slider app', 'digiappsaz'),
        __('slider app', 'digiappsaz'),
        'manage_options',
        'appsaz_slider',
        'home_plugin_app',
        1
    );

    add_submenu_page(
        'appsaz',
        __('home page app', 'digiappsaz'),
        __('home page app', 'digiappsaz'),
        'manage_options',
        'home_page_app',
        'get_data_home_page',
        2
    );

    add_submenu_page(
        'appsaz',
        __('download app', 'digiappsaz'),
        __('download app', 'digiappsaz'),
        'manage_options',
        'download_app',
        'form_download_app',
        4
    );

    add_submenu_page(
        'appsaz',
        __('setting', 'digiappsaz'),
        __('setting', 'digiappsaz'),
        'manage_options',
        'setting_app',
        'setting_app',
        5
    );

    add_submenu_page(
        'appsaz',
        __('دریافت نسخه پولی', 'digiappsaz'),
        __('دریافت نسخه پولی', 'digiappsaz'),
        'manage_options',
        'get_pro_radinapp',
        'page_pro_radinapp',
        5
    );


}

function plugin_deactivate_radinapp()
{

}


function GenerateChar_Radinapp($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($characters);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $characters[wp_rand(0, $len - 1)];
    }

    return $str;
}


//add_action('activated_plugin', 'my_save_error');
//function my_save_error()
//{
//    file_put_contents(dirname(__file__) . '/error_activation1.txt', ob_get_contents());
//}


function enqueue_scripts_radinapp()
{
    do_action('digiappsaz_load_scripts');
}

add_action('admin_enqueue_scripts', 'enqueue_scripts_radinapp');



