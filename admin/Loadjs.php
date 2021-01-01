<?php

function loadScript_digiappsaz(){
    wp_register_style('select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all');
    wp_register_script('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array('jquery'), '1.0', true);
    wp_enqueue_style('select2css');
    wp_enqueue_script('select2');
    wp_enqueue_media();
    wp_enqueue_style('style_admin_digiappsaz',APP_Radinapp_DIR .'/assets/css/style_admin.css');
    wp_enqueue_script('myscript_digiappsaz',APP_Radinapp_DIR.'/assets/myscript.js',array('jquery'),'1.2',false);
    wp_enqueue_script('load_select2_digiappsaz',APP_Radinapp_DIR.'/assets/loadcategories.js');
    wp_enqueue_script('js_color_digiappsaz',APP_Radinapp_DIR . '/assets/jscolor.js');
    wp_enqueue_script('alert_js_2',"https://cdn.jsdelivr.net/npm/sweetalert2@10.0.1/dist/sweetalert2.all.min.js");
    wp_enqueue_script('alert_jsdelivr',"https://cdn.jsdelivr.net/npm/promise-polyfill");
}
add_action('digiappsaz_load_scripts','loadScript_digiappsaz');
