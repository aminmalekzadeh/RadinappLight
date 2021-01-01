<?php


function form_download_app()
{
    ?>

    <noscript>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('لطفا جاوا اسکریپت خود را از مرورگر فعال کنید', 'sample-text-domain'); ?></p>
        </div>
    </noscript>
    <div class="wrap">
        <h2><?php esc_html_e('download app', 'digiappsaz') ?></h2>
            <form method="post"
                  id="appsaz_form"

                  enctype="multipart/form-data"
                  action="<?php echo SITE_COMPILE_Radinapp . 'process' ?>">

                <table class="widefat">
                    <thead>
                    <tr>
                        <th colspan="۲"><?php esc_html_e('download', 'digiappsaz') ?></th>
                    </tr>
                    </thead>


                    <tr>
                        <td><?php esc_html_e('نام انگلیسی اپلیکیشن', 'digiappsaz') ?></td>
                        <td>
                            <input id="app_name_en" type="text" name="app_name_en"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('نام فارسی اپلیکیشن', 'digiappsaz') ?></td>
                        <td>
                            <input id="app_name_fa" type="text" name="app_name_fa" required/>
                        </td>
                    </tr>


                    <tr>
                        <td><?php esc_html_e('application icon', 'digiappsaz') ?></td>
                        <td>
                            <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                                   type="file" name="icon_app" accept="image/*" data-type='image'
                                   required/>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><?php esc_html_e('main color application', 'digiappsaz') ?></td>
                        <td>
                            <input type="text" name="color_main_app" data-jscolor="" value=""
                                   required>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <input type="hidden" name="package_name"
                           id="package_name"
                           value="<?php echo $_SERVER['HTTP_HOST']; ?>" required/>
                    <input type="hidden" id="appurl" name="appurl" value="<?php echo home_url(); ?>"
                           required/>
                    <input type="hidden" id="email_user" name="email_user"
                           value="<?php echo get_bloginfo('admin_email') ?>"/>
                    <tr>
                        <td><?php esc_html_e('secondary color application', 'digiappsaz') ?></td>
                        <td>
                            <input type="text" name="color_secondary_app" data-jscolor="" value=""
                                   required>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <script>
                        function setTextColor(picker) {
                            document.getElementsByTagName('body')[0].style.color = '#' + picker.toString()
                        }
                    </script>
                    <tr>
                        <td><?php esc_html_e('splash logo in activity splash', 'digiappsaz') ?></td>
                        <td>
                            <input style="text-align:left;direction:ltr" id="appsaz_upload_image"
                                   type="file" name="logo_app"
                                   accept="image/*"
                                   data-type='image'
                                   required/>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><?php esc_html_e('تصویر سرچ در صفحه اصلی', 'digiappsaz') ?></td>
                        <td>
                            <input style="text-align:left;direction:ltr"
                                   id="appsaz_upload_image_search"
                                   type="file" name="image_search"
                                   accept="image/*" data-type='image'/>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="۲">
                            <input type="submit" name="Submit" id="submit_compile"
                                   class="button-primary" value="<?php esc_html_e('Submit') ?>"/>
                        </td>
                    </tr>
                </table>
            </form>
    </div>


    <?php
}
