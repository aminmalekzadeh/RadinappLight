<?php

function setting_app()
{
    ?>
    <div class="wrap">
        <br>
        <table class="widefat">
            <thead>
            <tr>
                <th>افزدون آیتم به صفحه تنظیمات اپلیکیشن</th>
            </tr>
            </thead>
            <tbody>
            <form action="<?php echo admin_url('admin-post.php?action=add_item_setting_digiappsaz') ?>"
                  method="post">


                <tr>
                    <td>
                        نام آیتم
                    </td>
                    <td>
                        <input type="text" placeholder="نام آیتم را وارد کنید"
                               name="digiappsaz_name_item">
                    </td>
                </tr>
                <tr>
                    <td>
                        لینک آیتم
                    </td>
                    <td>
                        <input type="text" placeholder="لینک آیتم را وارد کنید"
                               name="digiappsaz_link_item">
                    </td>
                </tr>
                <tr>
                    <td>
                        آیکون آیتم
                    </td>
                    <td>
                        <input style="text-align:left;direction:ltr" id="appsaz_upload_image_edit"
                               class="digiappsaz_upload_image_products"
                               type="text" value="" name="icon_url"/>&nbsp;
                        <input id="appsaz_upload_button_edit" type="button" class="button-primary"
                               value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" value="<?php esc_html_e('Submit') ?>"
                               class="button button-primary"></td>
                </tr>
            </form>
            </tbody>
        </table>
        <br/>

        <table class="widefat">
            <thead>
            <tr>
                <th>آیکون</th>
                <th>نام آيتم</th>
                <th>لینک آیتم</th>
                <th><?php esc_html_e('action', 'digiappsaz'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (get_posts(['post_type' => 'digiappsaz_setting']) as $post): ?>
                <tr>
                    <td><img src="<?php echo get_home_url().$post->post_content; ?>" width="50" height="50"/></td>
                    <td><?php echo $post->post_title; ?></td>
                    <td><?php echo $post->post_excerpt; ?></td>
                    <td>
                        <form class="form" method="post" action="<?php echo admin_url('admin-post.php?action=delete_item_setting')?>">
                            <input class="button button-link-delete" type="submit"
                                    name="delete_item_setting" value="<?php esc_html_e('remove', 'digiappsaz') ?>"/>
                            <input type="hidden" value="<?php echo $post->ID;?>" name="this_item_remove_setting"/>
                            <button class="button button-primary" type="button"
                                    id="edit_item_setting_digiappsaz">
                                <?php esc_html_e('ویرایش', 'digiappsaz') ?>
                            </button>
                        </form>
                        <input type="hidden" id="post_id_setting" value="<?php echo $post->ID ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <div id="edit_item_setting_digiappsaz_modal" class="modal">
        <div class="modal-content">
            <img class="close2" src="https://img.icons8.com/color/25/000000/close-window.png"/>

            <div class="modal-body">
                <form method="post" action="<?php echo admin_url('admin-post.php?action=edit_digiappsaz_item_setting')?>"
                <label for="edit_item_setting_title">
                    نام آیتم
                </label><br/>
                <input name="edit_setting_title" id="edit_item_setting_title" type="text"/><br/>
                <label for="edit_item_setting_link">
                    لینک آیتم
                </label><br/>
                <input name="edit_setting_link" id="edit_item_setting_link" type="text"/><br/><br/>

                <label for="edit_setting_digiappsaz_img">
                    انتخاب آیکون
                </label><br/>
                <input style="text-align:left;direction:ltr" id="edit_setting_digiappsaz_img"
                       class="digiappsaz_upload_image_products"
                       type="text" value="" name="icon_url_setting"/>&nbsp;
                <input type="button" class="appsaz_upload_button_edit button-primary"
                       value="<?php esc_html_e('Choose image', 'digiappsaz') ?>"/><br/><br/>

                <input type="hidden" name="post_id" id="item_id_setting">
                <input type="submit" id="submit_item_edit_digiappsaz" class="button button-primary" value="<?php esc_html_e('ویرایش' ,'digiappsaz') ?>"/>
            </div>
        </div>
    </div>
    </div>
    <?php
}
