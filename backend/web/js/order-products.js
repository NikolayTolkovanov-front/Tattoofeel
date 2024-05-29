$(function(){
    var $list = $('#order-products');

    $.rank = function(v) {
        return String(v).replace(/(\d{1,3})(?=((\d{3})*)$)/g, " $1");
    }
    $.price = function(v) {
        return Math.floor(+v);
    }
    $.cent = function(v) {
        var r = (+v).toFixed(2).toString().split('.');

        if ( r[1] )r = r[1];
        if (r < 10) r = '0' + r;
        if (r == 0) r = '00';

        return r;
    }

    $list.on('keydown', '.op-count', function(e){
        if(e.keyCode == 13) {
            calcTotal();
            e.preventDefault();
            return false;
        }
    });
    $list.on('change', '.op-count', function(e){
        calcTotal();
    });
    $list.on('click', '.op-del', function(e) {
        e.preventDefault();
        $(this).closest('.op-row').remove();
        calcTotal();
    })

    $('#product-search').keydown(function(e){
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

    function calcTotal() {
        var total = 0;
        $list.find('.op-row').each(function(){
            var $inputCount = $(this).find('.op-count');
            var rowTotal = +$inputCount.val()*$inputCount.attr('data-price');
            total += rowTotal;

            $(this).find('.op-total').text( $.rank($.price(rowTotal) ) );
            $(this).find('.op-total-cent').text( $.cent(rowTotal) );
        });

        $('.op-main-total').text($.rank($.price(total)));
        $('.op-main-total-cent').text($.cent(total));
        $('.op-main-total-cur').text($list.find('.op-cur').eq(0).text());
        $('.op-main-total-cur-cent').text($list.find('.op-cur-cent').eq(0).text());

        if (!$list.find('.op-row').length)
            $('.op-main-total-cur-cent').text('');
    }

    $('#product-search').each(function () {
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

                if ($row.length) {
                    var $rowInputCount = $row.find('.op-count');
                    $rowInputCount.val( +$rowInputCount.val() + 1 );
                    $row.find('.op-total').text($.rank( $rowInputCount.val() * item.price.one.value ));
                    calcTotal();

                    return false;
                }

                var html_item = $($('#order-products-row').html());
                var $inputCount = html_item.find('.op-count');
                var total = +$inputCount.val() * item.price.one.value;

                html_item.attr('id', 'prod-id-' + item.id);
                html_item.find('.op-name').text(item.label);
                html_item.find('.op-price').text($.rank($.price(item.price.one.value)));
                html_item.find('.op-price-cent').text($.cent(item.price.one.value));
                html_item.find('.op-total').text($.rank( $.price(total) ));
                html_item.find('.op-total-cent').text($.cent( total ));
                html_item.find('.op-cur').text(item.price.cur);
                html_item.find('.op-cur-cent').text(item.price.cur_cent);
                $inputCount.attr('data-price', item.price.one.value);
                $inputCount.attr('name', 'UserClientOrder[products][' + item.id + ']');

                $list.append(html_item);
                calcTotal();

                return false;
            });
    });

    calcTotal();
})
