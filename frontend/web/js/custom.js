$(document).ready(function () {
    // Прокрутка к началу страницы
    $(window).scroll(function() {
        if ($(this).scrollTop() != 0) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });

    $('#toTop').on('click', function(e) {
        e.preventDefault();
        // e.stopPropagation();
        // e.stopImmediatePropagation();
        console.log('toTop click');
        $('body, html').stop().animate({scrollTop: 0});
    });

    // Закрыть модальное окно
    $(document).on('click', '.modal-close', function(e) {
        $(this).closest('.my-modal').hide();
    });

    // Модалка купить в один клик
    $(document).on('click', '.js-one-click', function(e) {
        e.preventDefault();
        $('#buy-one-click-form').closest('.my-modal').show();
    });

    // Модалка выбрать свой город
    $(document).on('click', '.js-select-your-city', function(e) {
        e.preventDefault();
        $('#select-your-city-form').closest('.my-modal').show();
    });

    // Модалка предложения и отзывы
    $(document).on('click', '.js-show-reviews-form', function(event) {
        event.preventDefault();
        $.ajax({
            url: '/show-reviews-form/',
            type: "get",
            success: function (data) {
                $('#reviews-form').html(data).closest('.my-modal').show();
            }
        });
    });

    $(document).on("submit", '.reviews-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/send-review/",
            type: "POST",
            data: $(this).serialize(),
            success: function(data) {
                if (data === 'success') {
                    $('#reviews-form').closest('.my-modal').hide();
                    $('body').find('.pop-mes').trigger('pop-mes.add', ['Спасибо. Ваше сообщение отправлено']);
                } else if (data === 'error') {
                    $('#reviews-form .help-block-error').html('Ошибка при отправке сообщения');
                } else {
                    $('#reviews-form').html(data);
                }
            }
        });
    });

    // Модалка купить в один клик
    $(document).on('click', '.js-show-buy-one-click-form', function(event) {
        event.preventDefault();
        $.ajax({
            url: '/show-buy-one-click-form/',
            type: "get",
            success: function (data) {
                $('#buy-one-click-form').html(data).closest('.my-modal').show();
            }
        });
    });

    $(document).on("submit", '.buy-one-click-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/send-buy-one-click/",
            type: "POST",
            data: $(this).serialize(),
            success: function(data) {
                if (data == 'success') {
                    $('#buy-one-click-form').closest('.my-modal').hide();
                    $('body').find('.pop-mes').trigger('pop-mes.add', ['Спасибо. Ваше сообщение отправлено']);
                } else if (data == 'error') {
                    $('#buy-one-click-form .help-block-error').html('Ошибка при отправке сообщения');
                } else {
                    $('#buy-one-click-form').html(data);
                }
            }
        });
    });

    // Модалка купить в один клик на странице корзины
    $(document).on('click', '.js-show-buy-one-click-cart-form', function(event) {
        event.preventDefault();
        $.ajax({
            url: '/show-buy-one-click-cart-form/',
            type: "get",
            success: function (data) {
                $('#buy-one-click-cart-form').html(data).closest('.my-modal').show();
            }
        });
    });

    $(document).on("submit", '.buy-one-click-cart-form', function (e) {
        e.preventDefault();
        let submitBtn = $(this).find('.btn');
        submitBtn.attr('disabled', true);

        $.ajax({
            url: "/send-buy-one-click-cart/",
            type: "POST",
            data: $(this).serialize(),
            success: function(data) {
                //console.log(data);
                if (data == 'success') {
                    location.href = '/lk/pay-success/';
                } else if (data == 'error') {
                    $('#buy-one-click-cart-form .help-block-error').html('Ошибка при формировании заказа');
                    submitBtn.attr('disabled', false);
                } else {
                    console.log(data);
                    $('#buy-one-click-cart-form').html(data);
                }
            }
        });
    });

    // $('#header-menu-search').on('submit', function(event) {
    //     //if (!$(this).autocomplete("instance").menu.active) {
    //     if (event.keyCode === 13 && !$(this).autocomplete("instance").menu.active) {
    //         event.preventDefault();
    //         //$(this).submit();
    //         console.log('enter');
    //         return false;
    //     }
    // });

    // Кнопка "Все результаты" в выпадающем списке поиска
    $(document).on('click', '.search-all-result-btn', function (event) {
        event.preventDefault();
        $('#header-menu-search').submit();
    });

    // Кнопка "Не нашли что искали" в выпадающем списке поиска
    // $(document).on('click', '.search-not-found-btn', function (e) {
    //     e.preventDefault();
    //
    // });

    // Модалка не нашли что искали
    $(document).on('click', '.search-not-found-btn', function(event) {
        event.preventDefault();
        $(this).closest('.ui-autocomplete-search-menu').hide();
        $.ajax({
            url: '/show-not-found-search-form/',
            type: "get",
            success: function (data) {
                $('#not-found-search-form').html(data).closest('.my-modal').show();
            }
        });
    });

    $(document).on("submit", '.not-found-search-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/send-not-found-search/",
            type: "POST",
            data: $(this).serialize(),
            success: function(data) {
                if (data === 'success') {
                    $('#not-found-search-form').closest('.my-modal').hide();
                    $('body').find('.pop-mes').trigger('pop-mes.add', ['Спасибо. Ваше сообщение отправлено']);
                } else if (data === 'error') {
                    $('#not-found-search-form .help-block-error').html('Ошибка при отправке сообщения');
                } else {
                    $('#not-found-search-form').html(data);
                }
            }
        });
    });

    // Модалка оплаты заказа на странице с заказами
    $(document).on('click', '.js-show-pay-form', function(event) {
        event.preventDefault();
        $.ajax({
            url: '/show-pay-form/',
            type: "get",
            data: {order_id: $(this).data('order_id')},
            success: function (data) {
                if (data != '') {
                    $('#pay-form').html(data).closest('.my-modal').show();
                } else {
                    $('body').find('.pop-mes').trigger('pop-mes.add', ['Ошибка при обработке заказа']);
                }
            }
        });
    });

    $('.modal-form').on('click', 'input[name="payment_type"]', function (event) {
        let paymentType = parseInt($('input[name="payment_type"]:checked').val());

        $.get({
            url: "/lk/get-commission-info/",
            data: {
                payment_type_id: paymentType,
            },
            success: function (data) {
                //console.log(data);
                $('.modal-form .pay__variant-info-text').html(data.info);

                let product_cost = Number($('#product-sum').attr('data-product-price'));
                let delivery_cost = Number($('#delivery-sum').text().replace(/[^.\d]+/g,"").replace( /^([^\.]*\.)|\./g, '$1' ));
                let commission_cost = Math.ceil((delivery_cost + product_cost) / 100 * data.percent);

                $('#commission-sum').html(commission_cost + ' <span class="rub">i</span>');
                $('#total-sum').html((delivery_cost + product_cost + commission_cost) + ' <span class="rub">i</span>');
            }
        });
    });

    $('.category-footer').on('click', '.btn-more', function(event) {
        event.preventDefault();
        $(this).closest('.category-footer').addClass('more');
    });

    $('.category-footer').on('click', '.btn-less', function(event) {
        event.preventDefault();
        $(this).closest('.category-footer').removeClass('more');
    });

    // brand slider
    var brandSwiper = new Swiper('.swiper-container', {
        initialSlide: 1,
        slidesPerView: 'auto',
        speed: 400,
        spaceBetween: 0,
        loop: true,
        autoplay: {
            delay: 3000,
        },

        // If we need pagination
        // pagination: {
        //     el: '.slick-dots',
        // },
        // Navigation arrows
        // navigation: {
        //     nextEl: '.swiper-button-next',
        //     prevEl: '.swiper-button-prev',
        // },
    });

    // Поля в форме профиля в ЛК
    $(document).on('input', '.ozn-inp', function(){
        if ($(this).val() !== "") {
            $(this).closest('.like-ozn-labl').addClass('has-text');
        } else {
            $(this).closest('.like-ozn-labl').removeClass('has-text');
        }
    });
    $('.ozn-inp').trigger('input');

    setTimeout(function () {
        $('.ozn-inp').focus();
    }, 100);
    setTimeout(function () {
        $('.ozn-inp').blur();
    }, 200);
    setTimeout(function () {
        $('.like-ozn-labl span.label-text').css('opacity', '1');
    }, 500);
    setTimeout(function () {
        $('.like-ozn-labl span.label-text').css('transition', '.5s');
    }, 550);

    $(document).on('click', '.orders-head-number,.orders-head-arrow', function (e) {
        e.preventDefault();
        let tr = $(this).closest('tr');

        if (tr.hasClass('is-collapsed')) {
            tr.removeClass('is-collapsed');
            tr.next('tr').find('.tr-ordrs-content-wrap').slideDown(700);
        } else {
            tr.addClass('is-collapsed');
            tr.next('tr').find('.tr-ordrs-content-wrap').slideUp(700);
        }
    });

    // $('#header-menu-search').on('keydown', function(event) {
    //     //if (event.keyCode !== 13 && $(this).autocomplete("instance").menu.active) {
    //     if (event.keyCode === 13) {
    //         event.preventDefault();
    //         $(this).submit();
    //         //console.log('enter');
    //     }
    // });

    // скроллинг страницы к началу при переходе через pajax
    $('body').bind('pjax:end', function() {
        $(window).scrollTop(0);

        $('.ozn-inp').trigger('input');

        setTimeout(function () {
            $('.ozn-inp').focus();
        }, 100);
        setTimeout(function () {
            $('.ozn-inp').blur();
        }, 200);
        setTimeout(function () {
            $('.like-ozn-labl span.label-text').css('opacity', '1');
        }, 500);

        setTimeout(function () {
            $('.like-ozn-labl span.label-text').css('transition', '.5s');
        }, 550);
    });

    $(document).on('click', '.activate-search-mob', function (e) {
        e.preventDefault();
        $('body').addClass('search-mob-active');
        $('#menu-search').focus();
    });
    $(document).on('blur', '#menu-search', function () {
        $('body').removeClass('search-mob-active');
    });

    // Фильтр каталога через #filter
    // $(document).on('change', '.js-catalog-filter input', function(){
    //     setTimeout(function(){
    //         history.replaceState(undefined, undefined, "#filter="+JSON.stringify(window.currentFilter))
    //     }, 500);
    // });

    // if (location.hash.indexOf("filter=") >= 0) {
    //     let hash = JSON.parse(decodeURIComponent(location.hash.split("filter=")[1]));
    //     let lastEl = null;
    //
    //     for(let key in hash) {
    //         //console.log(key);
    //         //console.log(hash[key]);
    //         if (key === "category") continue;
    //         if (key === "sorted") continue;
    //         if (key === "page") continue;
    //         if (key === "term") continue;
    //         if (key === "filters") {
    //             for (let customFilter in hash[key]) {
    //                 for (let customFilter2 in hash[key][customFilter]) {
    //                     lastEl = $('input[name="filter_id['+customFilter+'][]"][value="'+hash[key][customFilter][customFilter2]+'"]').prop("checked", true);
    //                 }
    //             }
    //         }
    //         if (key === 'price') {
    //             lastEl = $(".slider-price input").jRange("setValue", hash[key]);
    //             continue;
    //         }
    //         if (Array.isArray(hash[key]) && hash[key].length > 0){
    //             for (let val of hash[key]) {
    //                 lastEl = $('input[name="'+key+'"][value="'+val+'"]').prop("checked", true);
    //             }
    //         }
    //     }
    //
    //     if (lastEl) {
    //         lastEl.change();
    //     }
    // }

    $('body').on('click', '.pointer-label', function(){
        let prixCur = $(this).text().split(" i")[0].replace(/ /g, "").trim();
        if ($(this).find('.prix-countr').length === 0) $(this).html('<span class="prix-countr">'+prixCur+'</span><span class="currency"> <span class="rub">i</span></span>');
        $(this).find('.prix-countr').prop("contenteditable", true); $(this).find('.prix-countr').focus()});
    $('body').on("blur", '.pointer-label .prix-countr', function(){
        let min = $(this).closest('.slider-price').find('.pointer-label.low').text().split(" i")[0].replace(/ /g, "");
        let max = $(this).closest('.slider-price').find('.pointer-label.high').text().split(" i")[0].replace(/ /g, "");
        $(this).closest(".slider-price").find("input").jRange("setValue", min+","+max);});
    $('body').on("keyup", 'input[name="SignupForm[phone]"]', function(e) {
        $(this).val($(this).val().replace(/[^0-9+() -]/g,''));
    });

    //Применение купона
    $(document).on('click', '#lk-cart-coupon-btn', function () {
        $.ajax({
            url: "/lk/coupon-apply/",
            type: 'GET',
            async: false,
            data: {
                code: $('#lk-cart-coupon-input').val(),
            },
            success: function (res) {
                //console.log(res);

                if (res['status'] == true) {
                    $('.lk-cart-coupon-msg').removeClass('error').html(res['msg']);
                    $('#lk-cart-sum-without-discount').html(res['sumWithoutDiscountFormat']);
                } else {
                    $('.lk-cart-coupon-msg').addClass('error').html(res['msg']);
                }

                $.get('/lk/get-cart-list/')
                    .done(function (r) {
                        $('#lk-cart-list').html(r)
                    })
            }
        });
    });

    $(document).on('click', '.lk-cart-del-coupon', function(){
        $('#lk-cart-coupon-input').val("");
        $('#lk-cart-coupon-btn').click();
        $('#lk-cart-sum-without-discount').html($('#lk-cart-sum').html());
    });

    $(document).on("click", ".review__rating-link", function (e) {
        e.preventDefault();

        let tab_id = '#id__'+$(this).attr('data-product_id')+'__reviews';
        $('a[href="'+tab_id+'"').click();
        $('html, body').animate({
            scrollTop: $(tab_id).offset().top
        }, 1000);
    });

    /* для вывода звёзд рейтинга */
    $(".countStarsBlock .iconStar")
        .mouseenter(function() {
            let numberStars = parseInt($(this).attr("data-check-val"));

            for (let i = 1; i <= 5; i++) {
                if (i <= numberStars) {
                    $(".iconStar_"+i).find(".emptyStars").css("display", "none");
                    $(".iconStar_"+i).find(".fullStars").css("display", "block");
                }
                else {
                    $(".iconStar_"+i).find(".fullStars").css("display", "none");
                    $(".iconStar_"+i).find(".emptyStars").css("display", "block");
                }
            }

        })
        .mouseleave(function(){
            let numberStars = $(".countStarsBlock input[type='checkbox']").val();

            for (let i = 1; i <= 5; i++) {
                if (i <= numberStars) {
                    $(".iconStar_"+i).find(".emptyStars").css("display", "none");
                    $(".iconStar_"+i).find(".fullStars").css("display", "block");
                }
                else {
                    $(".iconStar_"+i).find(".fullStars").css("display", "none");
                    $(".iconStar_"+i).find(".emptyStars").css("display", "block");
                }
            }
        });

    /* при нажатии на звёздочку - сохраняем значение */
    $(".countStarsBlock .iconStar").on("click", function() {
        let valCheck = $(this).attr("data-check-val")
        $(".countStarsBlock input[type='checkbox']").val(valCheck);
    });

    let regText = 'Отзывы могут писать только зарегистрированные пользователи. Пожалуйста, пройдите <a href="/lk/login/">регистрацию</a>';
    $(document).on('focus', '.review__textarea.js-show-popup', function (e) {
        e.preventDefault();
        $('.pop-mes').trigger('pop-mes.add', [regText]);
    });

    $(document).on('click', '.btn.js-show-popup', function (e) {
        e.preventDefault();
        $('.pop-mes').trigger('pop-mes.add', [regText]);
    });

    $('.review__form').on('beforeSubmit', 'form', function(e){
        let data = $(this).serialize();
        $.ajax({
            url: '/catalog/add-review/',
            type: 'POST',
            data: data,
            success: function(res){
                if (typeof res.success !== 'undefined' && res.success === true) {
                    for (let i = 1; i <= 5; i++) {
                        $(".iconStar_"+i).find(".fullStars").css("display", "none");
                        $(".iconStar_"+i).find(".emptyStars").css("display", "block");
                    }

                    $(".countStarsBlock input[type='checkbox']").val(0);
                    $('.review__textarea textarea').val('');
                }

                if (typeof res.msg !== 'undefined') {
                    $('.pop-mes').trigger('pop-mes.add', [res.msg]);
                }
                //console.log(res);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("Status: " + textStatus);
            }
        });

        return false;
    });
});

/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};
