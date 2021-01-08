<?php

function formSubmit()
{
    global $pagenow;
    $my_post = array(
        'post_title' => sanitize_title($_POST['title_slider']),
        'guid' => str_replace(get_home_url(), '', sanitize_text_field($_POST['slider_image'])),
        'post_content' => sanitize_url($_POST['link_slider']),
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_category' => array(),
        'post_type' => 'slider_app',
    );
    $insert = wp_insert_post($my_post);
    if (is_wp_error($insert)) {
        echo 'error';
    } else {
        wp_redirect(admin_url('admin.php?page=appsaz_slider'));
        if ($pagenow == 'admin.php?page=appsaz_slider') {
            sample_admin_notice__success();
        }
    }

}

add_action('admin_post_save', 'formSubmit');
add_action('admin_post_nopriv_save', 'formSubmit');

function sample_admin_notice__success()
{
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('اسلایدر با موفقیت آپلود شد', 'sample-text-domain'); ?></p>
    </div>
    <?php
    return add_action('admin_notices', 'sample_admin_notice__success');
}

function getSliders13()
{
    $posts = get_posts(
        [
            'post_type' => "slider_app",
            'numberposts' => -1
        ]
    );
    $arr = array();
    foreach ($posts as $post) {
        $arr[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'image' => $post->guid
        ];
    }
    return $arr;
}

add_action('admin_post_delete_slider', 'removeSlider');
add_action('admin_post_nopriv_delete_slider', 'removeSlider');
function removeSlider()
{
    wp_delete_post(sanitize_text_field($_POST['delete_slider']), true);
    wp_redirect(admin_url('admin.php?page=appsaz_slider'));
}


function add_content_home()
{
    global $wpdb;
    $content = null;

    if (isset($_POST['content'])) {
        $content = json_encode(sanitize_text_field($_POST['content']));
    } elseif (isset($_POST['content_products'])) {
        $content = json_encode(sanitize_text_field($_POST['content_products']));
    } else {
        echo "null";
    }


    $tablename = $wpdb->prefix . 'digiappsaz_home_page';
    $wpdb->insert(
        $tablename,
        array(
            'post_title' => sanitize_title($_POST['post_title']),
            'post_type' => sanitize_text_field($_POST['post_type']),
            'image_url' => str_replace(get_home_url(), '', sanitize_text_field($_POST['image_url'])),
            'content_type' => sanitize_text_field($_POST['content_type']),
            'content' => $content,
            'image_small' => (sanitize_text_field($_POST['post_type']) == 'image' || sanitize_text_field($_POST['recycler_custom_product'])) ? null : str_replace(get_home_url(), '', sanitize_text_field($_POST['image_small'])),
        )
    );
    wp_redirect(admin_url('admin.php?page=home_page_app'));
}

add_action('admin_post_add_content_home', 'add_content_home');
add_action('admin_post_nopriv_add_content_home', 'add_content_home');

function getListHomeData()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'digiappsaz_home_page';

    $data = $wpdb->get_results("SELECT * FROM $tablename ORDER BY item_order", 'ARRAY_A');

    return $data;
}

function updateDataHome()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'digiappsaz_home_page';
    $id_ary = sanitize_text_field($_POST["selecteditem"]);
    for ($i = 0; $i < count($id_ary); $i++) {
        $wpdb->update($tablename, ['item_order' => $i], ['id' => $id_ary[$i]]);
    }

}

add_action('wp_ajax_nopriv_order_content', 'updateDataHome');
add_action('wp_ajax_order_content', 'updateDataHome');

function deleteDataHome()
{
    global $wpdb;
    $item_id = sanitize_text_field($_POST['id_item_home']);
    $tablename = $wpdb->prefix . 'digiappsaz_home_page';

    $wpdb->delete($tablename, ['id' => $item_id]);
    wp_redirect(admin_url('admin.php?page=home_page_app&tab=lists_data_home_page'));
}

add_action('admin_post_nopriv_delete_data_home', 'deleteDataHome');
add_action('admin_post_delete_data_home', 'deleteDataHome');

function ListMessage()
{
    $comments = get_posts(array(
        'post_type' => 'digiappsaz_message',
    ));

    return $comments;
}

function DeleteMessage()
{
    $comment_id = sanitize_text_field($_POST['id_item_message']);
    wp_delete_post($comment_id, true);
    wp_redirect(admin_url('admin.php?page=send_message'));
}

add_action('admin_post_nopriv_delete_message', 'DeleteMessage');
add_action('admin_post_delete_message', 'DeleteMessage');


function update_homepage_digiappsaz()
{
    global $wpdb;


    $wpdb->update($wpdb->prefix . 'digiappsaz_home_page', [
        'post_title' => sanitize_text_field($_POST['post_title']),
        'image_url' => str_replace(get_home_url(), '', sanitize_text_field($_POST['image_url'])),
        'post_type' => sanitize_text_field($_POST['post_type']),
        'content_type' => sanitize_text_field($_POST['content_type']),
        'image_small' => str_replace(get_home_url(), '', $_POST['image_small']),
        'content' => serialize(sanitize_text_field($_POST['content']))
    ], ['id' => intval($_POST['postid'])]);
    wp_redirect(admin_url('admin.php?page=home_page_app&tab=lists_data_home_page'));

}

add_action('admin_post_nopriv_update_home_page', 'update_homepage_digiappsaz');
add_action('admin_post_update_home_page', 'update_homepage_digiappsaz');

function add_item_setting_digiappsaz()
{
    if (isset($_POST['digiappsaz_name_item']) and isset($_POST['icon_url']) and isset($_POST['digiappsaz_link_item'])) {
        wp_insert_post([
            'post_title' => sanitize_title($_POST['digiappsaz_name_item']),
            'post_type' => 'digiappsaz_setting',
            'post_content' => str_replace(get_home_url(), '', sanitize_text_field($_POST['icon_url'])),
            'post_excerpt' => sanitize_text_field($_POST['digiappsaz_link_item']),
            'post_status' => 'publish'
        ]);
    }
    wp_redirect(admin_url('admin.php?page=setting_app'));
}

add_action('admin_post_nopriv_add_item_setting_digiappsaz', 'add_item_setting_digiappsaz');
add_action('admin_post_add_item_setting_digiappsaz', 'add_item_setting_digiappsaz');

function delete_items_setting_digiappsaz()
{
    if (sanitize_text_field($_POST['delete_item_setting'])) {
        wp_delete_post($_POST['this_item_remove_setting'], true);
    }
    wp_redirect(admin_url('admin.php?page=setting_app'));
}

add_action('admin_post_nopriv_delete_item_setting', 'delete_items_setting_digiappsaz');
add_action('admin_post_delete_item_setting', 'delete_items_setting_digiappsaz');

function update_items_setting_digiappsaz()
{
    if (isset($_POST['edit_setting_title']) and isset($_POST['icon_url_setting']) and isset($_POST['edit_setting_link'])) {
       $update_item =  wp_update_post([
            'ID' => intval($_POST['post_id']),
            'post_title' => sanitize_title($_POST['edit_setting_title']),
            'post_content' => str_replace(get_home_url(), '', sanitize_text_field($_POST['icon_url_setting'])),
            'post_excerpt' => sanitize_text_field($_POST['edit_setting_link'])
        ]);
    }
    wp_redirect(admin_url('admin.php?page=setting_app'));
}
add_action('admin_post_nopriv_edit_digiappsaz_item_setting', 'update_items_setting_digiappsaz');
add_action('admin_post_edit_digiappsaz_item_setting', 'update_items_setting_digiappsaz');
