$.init = function () {

    //map
    $('#map').each(function () {
        return;
        $.get('/lk/cdek-pvz/', function (response) {

            // Функция ymaps.ready() будет вызвана, когда
            // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
            ymaps.ready(init);

            function init() {
                // Создание карты.
                var myMap = new ymaps.Map("map", {
                        // Координаты центра карты.
                        // Порядок по умолчанию: «широта, долгота».
                        // Чтобы не определять координаты центра карты вручную,
                        // воспользуйтесь инструментом Определение координат.
                        center: [55.76, 37.64],
                        // Уровень масштабирования. Допустимые значения:
                        // от 0 (весь мир) до 19.
                        zoom: 9,
                        controls: ['zoomControl', 'searchControl', 'fullscreenControl']
                    }),
                    myPlacemarks = [];
                if (response.Pvz) {
                    for (let i = 0; i < response.Pvz.length; i++) {
                        let p = response.Pvz[i];

                        myPlacemarks[i] = new ymaps.Placemark([p['@attributes']['coordY'], p['@attributes']['coordX']], {
                            // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                            balloonContentHeader: p['@attributes']['Name'],
                            balloonContentBody: p['@attributes']['Address'],
                            //balloonContentFooter: "Подвал",
                            //hintContent: "Хинт метки"
                        })
                        //myMap.geoObjects.add(myPlacemarks[i]);
                    }
                }

                let clusterer = new ymaps.Clusterer({
                    /**
                     * Через кластеризатор можно указать только стили кластеров,
                     * стили для меток нужно назначать каждой метке отдельно.
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage.xml
                     */
                    preset: 'islands#invertedVioletClusterIcons',
                    /**
                     * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
                     */
                    groupByCoordinates: false,
                    /**
                     * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ClusterPlacemark.xml
                     */
                    clusterDisableClickZoom: true,
                    clusterHideIconOnBalloonOpen: false,
                    geoObjectHideIconOnBalloonOpen: false
                });

                /**
                 * Можно менять опции кластеризатора после создания.
                 */
                clusterer.options.set({
                    gridSize: 80,
                    clusterDisableClickZoom: true
                });

                /**
                 * В кластеризатор можно добавить javascript-массив меток (не геоколлекцию) или одну метку.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Clusterer.xml#add
                 */
                clusterer.add(myPlacemarks);
                myMap.geoObjects.add(clusterer);

            }

        })
    })


    //search
    $('#menu-search').each(function () {
        var t = $(this);

        if (t.hasClass('-init')) return;
        t.addClass('-init');

        t.on('autocompletefocus',
            function () {
                return false;
            });
        t.on('autocompleteselect',
            function (e, ui) {
                document.location.href = ui.item.url;
                return false;
            });
        t.on('autocompletecreate',
            function () {
                $(this).data('ui-autocomplete')._renderItem =
                    function (ul, item) {

                        var label = item.label;
                        return $('<li></li>').data('item.autocomplete', item)
                            .append(
                                '<a href="' + item.url + '">' +
                                '<span class="ui-sm-it__pict" style="background-image:url(' +
                                item.imgUrl + ')"></span>' +
                                '<span class="ui-sm-it__label">' + label + '</span>' +
                                '<span class="ui-sm-it__price">' + item.price + '</span>' +
                                '</a>'
                            ).appendTo(ul);
                    };
                //console.log($(this).data('ui-autocomplete')._renderItem);

                $(this).data('ui-autocomplete')._renderMenu =
                    function (ul, items) {
                        var that = this;
                        $.each(items, function (index, item) {
                            that._renderItemData(ul, item);
                        });
                        $(ul).append(
                            '<div class="ui-autocomplete-search-menu-footer"><a href="#" class="btn search-all-result-btn">Все результаты</a>' + '<a href="#" class="btn search-not-found-btn">Не нашли, что искали?</a></div>'
                        );
                    }
            });
    });

    //home slider
    $('.home-slider__list:not(.-init)').each(function () {
        if (!$(this).hasClass('-init')) {
            $(this).addClass('-init');
            $(this).slick({
                dots: true,
                arrows: false,
                speed: 500,
                fade: true,
                autoplay: true,
                cssEase: 'ease-in'
            });
        }
    });


    //tile slider
    $('.category-tile._slider:not(.-init), .category-tile-title-outer._slider:not(.-init)').each(function (t_i) {
        if (!$(this).hasClass('-init')) {
            $(this).addClass('-init');

            var NTH_OFFSET_BY2 = 0.08;
            var NTH_OFFSET_DEF = 0.08;

            var t = $(this);

            var $dots = t.find('.category-tile__dots');
            var $container = t.find('.category-tile__list');

            var count = 0;
            var by_item = 2;//4,3,2,1
            var nth_offset = NTH_OFFSET_DEF;
            var last_device = null;

            function init_dots() {
                $dots.empty();
                for (var i = 0; i < Math.ceil($container.find('>*:not(._empty):not(.last-page)').length / by_item); i++)
                    $dots.append('<li />');
                $dots.find('li').eq(0).addClass('-act');
            }

            function move() {
                $container.css('transform', 'translate(-' +
                    count * t.width() * (1 + nth_offset)
                    + 'px)');
                dotsAct();
            }

            function prev() {
                if (count > 0) {
                    count--;
                    move();
                }
            }

            function next() {
                if (Math.ceil($container.find('>*:not(._empty)').length / by_item) > count + 1) {
                    count++;
                    move();
                }
            }

            function getDevice() {
                var ww = $(window).width();

                if (ww < 576) return 'mob';
                if (ww < 768) return 'mob-lg';
                if (ww < 992) return 'tab';
                if (ww < 1200) return 'laptop';

                return 'laptop-lg';
            }

            function dotsAct() {
                $dots.find('li').removeClass('-act');
                $dots.find('li').eq(count).addClass('-act');
            }

            function init_event() {
                $dots.on('click', 'li', function () {
                    count = $dots.find('li').index($(this));
                    move();
                })

                $(window)
                    .off('resize.category-tile-slider-' + t_i)
                    .on('resize.category-tile-slider-' + t_i, function () {

                        var device = getDevice();

                        if (device !== last_device) {

                            if (device === 'mob-lg') {
                                by_item = 2;
                            } else if (device.match('mob')) {
                                by_item = 2;
                            } else if (device === 'tab')
                                by_item = 3;
                            else if (device.match('laptop'))
                                by_item = 4;

                            if (device === 'mob-lg')
                                nth_offset = NTH_OFFSET_BY2;
                            else
                                nth_offset = NTH_OFFSET_DEF;

                            count = 0;
                            last_device = device;
                            init_dots();
                        }

                        move();
                    })

                $(window).triggerHandler('resize.category-tile-slider-' + t_i);
            }

            function init_swipe() {
                var initialPoint;
                var finalPoint;

                t.on('touchstart', function (e) {
                    initialPoint = event.changedTouches[0];
                });

                t.on('touchend', function (e) {
                    finalPoint = event.changedTouches[0];
                    var xAbs = Math.abs(initialPoint.pageX - finalPoint.pageX);
                    var yAbs = Math.abs(initialPoint.pageY - finalPoint.pageY);

                    if (xAbs > 20 || yAbs > 20) {
                        if (xAbs > yAbs) {
                            if (finalPoint.pageX < initialPoint.pageX) {
                                next();
                            } else {
                                prev();
                            }
                        } else {
                            if (finalPoint.pageY < initialPoint.pageY) {
                                /*СВАЙП ВВЕРХ*/
                            } else {
                                /*СВАЙП ВНИЗ*/
                            }
                        }
                    }
                });
            }

            init_event();
            init_swipe();
        }
    });

    //slider price
    $('.slider-price:not(.-init) input').each(function () {
        if (!$(this).hasClass('-init')) {
            $(this).addClass('-init');

            var t = $(this);
            var minMaxPrices = $(this).attr('default-value').split(',');
            var int = null;
            t.jRange({
                from: minMaxPrices instanceof Array ? minMaxPrices[0] : 0,
                to: minMaxPrices instanceof Array ? minMaxPrices[1] : 500000,
                step: 1,
                scale: false,
                width: 203,
                format: function (value, pointer) {
                    return String(value).replace(/(\d{1,3})(?=((\d{3})*)$)/g, " $1") + ' <span class="rub">i</span>'
                },
                showLabels: true,
                isRange: true,
                ondragend: function () {
                    if (!int) {
                        int = setTimeout(function () {
                            t.closest('.js-catalog-filter').trigger('change-filter');
                            int = null;
                        }, 300)
                    }
                }
            });
        }
    });

    //js-catalog-filter
    $('.js-catalog-filter:not(.-init)').each(function () {
        $(this).addClass('-init');
        var t = $(this);

        //js-stock-product-list
        $('.js-stock-product-list:not(.-init)').each(function () {
            $(this).addClass('-init');
            var tt = $(this);

            tt.find('ul > li > a').on('click', function (e) {
                e.preventDefault();

                $('.js-catalog-load-more').attr('data-page', 0);

                tt.find('>span').text($(this).text())

                tt.find('.-act').removeClass('-act');
                $(this).parent().addClass('-act');
                $('#js-stock-product-list__hidden').val($(this).attr('data-prop'));

                send($(this).attr('data-load-more-url-new'));

                return true;
            })
        });

        //js-sorted-product-list
        $('.js-sorted-product-list:not(.-init)').each(function () {
            $(this).addClass('-init');
            var tt = $(this);

            tt.find('ul > li > a').on('click', function (e) {
                e.preventDefault();
                $('.js-catalog-load-more').attr('data-page', 0);

                tt.find('>span').text($(this).text())

                tt.find('.-act').removeClass('-act');
                $(this).parent().addClass('-act');
                $('#js-sorted-product-list__hidden').val($(this).attr('data-prop'));

                send($(this).attr('data-load-more-url-new'));

                return true;
            })
        });

        //js-catalog-load-more
        $('.js-catalog-load-more:not(.-init)').each(function () {
            $(this).addClass('-init');
            var tt = $(this);

            tt.bind('click', function (e) {
                e.preventDefault();

                var page = +tt.attr('data-page') + 1;

                tt.attr('data-page', page);

                if (tt.hasClass('-loading'))
                    return false;

                tt.addClass('-loading');

                send($(this).attr('data-load-more-url-new'));

                return true;
            });
        });

        t.on('change', 'input:not([name="price"])', function () {
            $('.js-catalog-load-more').attr('data-page', 0);
            send();

            return true;
        });

        t.on('change-filter', function () {
            $('.js-catalog-load-more').attr('data-page', 0);
            send();

            return true;
        });

        //init
        t.trigger('change.cf');

        function getParams() {
            t.trigger('change.cf');

            let res = {filters: {
                    price: {},
                    brand: [],
                    manufacturer: {},
                    category: [],
                    is_discount: {},
                    sort: {},
                    in_stock: {},
                    pagination: {},
                    //page: 0,
                    search: '',
                }
            };

            // $('.js-catalog-load-more').each(function () {
            //     res['filters']['pagination']['PAGE'] = $(this).attr('data-page');
            // });

            $('#menu-search').each(function () {
                res['filters']['search'] = $(this).val();
            });

            $('#js-stock-product-list__hidden').each(function () {
                res['filters']['in_stock']['IN_STOCK'] = ($(this).val() === 'in_stock') ? 'Y' : 'N';
            });

            $('input[name="is_discount"]').each(function () {
                res['filters']['is_discount']['IS_DISCOUNT'] = ($(this).val() === '1') ? 'Y' : 'N';
            });

            $('#js-sorted-product-list__hidden').each(function () {
                let cur_sort = $(this).val().split('-');

                if (cur_sort.length === 2) {
                    res['filters']['sort']['TYPE'] = cur_sort[0];
                    res['filters']['sort']['ORDER'] = cur_sort[1];
                }
            });

            t.find('input[name="price"]').each(function () {
                if ($(this).val() !== $(this).attr('default-value')) {
                    let cur_price = $(this).val().split(',');

                    if (cur_price.length === 2) {
                        res['filters']['price']['FROM'] = cur_price[0];
                        res['filters']['price']['TO'] = cur_price[1];
                    }
                }
            });

            t.find('input[name="brand"]').each(function () {
                if ($(this)[0].checked) {
                    res['filters']['brand'].push($(this).val());
                }
            });

            t.find('input[name="manufacturer"]').each(function () {
                if ($(this)[0].checked) {
                    res['filters']['manufacturer'][$(this).attr('data-value')] = $(this).val();
                }
            });
            t.find('input[name="category"]').each(function () {
                if ($(this)[0].checked) {
                    res['filters']['category'].push($(this).val());
                }
            });
            t.find('.category-filter__block__form').each(function (index) {
                $(this).find('input[name *= "filter_id"]').each(function (key) {
                    var zis = $(this)[0];
                    var zisKey = zis.getAttribute("name")
                        .replace("filter_id", "")
                        .replace("[]", "")
                        .replace("[", "")
                        .replace("]", "");
                    if ($(this)[0].checked && $(this).closest('.none').length === 0) {
                        res['filters']['filter_' + zisKey] = res['filters']['filter_' + zisKey] || {};
                        res['filters']['filter_' + zisKey][key] = zis.value;
                    }
                });
            });

            window.currentFilter = res;
            //console.log(res);

            return res;
        }

        function send(uriNew = '') {
            //console.trace();

            if (uriNew === '') {
                let params = getParams();

                window.currentUrlXhr && window.currentUrlXhr.abort();
                window.currentUrlXhr = $.post('/catalog/generate-url/', params).done(function (response) {
                    if (typeof response.uri !== 'undefined' && response.uri !== '') {
                        if (history.pushState) {
                            history.replaceState(null, null, window.location.protocol + "//" + window.location.host + response.uri);
                        } else {
                            console.warn('History API не поддерживает ваш браузер');
                        }

                        renderCatalogPage(response.uri);
                    }

                    if (typeof response.metaTags !== 'undefined') {
                        //console.log(response.metaTags);
                        if (typeof response.metaTags.h1 !== 'undefined' && response.metaTags.h1 !== '') {
                            $('h1.visually-hidden').html(response.metaTags.h1);
                            $('.category-footer-title').html(response.metaTags.h1);
                        }

                        if (typeof response.metaTags.title !== 'undefined' && response.metaTags.title !== '') {
                            $(document).attr("title", response.metaTags.title);
                        }

                        if (typeof response.metaTags.description !== 'undefined' && response.metaTags.description !== '') {
                            $('meta[name=description]').attr("content", response.metaTags.description);
                        }

                        if (typeof response.metaTags.keywords !== 'undefined' && response.metaTags.keywords !== '') {
                            $('meta[name=keywords]').attr("content", response.metaTags.keywords);
                        }

                        if (typeof response.metaTags.seo_text !== 'undefined' && response.metaTags.seo_text !== '') {
                            $('.category-footer-desc-text').html(response.metaTags.seo_text);
                        }
                    }
                });
            } else if (uriNew.indexOf('/catalog/') !== -1) { // load more
                let params = getParams();
                params['filters']['pagination']['PAGE'] = +$('.js-catalog-load-more').attr('data-page') + 1;

                window.currentUrlXhr && window.currentUrlXhr.abort();
                window.currentUrlXhr = $.post('/catalog/generate-url/', params).done(function (response) {
                    if (typeof response.uri !== 'undefined' && response.uri !== '') {
                        if (history.pushState) {
                            history.replaceState(null, null, window.location.protocol + "//" + window.location.host + response.uri);
                        } else {
                            console.warn('History API не поддерживает ваш браузер');
                        }

                        //$('.js-catalog-load-more').attr('data-load-more-url-new', response.uri);

                        renderCatalogPage(response.uri);
                    }
                });
            } else if (uriNew.indexOf('/brands/') !== -1) {
                let res = {
                    page: 0
                };

                $('.js-catalog-load-more').each(function () {
                    res['page'] = $(this).attr('data-page');
                });

                renderCatalogPage(uriNew, res);
            }
        }

        function renderCatalogPage(uri, params = []) {
            window.currentFilterXhr && window.currentFilterXhr.abort();
            window.currentFilterXhr = $.post(uri, params)
                .done(function (response) {
                    var $list;
                    if ($('#product-list-container .product-list').length > 0) {
                        $list = $('#product-list-container .product-list');
                    } else {
                        $list = $('.product-list');
                    }

                    $('.js-link-pager-pagination').remove();

                    if (+$('.js-catalog-load-more').attr('data-page') > 0)
                        $list.append(response);
                    else
                        $list.html(response);

                    $('.js-catalog-load-more').removeClass('-loading');

                    if ($list.find('.empty').length || $list.find('.last-page').length) {
                        $list.next('.btn-box').hide();
                    } else {
                        $list.next('.btn-box').show();
                    }
                });
        }

        // список сортировки на странице категории каталога
        $(document).on('click', '.sorted ul li a', function () {
            if ($(this).closest('.product-list__sorted__inner').length > 0) {
                let htm = $(this).closest('.product-list__sorted__inner').html();
                $(this).closest('.product-list__sorted__inner').html(htm.replace(/( -init)/g, ""));

                $('.js-stock-product-list:not(.-init)').each(function () {
                    $(this).addClass('-init');
                    var tt = $(this);

                    tt.find('ul > li > a').on('click', function (e) {
                        e.preventDefault();

                        $('.js-catalog-load-more').attr('data-page', 0);

                        tt.find('>span').text($(this).text());

                        tt.find('.-act').removeClass('-act');
                        $(this).parent().addClass('-act');
                        $('#js-stock-product-list__hidden').val($(this).attr('data-prop'));

                        send($(this).attr('data-load-more-url-new'));

                        return true;
                    })
                });


                $('.js-sorted-product-list:not(.-init)').each(function () {
                    $(this).addClass('-init');
                    var tt = $(this);

                    tt.find('ul > li > a').on('click', function (e) {
                        e.preventDefault();
                        $('.js-catalog-load-more').attr('data-page', 0);

                        tt.find('>span').text($(this).text());

                        tt.find('.-act').removeClass('-act');
                        $(this).parent().addClass('-act');
                        $('#js-sorted-product-list__hidden').val($(this).attr('data-prop'));

                        send($(this).attr('data-load-more-url-new'));

                        return true;
                    })
                });
            }
        });
    });

    //product card sliders
    $('.product-card-pict:not(.-init)').each(function () {
        $(this).trigger('slick');
    });

    //data-hide-timeout
    $('[data-hide-timeout]').each(function () {
        var t = $(this);
        setTimeout(function () {
            t.trigger('click');
        }, 8000)
    })

};

//binding
$(function () {
    var $b = $('body');

    //putlink
    $b.on('click', '.js-lk-cart-deferred', function (e) {
        e.preventDefault();
        var t = $(this);
        t.toggleClass('-act');
        var id = t.attr('data-product-id');
        id && $.get('/catalog/deferred/?type=' + (+t.hasClass('-act')) + '&id=' + id);
    });
    $b.on('click', '.put-link-el', function (e) {
        e.preventDefault();
        var t = $(this);
        t.toggleClass('-act');
        var id = t.attr('data-product-id');
        id && $.get('/catalog/deferred/?type=' + (+t.hasClass('-act')) + '&id=' + id);
        if (window.products_configs && window.products_configs[id])
            window.products_configs[id]['isDeferred'] = +t.hasClass('-act');
    });
    $b.on('click', '.js-lk-del-deferred', function (e) {
        e.preventDefault();
        var t = $(this);
        var id = t.attr('data-product-id');
        id && $.get('/catalog/deferred/?type=0&id=' + id);
        t.closest('.lk-table__row').remove();
    });

    //pop-mes
    $b.on('click', '.pop-mes__item', function () {
        var t = $(this);
        $(this).fadeOut(600, function () {
            t.remove();
        });
    });

    $b.on('pop-mes.add', '.pop-mes', function (e, msg) {
        var $msg = $('<sapn class="pop-mes__item pop-mes__def" />');
        $msg.html(msg);

        $(this).append($msg);

        setTimeout(function () {
            $msg.trigger('click');
        }, 3500);
    });

    //cart
    $b.on('change', '.product-card-data__count .number', function () {
        $('#add-cart-product').attr('data-count', $(this).find('[type="hidden"]').val())
    });
    $b.on('change', '.product-list__config__list .number', function () {
        $(this).closest('.plt-config').attr('data-count', $(this).find('[type="hidden"]').val())
    });

    function isInView(elem) {
        return $(elem).offset().top - $(window).scrollTop() < $(elem).height();
    }

    $b.on('click', '.js-add-cart', function (e) {
        //console.log(e);

        e.preventDefault();

        var t = $(this);
        var configs = {};
        var id = t.attr('data-product-id');
        var count = t.attr('data-count') || 1;

        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        // var elem = $('.product-card-props');
        // console.log(elem);
        // var elemTop = $(elem).offset().top;
        // var elemBottom = elemTop + $(elem).height();

        //console.log(docViewBottom >= elemTop && docViewTop <= elemBottom);
        // if (0 < $(elem).length && 1 < $('.pcp-item').length && !(docViewBottom >= elemTop && docViewTop <= elemBottom)) {
        //     $('body, html').animate({scrollTop: $('.product-card-data__props').offset().top}, 800);
        //     $b.find('.pop-mes').trigger('pop-mes.add', ['Пожалуйста, выберите модификацию']);
        // } else {
        {
            if (+t.attr('data-amount') < count) {
                $b.find('.pop-mes').trigger('pop-mes.add', ['Товара нет в наличии']);
            } else {
                if (id) {
                    $.get('/catalog/add-cart/?count=' + count + '&id=' + id)
                        .done(function (r) {
                            if (r === false) {
                                $b.find('.pop-mes').trigger('pop-mes.add', ['Товара нет в наличии']);
                            } else {
                                $b.find('.pop-mes').trigger('pop-mes.add', ['Товар добавлен <a href="/lk/cart/">в корзину</a>']);
                                $b.find('.number[data-product-id="' + id + '"]').trigger('reset');
                                $b.find('.menu-cart__count').html(r.count);
                                //$('#cart-count').html(r.count);
                                $b.find('#cart-sum').removeClass('none').html(r.sumFormat);
                                $b.find('#lk-cart-sum').html(r.sumFormat);

                                // e-commerce
                                configs[id] = {
                                    'id': id,
                                    'count': count
                                };
                                addProductsEcommerce(configs);
                            }
                        })
                        .fail(function (r) {
                            $b.find('.pop-mes').trigger('pop-mes.add', ['Упс! Что то пошло не так']);
                        });
                }
            }
        }
    });

    $b.on('click', '.js-add-cart-configs', function (e) {
        //console.log(e);

        e.preventDefault();
        var t = $(this);
        var configs = {};
        var initially = $('.menu-cart .menu-cart__count').html();
        var isAdded = false;

        t.closest('.product-list__config').find('.plt-config').each(function () {
            if (+$(this).attr('data-count')) {
                configs[$(this).attr('data-product-id')] = {
                    'id': $(this).attr('data-product-id'),
                    'count': $(this).attr('data-count')
                };
            }

            if (+$(this).attr('data-count') > 0) {
                isAdded = true;
            }
        });

        if (!isAdded) {
            $b.find('.pop-mes').trigger('pop-mes.add', ['Не указано количество товара']);
        } else {
            $.post('/catalog/add-cart-configs/', {configs: configs})
                .done(function (r) {
                    if (+r.count <= +initially) {
                        $b.find('.pop-mes').trigger('pop-mes.add', ['Товара нет в наличии']);
                    } else {
                        $b.find('.pop-mes').trigger('pop-mes.add', ['Товары добавлены <a href="/lk/cart/">в корзину</a>']);
                        t.closest('.product-list__config').find('.plt-config').find('.number').trigger('reset');
                        t.closest('.product-list__config').find('[data-count]').attr('data-count', 0);
                        $b.find('.menu-cart__count').html(r.count);
                        $b.find('#cart-sum').removeClass('none').html(r.sumFormat);

                        // e-commerce
                        addProductsEcommerce(configs);
                    }
                })
                .fail(function (r) {
                    $b.find('.pop-mes').trigger('pop-mes.add', ['Упс! Что то пошло не так']);
                });
        }
    });

    //lk del cart item
    $b.on('click', '.js-lk-cart-del', function (e) {
        e.preventDefault();
        let t = $(this);
        let count = $(this).closest('tr').find('.number__value').val();
        let id = t.attr('data-product-id');
        let code = $('#lk-cart-coupon-input').val();

        $.get('/catalog/remove-cart/?id=' + id + '&coupon_code=' + code)
            .done(function (r) {
                //console.log(r);
                $b.find('.pop-mes').trigger('pop-mes.add', ['Товар удален из корзины']);
                $b.find('.menu-cart__count').html(r.count);

                if(r.count<=0){
                    //TODO костыль, стоит переделать по нормальному
                    $b.find(".lk-table__head").html("<td className='lk-table-fd-name _empty'>Корзина пуста</td>");

                    $b.find(".lk-profile-btn").html('');
                    $b.find(".lk-cart-coupon").html('');
                    $b.find(".lk-cart-total").html('');

                    return;
                }

                if (r.sumFormat) {
                    $b.find('#cart-sum').removeClass('none').html(r.sumFormat);
                } else {
                    $b.find('#cart-sum').addClass('none');
                }

                $b.find('#lk-cart-sum').html(r.sumFormat);

                if (!!r.sum_discount) {
                    $b.find('#lk-cart-coupon-input').val(r.cart.couponCode);
                    $b.find('.lk-cart-coupon-msg').removeClass('error').html(Number(r.sumDiscountFormat) ? ('Скидка составит ' + r.sumDiscountFormat) : 'Промокод применен');
                } else {
                    if (code !== '') {
                        $('#lk-cart-coupon-btn').click()
                    } else {
                        $b.find('.lk-cart-coupon-msg').removeClass('error').html('');
                    }
                }
                $b.find('#lk-cart-sum-without-discount').html(r.sumWithoutDiscountFormat);

                // e-commerce
                removeProductsEcommerce(count, id);
            })
            .fail(function (r) {
                $b.find('.pop-mes').trigger('pop-mes.add', ['Упс! Что то пошло не так']);
            });

        t.closest('.lk-table__row').next('.lk-table-additional-row').remove();
        t.closest('.lk-table__row').remove();
    });


    window.changingCartAjx = {};
    //lk change cart
    $b.on('change', '.js-lk-change-cart', function () {
        let t = $(this);
        let id = t.attr('data-product-id');
        let real_count = typeof t.attr('data-real-count') !== 'undefined' ? parseInt(t.attr('data-real-count')) : -1;
        let count = t.find('[type="hidden"]').val();
        let code = $('#lk-cart-coupon-input').val();

        if (real_count < parseInt(t.find('[type="hidden"]').val())) {
            t.closest('.lk-table__row').next('.lk-table-additional-row').removeClass('hidden-row');
        } else {
            t.closest('.lk-table__row').next('.lk-table-additional-row').addClass('hidden-row');
        }

        // e-commerce
        changeProductsEcommerce(count, id);

        if (typeof window.changingCartAjx[id] !== "undefined") {
            window.changingCartAjx[id].abort();
        }
        window.changingCartAjx[id] = $.ajax({
            url: '/catalog/change-cart/?count=' + count + '&id=' + id + '&coupon_code=' + code,
            success: function (r) {
                //console.log(r);
                if (r === false) {
                    $b.find('.pop-mes').trigger('pop-mes.add', ['Товара нет в наличии']);
                } else {
                    $b.find('.menu-cart__count').html(r.cart.count);
                    $b.find('#cart-sum').removeClass('none').html(r.cart.sumFormat);

                    if (!!r.cart.sum_discount) {
                        $b.find('#lk-cart-coupon-input').val(r.cart.couponCode);
                        $b.find('.lk-cart-coupon-msg').removeClass('error').html(Number(r.cart.sumDiscountFormat) ? ('Скидка составит ' + r.cart.sumDiscountFormat) : 'Промокод применен');
                    } else {
                        if (code !== '') {
                            //$b.find('#lk-cart-coupon-input').val(r.cart.couponCode);
                            $b.find('.lk-cart-coupon-msg').addClass('error').html('Промокод не соответствует условиям его применения');
                        } else {
                            $b.find('.lk-cart-coupon-msg').removeClass('error').html('');
                        }
                    }

                    $b.find('#lk-cart-sum').html(r.cart.sumFormat);
                    $b.find('#lk-cart-sum-without-discount').html(r.cart.sumWithoutDiscountFormat);
                    t.closest('.lk-table__row').find('.lk-table-fd-sum').html(r.price);
                }
            },
            fail: function (r) {
                $b.find('.pop-mes').trigger('pop-mes.add', ['Упс! Что то пошло не так']);
            }
        });
    });

    function addProductsEcommerce(configs) {
        $.post('/catalog/add-products-ecommerce/', {configs: configs})
            .done(function (r) {
                if (r !== false) {
                    window.dataLayer.push(r);

                    // VK Retargeting
                    if (typeof r.ecommerce.add.products[0].price !== 'undefined' && typeof r.ecommerce.add.products[0].quantity !== 'undefined') {
                        //console.log(r.ecommerce.add.products[0].price * r.ecommerce.add.products[0].quantity);
                        VK.Goal('add_to_cart', {value: r.ecommerce.add.products[0].price * r.ecommerce.add.products[0].quantity})
                    }
                }
            })
    }

    function removeProductsEcommerce(count, id) {
        $.get('/catalog/remove-products-ecommerce/?count=' + count + '&id=' + id)
            .done(function (r) {
                if (r !== false) {
                    //console.log(r);
                    window.dataLayer.push(r);
                }
            });
    }

    function changeProductsEcommerce(count, id) {
        $.get('/catalog/change-products-ecommerce/?count=' + count + '&id=' + id)
            .done(function (r) {
                if (r !== false) {
                    //console.log(r);
                    window.dataLayer.push(r);
                }
            });
    }

    //lk recently add cart
    $b.on('click', '#lk-recently-row .js-add-cart', function (e) {
        e.preventDefault();
        $.get('/lk/get-cart-list/')
            .done(function (r) {
                $b.find('#lk-cart-list').html(r)
            })
    });

    $b.on('change', '.js-lk-change-order', function () {
        var count = $(this).find('[type="hidden"]').val();
        var price = $(this).closest('.lk-table__row').find('.lk-table-fd-sum').attr('data-price');

        $(this).closest('.lk-table__row').find('.lk-table-fd-sum').html((price * count).format(0, 3, ' ') + ' <span class="rub">i</span>')
    });

    //lk del order item
    $b.on('click', '.js-lk-order-del', function (e) {
        e.preventDefault();
        var tr = $(this).closest('tr');

        $(this).closest('.lk-table__row').remove();
        if (tr.find('.lk-table__row').length == 0) {
            tr.find('.lk-table-fd-save').hide();
        }
    });

    //lk save order
    $b.on('click', '.js-lk-order-save', function (e) {
        e.preventDefault();
        var t = $(this);
        var rows = t.closest('tr').find('.lk-table__row');
        var products = [];

        rows.each(function (index, value) {
            products.push(
                {
                    'id': +$(this).find('.number').attr('data-product-id'),
                    'quantity': +$(this).find('[type="hidden"]').val(),
                }
            );
        });

        $.post('/lk/change-order-products/', {
            'orderId': $(this).attr('data-order-id'),
            'products': products,
        })
            .done(function (data) {
                console.log(data);
                if (data.status === 'error') {
                    $b.find('.pop-mes').trigger('pop-mes.add', [data.error]);
                } else if (data.status === 'ok') {
                    t.closest('tr').prev('tr').find('.order__sum').html(data.orderSum);
                    $b.find('.pop-mes').trigger('pop-mes.add', ['Заказ был изменён']);
                }
            })
            .fail(function (data) {
                $b.find('.pop-mes').trigger('pop-mes.add', ['Упс! Что то пошло не так']);
            });
    });

    //placeholder
    $b.on('focus', '.placeholder', function () {
        var t = $(this);
        t.attr('data-placeholder', t.attr('placeholder'));
        t.attr('placeholder', '');
    });
    $b.on('blur', '.placeholder', function () {
        var t = $(this);
        t.attr('placeholder', t.attr('data-placeholder'));
    });

    //product slick slider
    $b.on('unslick', '.product-card-pict', function () {
        $(this).removeClass('-init');
        var t = $(this);

        t.find('.product-card-pict__list-img.slick-slider')
            .after('<div class="product-card-pict__list-img" id="pr-pict" />');
        t.find('.product-card-pict__list-img.slick-slider')
            .remove();

        t.find('.product-card-pict__list-main-img.slick-slider')
            .after('<div class="product-card-pict__list-main-img" id="pr-pict-main" />');
        t.find('.product-card-pict__list-main-img.slick-slider')
            .remove();

    });

    $b.on('slick', '.product-card-pict', function () {
        $(this).addClass('-init');
        var t = $(this);

        if (t.find('.product-card-pict__list-main-img > *').length <= 1) return;

        t.find('.product-card-pict__list-main-img').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.product-card-pict__list-img'
        });

        t.find('.product-card-pict__list-img').slick({
            slidesToScroll: 1,
            asNavFor: '.product-card-pict__list-main-img',
            dots: true,
            arrows: false,
            focusOnSelect: true,
            variableWidth: true,
            centerMode: true,
        });

    });

    //config
    $b.on('click', '.product-list__item._has-config .product-list-cart.pArw', function (e) {
        e.preventDefault();

        var t = $(this);
        var $item = t.closest('.product-list__item');
        var $list = $item.parent();
        var itemIdx = $list.find('>.product-list__item').index($item);

        !$item.hasClass('-open-config') ? show() : hide();

        function show() {

            $list.find('>.product-list__item').removeClass('-open-config');
            $list.find('.product-list__config').remove();

            var li = setTimeout(function () {
                $item.addClass('-open-config-load');
            }, 300);

            var firstIdx = itemIdx - (itemIdx % 2);
            var secondIdx = itemIdx - (itemIdx % 2) + 1;
            var thirdIdx = itemIdx - (itemIdx % 3) + 2;
            var fourthIdx = itemIdx - (itemIdx % 4) + 3;

            var $first = $list.find('>.product-list__item').eq(firstIdx);
            var $second = $list.find('>.product-list__item').eq(secondIdx);
            var $third = $list.find('>.product-list__item').eq(thirdIdx);
            var $fourth = $list.find('>.product-list__item').eq(fourthIdx);

            if (!$second.length) {
                $second = $first;
                $third = $first;
                $fourth = $first;
            } else if (!$third.length) {
                $third = $second;
                $fourth = $second;
            } else if (!$fourth.length) {
                $fourth = $third;
            }

            $second
                .after($('<div class="product-list__config _c2" />'));
            $third
                .after($('<div class="product-list__config _c3" />'));
            $fourth
                .after($('<div class="product-list__config _c4" />'));


            $.get('/catalog/config/' + $item.attr('data-id') + '/', function (data) {
                $list.find('.product-list__config').html(data);
                $list.find('.product-list__config').each(function () {
                    $(this).height($(this).children().outerHeight(true));

                    if ($(this).hasClass('_c2') || $(this).hasClass('_c3'))
                        $(this).find('.l-1 > *').append(
                            $(this).find('.l-2 > * > *:not(.product-list__config__table__head)')
                        )
                });
                $item.addClass('-open-config');
            }).always(function () {
                clearInterval(li);
                $item.removeClass('-open-config-load');
            })
        }

        function hide() {
            $list.find('>.product-list__item').removeClass('-open-config');
            $list.find('.product-list__config').each(function () {
                $(this).height(0);
            });
            setTimeout(function () {
                $list.find('.product-list__config').remove();
            }, 500);
        }
    });

    //product
    $b.on('click', '.product-card-share > span', function (e) {
        e.preventDefault();
        $(this).closest('.product-card-share').toggleClass('-open');
    });

    //question
    $b.on('click', '.question__title', function () {
        $(this).toggleClass('-open');
    });

    //category filter wrap
    $b.on('click', '.category-filter__block__title', function () {
        $(this).parent().toggleClass('-open');
    });

    //reset filter
    $b.on('click', '.js-reset-filter', function (e) {
        e.preventDefault();

        var $f = $(this).closest('.category-filter');

        $f.find('input[type="checkbox"]').each(function () {
            $(this)[0].checked = false;
        });
        $f.find('.slider-price input').each(function () {
            $(this).jRange('setValue', $(this).attr('default-value'));
        });
        $f.trigger('change-filter');
    });

    //articles
    $b.on('click', '.js-article-full-text', function (e) {
        e.preventDefault();

        var $item = $(this).closest('.articles__item');

        $item.toggleClass('-open');

        if ($item.hasClass('-open'))
            $(this).text($(this).attr('data-full-text-label'));
        else
            $(this).text($(this).attr('data-short-text-label'));

    });

    //menu active
    $b.on('click', '.menu-nav__list > li', function () {
        $(this).parent().find('.-active').removeClass('-active');
        $(this).addClass('-active');
    });

    //sandwich
    $b.on('click', '.js-sandwich', function (e) {
        $(this).toggleClass('-open');
        $b.toggleClass('-menu-open');
    });

    //number
    $b.on('reset', '.number', function () {
        var $n = $(this).closest('.number');
        //var val = +$n.find('[type=hidden]').val() + 1

        if ($(this).attr('data-min') === '0') {
            //$n.find('.number__value').text('0');
            $n.find('.number__value').val(0);
            $n.find('[type=hidden]').val(0);
        } else {
            //$n.find('.number__value').text('01');
            $n.find('.number__value').val(1);
            $n.find('[type=hidden]').val(1);
        }

    });
    $b.on('click', '.number__plus', function () {
        var $n = $(this).closest('.number');
        var val = +$n.find('[type=hidden]').val() + 1;

        if (+$n.attr('data-max') < val) {
            return;
        }

        //$n.find('.number__value').text(val < 10 && val != 0 ? '0' + val : val);
        $n.find('.number__value').val(val);
        $n.find('[type=hidden]').val(val);
        $n.trigger('change');

        // if ($('#lk-cart-coupon-input').length) {
        //     $('#lk-cart-coupon-btn').click();
        // }
    });
    $b.on('click', '.number__minus', function () {
        var $n = $(this).closest('.number');
        var val = +($n.find('[type=hidden]').val() - 1);

        if (val < 0) {
            $(this).val(0);
            $n.find('[type=hidden]').val(0);
            return;
        }

        if ($n.attr('data-min') === '0') {
            if (val === -1) return;
        } else {
            if (val === 0) return;
        }

        //$n.find('.number__value').text(val < 10 && val != 0 ? '0' + val : val);
        $n.find('.number__value').val(val);
        $n.find('[type=hidden]').val(val);
        $n.trigger('change');
    });
    $b.on('change', '.number__value', function () {
        var $n = $(this).closest('.number');

        if ($(this).val() == '') {
            $(this).val(0);
            $n.find('[type=hidden]').val(0);
        }

        var val = +parseInt($(this).val());

        if ($n.attr('data-min') === '0') {
            if (val <= 0) {
                $(this).val($n.find('[type=hidden]').val());
                return;
            }
        }

        if (val < 1) {
            $(this).val($n.find('[type=hidden]').val());
            return;
        }

        if (+$n.attr('data-max') < val) {
            val = +$n.attr('data-max');
        }

        $(this).val(val);
        $n.find('[type=hidden]').val(val);
        $n.trigger('change');
    });

    //tabs
    $b.on('click', '.tabs-nav > a', function (e) {
        e.preventDefault();

        var t = $(this);
        var $tabs = t.closest('.tabs');

        t.parent().children().removeClass('-act');
        t.addClass('-act');

        $tabs.find('.-open').removeClass('-open');
        $tabs.find(t.attr('href')).addClass('-open');
    });

    //data-toggle
    $b.on('click', '[data-toggle]', function (e) {
        e.preventDefault();
        var t = $(this);

        $(t.attr('data-toggle')).toggleClass(t.attr('data-toggle-class'));
        t.toggleClass(t.attr('data-toggle-self-class'));
        t.toggleClass('__act');

        if (t.hasClass('__act'))
            t.find('span').text(t.attr('data-toggle-self-label'));
        else
            t.find('span').text(t.attr('data-toggle-self-def-label'));
    });

    //data-load-more-url
    $b.on('click', '[data-load-more-url]', function (e) {
        e.preventDefault();
        var t = $(this);
        var $container = $(t.attr('data-load-more-container'));
        var page = t.data('load-more-page') || 0;

        if (t.hasClass('-loading'))
            return false;

        t.addClass('-loading');
        t.data('load-more-page', ++page);

        $.get(t.attr('data-load-more-url') + '?page=' + page).done(function (data) {

            $container.find('._empty').remove();

            $container.append(data);

            if ($container.find('.last-page').length)
                t.hide();

        }).always(function () {
            t.removeClass('-loading');
        })

    });

    //product config pcp-item
    $b.on('click', '.pcp-item', function (e) {
        e.preventDefault();

        if ($(this).hasClass('-act'))
            return;

        let t = $(this);
        let id = t.attr('data-id');
        let config = window.products_configs[id];
        let $p = $b.find('.product-card');
        let uri = t.attr('href');

        t.parent().children().removeClass('-act');
        t.addClass('-act');

        // подмена URL в адресной строке браузера
        if (typeof uri !== 'undefined' && uri !== '') {
            if (history.pushState) {
                history.replaceState(null, null, window.location.protocol + "//" + window.location.host + uri);
            } else {
                console.warn('History API не поддерживает ваш браузер');
            }
        }

        $b.find('.breadcrumbs li:last-child > span').html(config.title);
        $p.find('#pr-title').html(config.title);
        $p.find('#pr-title').attr('title', config.title);
        $p.find('#pr-body_short').html(config.body_short);
        $p.find('#pr-article span').html(config.article ? config.article : $p.find('#pr-article span').attr('data-empty'));

        //brand
        if (config.brand_title) {
            $p.find('#pr-brand').parent().show();
            $p.find('#pr-brand')
                .html(config.brand_title)
                .attr('href', config.brand_url);
        } else $p.find('#pr-brand').parent().hide();

        //amount
        $p.find('#pr-amount')
            .removeClass('_v_1 _v_2 _v_3 _v_4 _v_5 _v_6 _v_7')
            .addClass('_v_' + config.amountIndex);
        $p.find('#pr-amount-text').html(config.amountTitle);

        //price
        $p.find('[data-product-id]').attr('data-product-id', config.id);
        $p.find('[data-amount]').attr('data-amount', config.amount);
        $p.find('#pr-price').html(config.frontendCurrentPrice);
        $p.find('.product-card-data__count .number').attr('data-max', config.amount);
        if (+config.sale) {
            $p.find('#pr-old-price').parent().removeClass('none');
            $p.find('#pr-sale').html(config.sale);
            $p.find('#pr-old-price').html(config.frontendOldPrice);
        } else {
            $p.find('#pr-old-price').parent().addClass('none');
        }
        $p.find('.product-card-data__count .number').trigger('reset');
        if (+config.isDeferred) {
            $p.find('.put-link-el').addClass('-act');
        } else $p.find('.put-link-el').removeClass('-act');

        //share
        $p.find('#pr-vk-link').attr('href',
            $p.find('#pr-vk-link').attr('data-base') +
            config.canonical
        );
        $p.find('#pr-fb-link').attr('href',
            $p.find('#pr-fb-link').attr('data-base') +
            config.canonical
        );
        $p.find('#pr-copy').val(config.canonical);
        $p.find('#pr-ml-link').attr('href',
            'mailto::?subject=' + config.title + '&body=' + config.canonical
        );

        //desc
        $b.find('#pr-desc').html(config.body ? config.body : 'нет описания');

        //picts
        try {
            $p.find('.product-card-pict').trigger('unslick');
            let img = JSON.parse(config.bigImages_);
            let bigImg = '';
            let smallImg = '';

            img.map(function (t) {
                bigImg += '<span class="product-card-pict__list-main-img__item" style="background-image:url(' +
                    t + ')"></span>';
                smallImg += '<span class="product-card-pict__list-img__item"><span style="background-image:url(' +
                    t + ')"></span></span>';
            });

            if (!bigImg)
                bigImg = '<span class="product-card-pict__list-main-img__item" style="background-image:url(' +
                    config.defaultPict + ')"></span>';

            if (img.length > 3)
                $p.find('#pr-pict').removeClass('none');
            else $p.find('#pr-pict').addClass('none');

            $p.find('#pr-pict-main').html(bigImg);
            $p.find('#pr-pict').html(smallImg);

            $p.find('.product-card-pict').trigger('slick');

        } catch (e) {
            console.warn(e);
        }
    });

    //delivery
    $b.on('click', '[name="UserClientOrder[delivery_type]"]', function () {
        $b.find('.delivery-form-ext')
            .hide();

        $b.find('.delivery-form-ext').find('input, textarea')
            .attr('disabled', 'disabled');

        $b.find('#' + $(this).attr('data-owner-id'))
            .show();

        $b.find('#' + $(this).attr('data-owner-id')).find('input, textarea')
            .removeAttr('disabled');

    });

    $b.on('click', '.delivery-form-tab-nav a', function (e) {
        e.preventDefault();
        $(this).parent().children().removeClass('-act');
        $(this).addClass('-act');

        $b.find('.delivery-form-tab > div').hide();
        $b.find($(this).attr('href')).show();
    });

    $.init();

    //pjax
    $('#pjax-main')
        .on('pjax:start', function () {
        })
        .on('pjax:end', function () {
            $.init()
        });

});


$(function () {

    window.attrOld = document.querySelector('meta[name="viewport"]').getAttribute("content");
    $(window).resize(function () {
        if (window.outerWidth < 1130) {
            if (document.querySelector('meta[name="viewport"]').getAttribute("content") !== "width=375, user-scalable=no") {
                document.querySelector('meta[name="viewport"]').setAttribute("content", "width=375, user-scalable=no");
            }
        } else {
            if (document.querySelector('meta[name="viewport"]').getAttribute("content") !== attrOld) {
                document.querySelector('meta[name="viewport"]').setAttribute("content", attrOld);
            }
        }
    }).resize();

});
