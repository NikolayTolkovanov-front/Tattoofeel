$(function(){
    var $list = $('#product-related');

    $list.on('click', '.op-del', function(e) {
        e.preventDefault();
        $(this).closest('.op-row').remove();
    })

    $('#product-search-rel').keydown(function(e){
        if(e.keyCode == 13) {
            if($(this).val() == '') {
                e.preventDefault();
                return false;
            }
            if($('.ui-autocomplete')[0].style.display == 'none') {
                e.preventDefault();
                return false;
            }
            if($('.ui-autocomplete')[0].style.display != 'none' &&
                !$('.ui-autocomplete .ui-state-active').length) {
                e.preventDefault();
                return false;
            }
        }
    });

    $('#product-search-rel').each(function () {
        var t = $(this);

        if (t.hasClass('-init')) return;
        t.addClass('-init');

        t.on('autocompletefocus',
            function () {
                return false;
            });
        t.on('autocompleteselect',
            function (e, ui) {

                var item = ui.item;
                var $row = $list.find('#prod-id-' + item.id);

                if ($row.length)
                    return false;

                var html_item = $($('#product-related-temp').html());

                html_item.attr('id', 'prod-id-' + item.id);
                html_item.find('.op-name').text(item.label);
                html_item.find('.op-hid').val(item.id);

                $list.append(html_item);

                return false;
            });
    });
})
