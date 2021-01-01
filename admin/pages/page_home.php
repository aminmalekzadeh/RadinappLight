<?php
global $tab_title_dpp;

function home_plugin_app()
{
    ?>

    <div class="wrap">
        <h2><?php esc_html_e('slider', 'digiappsaz') ?></h2>
        <form method="post" action="<?php echo admin_url('admin-post.php?action=save') ?>"
              class="validate" novalidate="novalidate">
            <input type="hidden" id='action' name="action" value="save">

            <table class="widefat">
                <thead>
                <tr>
                    <th colspan="۲"><?php esc_html_e('slider', 'digiappsaz') ?></th>
                </tr>
                </thead>
                <tr>
                    <td><?php esc_html_e('image slider', 'digiappsaz') ?></td>
                    <td>
                        <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                               type="text" name="slider_image"/>
                        <button id="appsaz_upload_button" type="button" class="button-primary">
                            <?php esc_html_e('Choose slider image', 'digiappsaz') ?>
                        </button>
                    </td>

                </tr>

                <tr>
                    <td><?php esc_html_e('slider title', 'digiappsaz') ?></td>
                    <td>
                        <input type="text" name="title_slider"
                               placeholder="<?php esc_html_e('slider title', 'digiappsaz') ?>">
                    </td>
                </tr>

                <tr>
                    <td>لینک</td>
                    <td>
                        <input type="text" name="link_slider"
                               placeholder="یک لینک در زمان کلیک اسلایدر وارد کنید">
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td colspan="۲">
                        <input type="submit" name="save"
                               value="<?php esc_html_e('save', 'digiappsaz') ?>"
                               class="button-primary"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>


    <div class="wrap">
        <table id="posts" class="widefat">
            <thead>
            <th colspan="2"><?php esc_html_e('list sliders', 'digiappsaz') ?></th>
            </tr>
            </thead>
            <tr>
                <th><b><?php esc_html_e('ID', 'digiappsaz') ?></b></th>
                <th><b><?php esc_html_e('image', 'digiappsaz') ?></b></th>
                <th><b><?php esc_html_e('title', 'digiappsaz') ?></b></th>
                <th><b><?php esc_html_e('action', 'digiappsaz') ?></b></th>
            </tr>
            <?php foreach (getSliders13() as $key => $item): ?>
                <tr>
                    <td>
                        <p id="getpostid">
                            <b>
                                <?php echo $item['id']; ?>
                            </b>
                        </p>
                    </td>
                    <td>
                        <img src="<?php echo get_home_url() . $item['image'];
                        ?>" class="images" width="100" height="100"/>
                    </td>
                    <td>
                        <p>
                            <b><?php echo $item['title']; ?></b>
                        </p>

                    </td>
                    <td>
                        <form method="post"
                              action="<?php echo admin_url('admin-post.php?action=delete_slider') ?>">
                            <button type="submit" name="delete_slider"
                                    class="button button-link-delete"
                                    value="<?php echo $item['id'] ?>">
                                حذف
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php
}


function get_data_home_page()
{
    global $tab_title_dpp;
    $tab_title_dpp = __('Manage Home Page', 'digiappsaz');
    ?>

    <div class="wrap">
        <h2><?php echo $tab_title_dpp; ?> </h2>

        <form method="post"
              action="<?php echo admin_url('admin-post.php?action=add_content_home') ?>"
              class="validate" novalidate="novalidate">
            <table class="widefat">
                <?php
                tabs_home_page_admin();

                if (isset($_GET['tab'])) {
                    $tab = $_GET['tab'];
                    if ($tab == 'add_products_at_home') {
                        add_products_home();
                    } elseif ($tab == 'add_image_at_home') {
                        add_image_home();
                    } elseif ($tab == 'lists_data_home_page') {
                        lists_data_home();
                    } else {
                        add_image_home();
                    }
                } else {
                    add_image_home();
                }

                ?>
            </table>
        </form>

    </div>

    <?php
}

function tabs_home_page_admin()
{
    global $current;

    $tabs = array(
        'add_image_at_home' => __('add image', 'digiappsaz'),
        'add_products_at_home' => __('add list products', 'digiappsaz'),
        'lists_data_home_page' => __('Manage Home Page', 'digiappsaz')
    );
    $html = '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ($tab == $current) ? 'home_page_app' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="?page=home_page_app&tab=' . $tab . '">' . $name . '</a>';
    }
    $html .= '</h2>';
    echo $html;
}

function add_products_home()
{
    global $tab_title_dpp;
    $tab_title_dpp = __('add list products', 'digiappsaz')
    ?>
    <thead>
    <tr>
        <th><?php echo $tab_title_dpp; ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php esc_html_e('title', 'digiappsaz') ?></td>
        <td>
            <input type="text" name="post_title"
                   placeholder="<?php esc_html_e('title', 'digiappsaz') ?>">
        </td>
    </tr>

    <tr id="choose_content_type_title">
        <td><?php esc_html_e('content type', 'digiappsaz') ?></td>
        <td id="choose_content_type">
            <select id="post_type" style=" width: 50%;" name="post_type">
                <option id="select_product_list" value="recycler_product">
                    <?php esc_html_e('list products from category', 'digiappsaz') ?>
                </option>
                <option id="select_product_custom_list" value="recycler_custom_product">
                    <?php esc_html_e('list product custom', 'digiappsaz') ?>
                </option>
            </select>
        </td>
    </tr>

    <tr id="choose_content_type_title">
        <td><?php esc_html_e('Choose view products', 'digiappsaz') ?></td>
        <td>
            <input type="radio" class="radio customize-control-radio" id="radio1"
                   value="products_list" name="content_type" checked>
            <label for="radio1"> <?php esc_html_e('list view', 'digiappsaz') ?> </label><br>
            <input type="radio" id="radio2" value="products_square" name="content_type">
            <label for="radio2"><?php esc_html_e('Square view', 'digiappsaz') ?> </label>
        </td>
    </tr>


    <tr id="choose_select_image_title">
        <td><?php esc_html_e('choose background products list', 'digiappsaz') ?></td>
        <td id="choose_select_image">
            <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                   type="text" value="" name="image_url"/>
            <input id="appsaz_upload_button" type="button" class="button-primary"
                   value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>
        </td>
    </tr>

    <tr id="choose_select_image_title">
        <td><?php esc_html_e('select image position one', 'digiappsaz') ?></td>
        <td id="choose_select_image">
            <input style="text-align:left;direction:ltr" id="appsaz_upload_image_small"
                   type="text" name="image_small"/>
            <input id="appsaz_upload_button2" type="button" class="button-primary"
                   value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>
        </td>
    </tr>

    <tr id="search_single_product_title">
        <td><?php esc_html_e('search products', 'digiappsaz') ?></td>
        <td id="search_single_product">
            <select name="content[]" id="search_products_custome" style="width: 75%" multiple>
                <?php
                $all_ids_products = get_posts(array(
                    'post_type' => 'product',
                    'numberposts' => -1,
                    'post_status' => 'publish',
                ));
                foreach ($all_ids_products as $items) :
                    ?>
                    <option id="selected_cat"
                            value="<?php echo $items->ID; ?>"> <?php echo $items->post_title; ?> </option>
                <?php
                endforeach;
                ?>
            </select>
        </td>
    </tr>

    <tr id="select_choose_cat_title">
        <td><?php esc_html_e('Choose Category', 'digiappsaz') ?></td>
        <td id="select_choose_cat">
            <select name="content[]" id="select_cat" multiple="multiple"
                    style="width: 35%">
                <?php
                $prod_categories = get_terms('product_cat', array(
                    'orderby' => 'name',
                    'HandleOrder' => 'ASC',
                    'hide_empty' => false
                ));
                foreach ($prod_categories as $prod_cat) :
                    $cat_thumb_id = get_term_meta($prod_cat->term_id, 'thumbnail_id', true);
                    $term_link = get_term_link($prod_cat, 'product_cat');
                    ?>
                    <option id="selected_cat"
                            value="<?php echo $prod_cat->term_id ?>"> <?php echo $prod_cat->name; ?> </option>
                <?php
                endforeach;
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td colspan="۲">
            <input type="submit" name="add_content"
                   value="<?php esc_html_e('save', 'digiappsaz') ?>"
                   class="button-primary"/>
        </td>
    </tr>
    </tbody>


    <?php
}

function add_image_home()
{
    global $tab_title_dpp;
    $tab_title_dpp = __('add image', 'digiappsaz');
    ?>
    <thead>
    <tr>
        <th><?php echo $tab_title_dpp; ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php esc_html_e('title', 'digiappsaz') ?></td>
        <td>
            <input type="text" name="post_title"
                   placeholder="<?php esc_html_e('title', 'digiappsaz') ?>">
        </td>
    </tr>
    <tr id="choose_content_type_title">
        <input type="hidden" value="image" name="post_type"/>
    </tr>

    <tr id="click_content_title">
        <td><?php esc_html_e('Choose Click Content', 'digiappsaz') ?></td>
        <td id="click_content">
            <select id="content_type" class="js-example-basic-single"
                    name="content_type">
                <option id="web_view"
                        value="web_view"><?php esc_html_e('open in web', 'digiappsaz') ?></option>
                <option id="product"
                        value="product"><?php esc_html_e('product', 'digiappsaz') ?></option>
                <option id="cat"
                        value="category"><?php esc_html_e('category', 'digiappsaz') ?></option>
            </select>
        </td>
    </tr>

    <tr id="choose_select_image_title">
        <td><?php esc_html_e('Choose image', 'digiappsaz') ?></td>
        <td id="choose_select_image">
            <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                   type="text" value="" name="image_url"/>
            <input id="appsaz_upload_button" type="button" class="button-primary"
                   value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>
        </td>
    </tr>

    <tr id="search_single_product_title">
        <td><?php esc_html_e('search products', 'digiappsaz') ?></td>
        <td id="search_single_product">
            <select name="content" class="digiappsaz_search_product_image" id="search_product"
                    style="width: 75%">
                <?php
                $all_ids_products = get_posts(array(
                    'post_type' => 'product',
                    'numberposts' => -1,
                    'post_status' => 'publish',
                ));
                foreach ($all_ids_products as $items) :
                    ?>
                    <option id="selected_cat"
                            value="<?php echo $items->ID; ?>"> <?php echo $items->post_title; ?> </option>
                <?php
                endforeach;
                ?>
            </select>
        </td>
    </tr>


    <tr>
        <td id="link_title"><?php esc_html_e('link') ?></td>
        <td id="link">
            <input id="link_open_image" type="text" name="content"
                   placeholder="<?php esc_html_e('open link when click', 'digiappsaz') ?>">
        </td>
    </tr>

    <tr id="select_choose_cat_title">
        <td><?php esc_html_e('Choose Category', 'digiappsaz') ?></td>
        <td id="select_choose_cat">
            <select name="content[]" id="select_cat" multiple="multiple"
                    style="width: 35%">
                <?php
                $prod_categories = get_terms('product_cat', array(
                    'orderby' => 'name',
                    'HandleOrder' => 'ASC',
                    'hide_empty' => false
                ));
                foreach ($prod_categories as $prod_cat) :
                    $cat_thumb_id = get_term_meta($prod_cat->term_id, 'thumbnail_id', true);
                    $term_link = get_term_link($prod_cat, 'product_cat');
                    ?>
                    <option id="selected_cat"
                            value="<?php echo $prod_cat->term_id ?>"> <?php echo $prod_cat->name; ?> </option>
                <?php
                endforeach;
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="۲">
            <input type="submit" name="add_content"
                   value="<?php esc_html_e('save', 'digiappsaz') ?>"
                   class="button-primary"/>
        </td>
    </tr>
    </tbody>

    <?php
}


function lists_data_home()
{
    global $tab_title_dpp;
    $tab_title_dpp = __('Manage Home Page', 'digiappsaz')
    ?>

    <thead>
    <tr>
        <th><?php echo $tab_title_dpp; ?></th>
    </tr>
    </thead>

    <table class="widefat">
        <thead>
        <tr>
            <th><b><?php esc_html_e('drag & drop', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('ID', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('title', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('content type', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('image', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('Content Click', 'digiappsaz') ?></b></th>
            <th><b><?php esc_html_e('Content', 'digiappsaz') ?></b></th>
            <th><b<?php esc_html_e('action', 'digiappsaz') ?></b></th>
        </tr>
        </thead>
        <tbody id="sortable">
        <?php foreach (getListHomeData() as $key => $item): ?>
            <tr id="<?php echo $item['id']; ?>">
                <td>
                    <img width="20" height="20"
                         src="https://img.icons8.com/ios-filled/50/000000/drag-reorder.png"/>
                </td>
                <td>
                    <p id="getpostid">
                        <b>
                            <?php echo $item['id']; ?>
                        </b>
                    </p>
                </td>
                <td>
                    <p id="posttitle">
                        <b><?php echo $item['post_title']; ?></b>
                    </p>
                </td>

                <td>
                    <p>
                        <b><?php
                            $posttype = $item['post_type'];

                            if ($posttype == 'recycler_product')
                                echo __('list product', 'digiappsaz');
                            elseif ($posttype == 'image')
                                echo __('image', 'digiappsaz');
                            elseif ($posttype == 'recycler_custom_product')
                                echo __('list product custom', 'digiappsaz'); ?>
                        </b>
                    </p>
                    <input type="hidden" value="<?php echo $posttype ?>" id="post_type_digiappsaz">
                </td>

                <td>
                    <img src="<?php echo get_home_url() . $item['image_url']; ?>" class="images"
                         width="100"
                         height="100"
                         id="get_image_src"/>
                    <input type="hidden" value="<?php echo get_home_url() . $item['image_url']; ?>"
                           id="get_image_src_product">
                    <input type="hidden"
                           value="<?php echo get_home_url() . $item['image_small']; ?>"
                           id="get_image_small_src_product">
                </td>

                <td>
                    <p>
                        <b><?php
                            $content_type = $item['content_type'];

                            if ($content_type != null) {
                                if ($content_type == 'product') {
                                    echo __('open image in product', 'digiappsaz');
                                } elseif ($content_type == 'web_view') {
                                    echo __('open in web', 'digiappsaz');
                                } elseif ($content_type == 'category') {
                                    echo __('open in category', 'digiappsaz');
                                }
                            } else {
                                echo __('null', 'digiappsaz');
                            }
                            ?>
                        </b>
                    </p>
                    <input type="hidden" id="content_type_select"
                           value="<?php echo $content_type; ?>">
                </td>

                <td>
                    <p>
                        <b>
                            <?php
                            $content = (array)json_decode($item['content']);
                            do_action('content_digiappsaz_list', $content, $posttype, $content_type);
                            ?>
                            <input type="hidden" id="list_content_home_page_digiappsaz"
                                   value="<?php echo implode(',', $content); ?>">
                        </b>
                    </p>
                </td>

                <td>
                    <form method="post"
                          action="<?php echo admin_url('admin-post.php?action=delete_data_home') ?>">
                        <button type="submit" name="id_item_home"
                                class="button button-link-delete"
                                value="<?php echo $item['id'] ?>">
                            <?php esc_html_e('remove', 'digiappsaz') ?>
                        </button>
                    </form>
                </td>
                <td>
                    <button type="button" id="edit_home_page_digiappsaz"
                            class="button button-primary">
                        <?php esc_html_e('ویرایش') ?>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br/>
    <?php do_action('edit_modal_digiappsaz') ?>
    <input type="hidden" name="item_order" id="item_order"/>


    <?php
}


function modal_edit_home_page()
{
    ?>
    <div id="edit_home_page_modal" class="modal">
        <div class="modal-content">

            <div class="modal-body">
                <img class="close1" src="https://img.icons8.com/color/25/000000/close-window.png"/>

                <!--     MODAL EDIT HOME PAGE IMAGE          -->
                <div id="table_edit_home_page_images" class="modal-body">
                    <form class="form"
                          action="<?php echo admin_url('admin-post.php?action=update_home_page') ?>"
                          method="post">

                        <p><?php esc_html_e('title', 'digiappsaz') ?></p>
                        <input type="text" name="post_title" class="digiappsaz_post_title_image"
                               id="post_title"
                               placeholder="<?php esc_html_e('title', 'digiappsaz') ?>">

                        <input type="hidden" value="image" name="post_type"/>
                        <input type="hidden" id="post_id_digiappsaz" name="postid">
                        <input type="hidden" value="" name="image_small">
                        <p><?php esc_html_e('Choose Click Content', 'digiappsaz') ?></p>
                        <select id="content_type_image" class="js-example-basic-single"
                                name="content_type">
                            <option id="web_view"
                                    value="web_view"><?php esc_html_e('open in web', 'digiappsaz') ?></option>
                            <option id="product"
                                    value="product"><?php esc_html_e('product', 'digiappsaz') ?></option>
                            <option id="cat"
                                    value="category"><?php esc_html_e('category', 'digiappsaz') ?></option>
                        </select>
                        <p><?php esc_html_e('Choose image', 'digiappsaz') ?></p>

                        <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                               class="digiappsaz_upload_image_image"
                               type="text" value="" name="image_url"/>
                        <input id="appsaz_upload_button" type="button" class="button-primary"
                               value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>


                        <p><?php esc_html_e('search product', 'digiappsaz') ?></p>

                        <select name="content" class="digiappsaz_search_product_image"
                                style="width: 75%">
                            <?php
                            $all_ids_products = get_posts(array(
                                'post_type' => 'product',
                                'numberposts' => -1,
                                'post_status' => 'publish',
                            ));
                            foreach ($all_ids_products as $items) :
                                ?>
                                <option id="selected_cat"
                                        value="<?php echo $items->ID; ?>"> <?php echo $items->post_title; ?> </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                        <br/>

                        <label for="link_open_image"><?php esc_html_e('link') ?></label> <br/>
                        <input class="digiappsaz_link_open_image" type="text" name="content"
                               placeholder="<?php esc_html_e('open link when click', 'digiappsaz') ?>">
                        <br/>
                        <br/>

                        <select name="content[]" class="digiappsaz_select_cat_image"
                                multiple="multiple"
                                style="width: 35%">
                            <?php
                            $prod_categories = get_terms('product_cat', array(
                                'orderby' => 'name',
                                'HandleOrder' => 'ASC',
                                'hide_empty' => false
                            ));
                            foreach ($prod_categories as $prod_cat) :
                                $cat_thumb_id = get_term_meta($prod_cat->term_id, 'thumbnail_id', true);
                                $term_link = get_term_link($prod_cat, 'product_cat');
                                ?>
                                <option id="selected_cat"
                                        value="<?php echo $prod_cat->term_id ?>"> <?php echo $prod_cat->name; ?> </option>
                            <?php
                            endforeach;
                            ?>
                        </select><br/>

                        <hr/>
                        <input type="submit" id="updated_content"
                               value="<?php esc_html_e('save', 'digiappsaz') ?>"
                               class="button-primary"/>
                    </form>
                </div>

                <!--     MODAL EDIT HOME PAGE PRODUCTS          -->
                <div id="table_edit_home_page_products" class="modal-body">

                    <form action="<?php echo admin_url('admin-post.php?action=update_home_page') ?>"
                          method="post" class="form">
                        <b><label for="post_title_products"><?php esc_html_e('title', 'digiappsaz') ?></label></b>
                        <input type="text" name="post_title" class="digiappsaz_post_title_products"
                               id="post_title_products"
                               placeholder="<?php esc_html_e('title', 'digiappsaz') ?>"><br><br>


                        <b><?php esc_html_e('content type', 'digiappsaz') ?></b>

                        <input type="hidden" id="post_id_digiappsaz_pro" name="postid">
                        <select id="post_type_products" style=" width: 50%;" name="post_type">
                            <option id="select_product_list" value="recycler_product">
                                <?php esc_html_e('list products from category', 'digiappsaz') ?>
                            </option>
                            <option id="select_product_custom_list" value="recycler_custom_product">
                                <?php esc_html_e('list product custom', 'digiappsaz') ?>
                            </option>
                        </select>
                        <br>

                        <?php esc_html_e('Choose view products', 'digiappsaz') ?><br>

                        <input type="radio" class="digiappsaz_content_type_product" id="radio1"
                               value="products_list" name="content_type" checked>
                        <label for="radio1"> <?php esc_html_e('list view', 'digiappsaz') ?> </label><br>
                        <input type="radio" id="radio2" class="digiappsaz_content_type_product"
                               value="products_square" name="content_type">
                        <label for="radio2"><?php esc_html_e('Square view', 'digiappsaz') ?> </label><br>


                        <b><?php esc_html_e('choose background products list', 'digiappsaz') ?></b><br>
                        <input style="text-align:left;direction:ltr" id="appsaz_upload_image_edit"
                               class="digiappsaz_upload_image_products"
                               type="text" value="" name="image_url"/>&nbsp;
                        <input id="appsaz_upload_button_edit" type="button" class="button-primary"
                               value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>
                        <br>

                        <b><?php esc_html_e('select image position one', 'digiappsaz') ?></b><br>

                        <input style="text-align:left;direction:ltr" id="appsaz_upload_image_small"
                               type="text" name="image_small"/>&nbsp;
                        <input id="appsaz_upload_button2" type="button" class="button-primary"
                               value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/><br>

                        <br>
                        <b><?php esc_html_e('search products', 'digiappsaz') ?></b>
                        <select name="content[]" id="select_box_products"
                                class="digiappsaz_select_product_products digiappsaz_content_products"
                                style="width: 75%" multiple>
                            <?php
                            $all_ids_products = get_posts(array(
                                'post_type' => 'product',
                                'numberposts' => -1,
                                'post_status' => 'publish',
                            ));
                            foreach ($all_ids_products as $items) :
                                ?>
                                <option id="selected_cat"
                                        value="<?php echo $items->ID; ?>"> <?php echo $items->post_title; ?> </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                        <br><br>


                        <b><?php esc_html_e('Choose Category', 'digiappsaz') ?></b>
                        <select name="content[]" id="select_cat_edit"
                                class="digiappsaz_select_cat_products digiappsaz_content_products"
                                multiple="multiple"
                                style="width: 35%">
                            <?php
                            $prod_categories = get_terms('product_cat', array(
                                'orderby' => 'name',
                                'HandleOrder' => 'ASC',
                                'hide_empty' => false
                            ));
                            foreach ($prod_categories as $prod_cat) :
                                $cat_thumb_id = get_term_meta($prod_cat->term_id, 'thumbnail_id', true);
                                $term_link = get_term_link($prod_cat, 'product_cat');
                                ?>
                                <option id="selected_cat"
                                        value="<?php echo $prod_cat->term_id ?>"> <?php echo $prod_cat->name; ?> </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                        <br>
                        <hr/>

                        <input type="submit"
                               value="<?php esc_html_e('save', 'digiappsaz') ?>"
                               class="button-primary"/>
                    </form>

                </div>

            </div>
        </div>
    </div>
    <?php
}

add_action('edit_modal_digiappsaz', 'modal_edit_home_page');


function content_digiappsaz_list($content, $post_type, $content_type)
{
    $str = '';
    if ($post_type == 'image' and $content_type == 'category') {
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'include' => $content
        ]);
        $c = array();
        foreach ($terms as $term) {
            $c[] = $term->name;
        }
        $str = 'دسته بندی: ' . implode(',', $c);
        echo_tb($str);
    } elseif ($post_type == 'image' and $content_type == 'product') {
        $s = implode(',', $content);
        echo 'محصول: ' . get_post($s)->post_title;
    } elseif ($post_type == 'image' and $content_type == 'web_view') {
        $s = implode(',', $content);
        $str = 'باز شدن در وب ویو: ' . '<a href="' . $s . '">' . 'لینک' . '</a>';
        echo_tb($str);
    } elseif ($post_type == 'recycler_custom_product') {
        $posts = get_posts([
            'post_type' => 'product',
            'include' => $content
        ]);
        $p = array();
        foreach ($posts as $post) {
            $p[] = $post->post_title;
        }
        $str = 'محصولات: ' . implode(',', $p);
        echo_tb($str);
    } elseif ($post_type == 'recycler_product') {
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'include' => $content
        ]);
        $c = array();
        foreach ($terms as $term) {
            $c[] = $term->name;
        }
        $str = 'دسته بندی: ' . implode(',', $c);
        echo_tb($str);
    }
}

add_action('content_digiappsaz_list', 'content_digiappsaz_list', 10, 3);

function echo_tb($str)
{
    if (strlen($str) > 100)
        $str = substr($str, 0, 99) . '...';
    echo $str;
}


