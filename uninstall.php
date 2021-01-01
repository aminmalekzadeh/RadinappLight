<?php


if (!defined( 'WP_UNINSTALL_PLUGIN' )){
    die();
}

global $wpdb;

delete_option('dapp_SECRETKEY');

unregister_post_type('slider_app');
unregister_post_type('digiappsaz_setting_item');
$wpdb->delete($wpdb->prefix.'posts',['post_type' => 'slider_app']);
$wpdb->query("Drop TABLE IF EXISTS  {$wpdb->prefix}digiappsaz_home_page");




