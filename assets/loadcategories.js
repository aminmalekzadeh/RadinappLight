jQuery(document).ready(function ($) {
    if ($('#select_cat').length > 0) {
        $('#select_cat').select2({
            placeholder: "جستجوی دسته بندی...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('#select_cat').on("click", function () {
            $('#select_cat').select2({
                placeholder: "جستجوی دسته بندی...",
                minimumInputLength: 3
            });
        });
    }


    if ($('#select_cat_edit').length > 0) {
        $('#select_cat_edit').select2({
            placeholder: "جستجوی دسته بندی...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('#select_cat_edit').on("click", function () {
            $('#select_cat_edit').select2({
                placeholder: "جستجوی دسته بندی...",
                minimumInputLength: 3
            });
        });
    }

    if ($('.digiappsaz_select_cat_image').length > 0) {
        $('.digiappsaz_select_cat_image').select2({
            placeholder: "جستجوی دسته بندی...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('.digiappsaz_select_cat_image').on("click", function () {
            $('.digiappsaz_select_cat_image').select2({
                placeholder: "جستجوی دسته بندی...",
                minimumInputLength: 3
            });
        });
    }

    if ($('#select_box_products').length > 0) {
        $('#select_box_products').select2({
            placeholder: "جستجوی ...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('#select_box_products').on("click", function () {
            $('#select_box_products').select2({
                placeholder: "جستجوی ...",
                minimumInputLength: 3
            });
        });
    }


    if ($('.digiappsaz_search_product_image').length > 0) {
        $('.digiappsaz_search_product_image').select2({
            placeholder: "جستجوی محصول...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('.digiappsaz_search_product_image').on("click", function () {
            $('.digiappsaz_search_product_image').select2({
                placeholder: "جستجوی محصول...",
                minimumInputLength: 3
            });
        });
    }

    if ($('#search_products_custome').length > 0) {
        $('#search_products_custome').select2({
            placeholder: "جستجوی محصولات...",
            minimumInputLength: 3,
            width: 'resolve',
        });

        $('#search_products_custome').on("click", function () {
            $('#search_products_custome').select2({
                placeholder: "جستجوی محصولات...",
                minimumInputLength: 3
            });
        });
    }



    if ($('#search_user').length > 0) {
        $('#search_user').select2({
            placeholder: "جستجوی کاربر...",
            minimumInputLength: 3,
        });

        $('#search_user').on("click", function () {
            $('#search_user').select2({
                placeholder: "جستجوی کاربر...",
                minimumInputLength: 3
            });
        });
    }

    if ($('#start_time').length > 0) {
        $(' #start_time').select2({
            placeholder: "انتخاب کنید",
            width: 100,
        });

        $(' #start_time').on("click", function () {
            $('select').select2({
                placeholder: "انتخاب کنید",
                width: 100,
            });
        });
    }

    if ($('#close_time').length > 0) {
        $(' #close_time').select2({
            placeholder: "انتخاب کنید",
            width: 100,
        });

        $(' #close_time').on("click", function () {
            $('select').select2({
                placeholder: "انتخاب کنید",
                width: 100,
            });
        });
    }



});


