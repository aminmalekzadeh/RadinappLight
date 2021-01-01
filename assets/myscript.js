
jQuery(document).ready(function ($) {
    var mediaUploader;
    $('#appsaz_upload_button').click(function () {
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'یک تصویر انتخاب کنید',
            button: {
                text: 'یک تصویر انتخاب کنید'
            }, multiple: false
        });
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            var data = $('#appsaz_upload_image').val(attachment.url);
        });
        mediaUploader.open();
    });


    var mediaUploader2;
    $('#appsaz_upload_button2').click(function () {
        if (mediaUploader2) {
            mediaUploader2.open();
            return;
        }
        mediaUploader2 = wp.media.frames.file_frame = wp.media({
            title: 'یک تصویر انتخاب کنید',
            button: {
                text: 'یک تصویر انتخاب کنید'
            }, multiple: false
        });
        mediaUploader2.on('select', function () {
            var attachment = mediaUploader2.state().get('selection').first().toJSON();
            var data = $('#appsaz_upload_image_small').val(attachment.url);
            console.log(attachment);
        });
        mediaUploader2.open();
    });


    var mediaUploader3;
    $('#appsaz_upload_button_edit').click(function () {
        if (mediaUploader3) {
            mediaUploader3.open();
            return;
        }
        mediaUploader3 = wp.media.frames.file_frame = wp.media({
            title: 'یک تصویر انتخاب کنید',
            button: {
                text: 'یک تصویر انتخاب کنید'
            }, multiple: false
        });
        mediaUploader3.on('select', function () {
            var attachment = mediaUploader3.state().get('selection').first().toJSON();
            var data = $('#appsaz_upload_image_edit').val(attachment.url);
            console.log(attachment);
        });
        mediaUploader3.open();
    });

    var mediaUploader4;
    $('.appsaz_upload_button_edit').click(function () {
        if (mediaUploader4) {
            mediaUploader4.open();
            return;
        }
        mediaUploader4 = wp.media.frames.file_frame = wp.media({
            title: 'یک تصویر انتخاب کنید',
            button: {
                text: 'یک تصویر انتخاب کنید'
            }, multiple: false
        });
        mediaUploader4.on('select', function () {
            var attachment = mediaUploader4.state().get('selection').first().toJSON();
            var data = $('#edit_setting_digiappsaz_img').val(attachment.url);
            console.log(attachment);
        });
        mediaUploader4.open();
    });



    $("#post_type")
        .change(function () {
            $("#post_type #select_product_list:selected").each(function () {
                $('#select_cat').prop("disabled", false);
                $('#select_cat').show();
                $('#search_products_custome').prop("disabled", "disabled");
                $('#content_type').prop("disabled", false);


            });

            $("#post_type #select_product_custom_list:selected").each(function () {
                $('#select_cat').prop("disabled", "disabled");
                $('#select_cat').hide();
                $('#search_products_custome').prop("disabled", false);
                $('#content_type').prop("disabled", false);


            });
        })
        .trigger("change");

    $('#content_type').change(function () {
        $("#content_type #web_view:selected").each(function () {
            $('#search_product').prop('disabled', 'disabled');
            $('#link_open_image').prop('disabled', false);
            $('#select_cat').prop('disabled', 'disabled');

        });

        $("#content_type #cat:selected").each(function () {
            $('#search_product').prop('disabled', 'disabled');
            $('#link_open_image').prop('disabled', 'disabled');
            $('#select_cat').prop('disabled', false);

        });

        $("#content_type #product:selected").each(function () {
            $('#search_product').prop('disabled', false);
            $('#link_open_image').prop('disabled', 'disabled');
            $('#select_cat').prop('disabled', 'disabled');

        });

    }).trigger("change");


    $(function () {
        $("#sortable").sortable({
            update: function (event, ui) {
                var selecteditem = [];
                $('#sortable tr').each(function () {
                    selecteditem.push($(this).attr("id"));
                });

                $.ajax({
                    url: 'admin-ajax.php?action=order_content',
                    method: "POST",
                    data: {selecteditem: selecteditem},
                    error: function () {
                        alert("error");
                    }
                });
            }
        });
    });










    $(this).on("click", '#edit_home_page_digiappsaz', function () {
        $('#edit_home_page_modal').show();

        var row = $(this).closest('tr');
        var columns = row.find('td');
        var value = columns.find("p#getpostid");
        var post_title = columns.find("p#posttitle");
        var image_src = columns.find('img#get_image_src');
        var content_type = columns.find('input#content_type_select');
        var post_type = columns.find('input#post_type_digiappsaz');
        var contents = columns.find('input#list_content_home_page_digiappsaz');
        var image_small = columns.find('input#get_image_small_src_product');
        var post_id_home_digiappsaz;
        post_id_home_digiappsaz = value[0].innerText;

        $('#post_id_digiappsaz').val(post_id_home_digiappsaz);
        $('#post_id_digiappsaz_pro').val(post_id_home_digiappsaz);

        if ((post_type[0].value === 'recycler_product') || (post_type[0].value === 'recycler_custom_product')) {
            $('#table_edit_home_page_images').hide();
            $('#table_edit_home_page_products').show();
            $('#table_edit_home_page_products').children().prop('disabled', false);
            $('#table_edit_home_page_images').children().prop('disabled', true);
        } else {
            $('#table_edit_home_page_products').hide();
            $('#table_edit_home_page_images').show();
            $('#table_edit_home_page_products').children().prop('disabled', true);
            $('#table_edit_home_page_images').children().prop('disabled', false);
        }
        $('#post_type_image').val(post_type[0].value).change();
        $('#post_type_products').val(post_type[0].value).change();

        $('#appsaz_upload_image_small').val(image_small[0].value);

        $("#post_type_products")
            .change(function () {
                $("#post_type_products #select_product_list:selected").each(function () {
                    $('.digiappsaz_select_cat_products').prop("disabled", false);
                    $('.digiappsaz_select_product_products').prop("disabled", "disabled");
                });

                $("#post_type_products #select_product_custom_list:selected").each(function () {
                    $('.digiappsaz_select_cat_products').prop("disabled", "disabled");
                    $('.digiappsaz_select_product_products').prop("disabled", false);
                });
            })
            .trigger("change");


        $('.digiappsaz_post_title_image').val(post_title[0].innerText);
        $('.digiappsaz_post_title_products').val(post_title[0].innerText);
        $('.digiappsaz_upload_image_image').val(image_src[0].currentSrc);
        $('.digiappsaz_upload_image_products').val(image_src[0].currentSrc);

        $('#content_type_image').change(function () {
            $("#content_type_image #web_view:selected").each(function () {
                // $('.digiappsaz_select_cat_image').prop('disabled', true);
                $('.digiappsaz_link_open_image').prop('disabled', false);
                $('.digiappsaz_link_open_image').attr('id','digiappsaz_content_image');
                $('.digiappsaz_search_product_image').prop('disabled', true);
                $('.digiappsaz_search_product_image').removeAttr('id');
                $('.digiappsaz_select_cat_image').prop('disabled', true);
                $('.digiappsaz_select_cat_image').removeAttr('id');
            });

            $("#content_type_image #cat:selected").each(function () {
                $('.digiappsaz_select_cat_image').prop('disabled', false);
                $('.digiappsaz_select_cat_image').attr('id','digiappsaz_content_image');
                $('.digiappsaz_link_open_image').prop('disabled', true);
                $('.digiappsaz_link_open_image').removeAttr('id');
                $('.digiappsaz_search_product_image').prop('disabled', true);
                $('.digiappsaz_search_product_image').removeAttr('id');
            });
            $("#content_type_image #product:selected").each(function () {
                $('.digiappsaz_select_cat_image').prop('disabled', true);
                $('.digiappsaz_select_cat_image').removeAttr('id');
                $('.digiappsaz_link_open_image').prop('disabled', true);
                $('.digiappsaz_link_open_image').removeAttr('id');
                $('.digiappsaz_search_product_image').prop('disabled', false);
                $('.digiappsaz_search_product_image').attr('id','digiappsaz_content_image');
            });
        }).trigger("change");

        $('#content_type_image').val(content_type[0].value).change();


        $('.digiappsaz_select_cat_image').val(contents[0].value.split(","));
        $('.digiappsaz_select_cat_image').trigger('change');
        $('.digiappsaz_select_cat_products').val(contents[0].value.split(","));
        $('.digiappsaz_select_cat_products').trigger('change');

        $('.digiappsaz_select_product_products').val(contents[0].value.split(","));
        $('.digiappsaz_select_product_products').trigger('change');
        $('.digiappsaz_search_product_image').val(contents[0].value.split(","));
        $('.digiappsaz_search_product_image').trigger('change');

        if (content_type[0].value == 'web_view') {
            $('.digiappsaz_link_open_image').val(contents[0].value);
            $('.digiappsaz_link_open_image').trigger("change");
        }

    });


    $('.close1').on("click", function () {
        $('#edit_home_page_modal').hide();
    });



    $(this).on("click","#edit_item_setting_digiappsaz",function () {
        $('#edit_item_setting_digiappsaz_modal').show();
        var row = $(this).closest('tr');
        var columns = row.find('td');
        var post_id = columns.find("input#post_id_setting");
        $('#edit_item_setting_title').val(columns[1].innerText);
        $('#edit_item_setting_link').val(columns[2].innerText);
        $("#edit_setting_digiappsaz_img").val(columns[0].lastChild.currentSrc);
        $('#item_id_setting').val(post_id[0].value);
        console.log(columns);
    });

    $('.close2').on("click",function () {
        $('#edit_item_setting_digiappsaz_modal').hide();
    })

    $('#submit_compile').on("click",function () {
        $('#myModal').show();
        $('.close').hide();
    })



    $("input[name='icon_app']").on("input",function () {
        var input = $(this);
        var format = this.files[0];
        // if (format.size > 1000000){
        //     Swal.fire({
        //         title: 'حجم فایل بیش از حد مجاز است',
        //         text: "حجم فایل باید کمتر از 1MB باشد",
        //         icon: 'error',
        //         confirmButtonText: 'تایید'
        //     });
        //     input.val(null);
        // }
        if (format['type'] !== "image/png" && format['type'] !== "image/jpeg" && format['type'] !== "image/jpg"){
            Swal.fire({
                title: 'خطای فرمت تصویر',
                text: "پسوند تصویر باید jpg یا png باشد",
                icon: 'error',
                confirmButtonText: 'تایید'
            });
            input.val(null);
        }
    })

    $("input[name='image_search']").on("input",function () {
        var input = $(this);
        var format = this.files[0];
        // if (format.size > 1000000){
        //     Swal.fire({
        //         title: 'حجم فایل بیش از حد مجاز است',
        //         text: "حجم فایل باید کمتر از 1MB باشد",
        //         icon: 'error',
        //         confirmButtonText: 'تایید'
        //     });
        //     input.val(null);
        // }
        if (format['type'] !== "image/png" && format['type'] !== "image/jpeg" && format['type'] !== "image/jpg"){
            Swal.fire({
                title: 'خطای فرمت تصویر',
                text: "پسوند تصویر باید jpg یا png باشد",
                icon: 'error',
                confirmButtonText: 'تایید'
            });
            input.val(null);
        }
    })

    $("input[name='logo_app']").on("input",function () {
        var input = $(this);
        var format = this.files[0];
        // if (format.size > 1000000){
        //     Swal.fire({
        //         title: 'حجم فایل بیش از حد مجاز است',
        //         text: "حجم فایل باید کمتر از 1MB باشد",
        //         icon: 'error',
        //         confirmButtonText: 'تایید'
        //     });
        //     input.val(null);
        // }
        if (format['type'] !== "image/png" && format['type'] !== "image/jpeg" && format['type'] !== "image/jpg"){
            Swal.fire({
                title: 'خطای فرمت تصویر',
                text: "پسوند تصویر باید jpg یا png باشد",
                icon: 'error',
                confirmButtonText: 'تایید'
            });
            input.val(null);
        }
    })

        $("#link_digiappsaz").on("click",function () {
            location.href = "https://digiappsaz.com"
        })


});


