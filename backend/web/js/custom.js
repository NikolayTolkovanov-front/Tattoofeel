$(document).ready(function () {
    // SEO сниппет
    // $('#seo-snippet a').click(function (e) {
    //     e.preventDefault();
    //     return false;
    // });

    $('#seo-title-field').on('input', function () {
        $('#seo-snippet-title').html($(this).val());
    });

    $('#seo-desc-field').on('input', function () {
        $('#seo-snippet-desc').html($(this).val());
    });
});