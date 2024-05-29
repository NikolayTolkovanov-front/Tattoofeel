/* jshint strict: true */
/*jshint sub:true*/

/**
 * @param {string} warning_message Сообщение, выводимое при нажатии на кнопку "Отправить"
 * @param {array} iml_pvz_data Список ПВЗ. Код ПВЗ, Срок доставки и цена
 *
 */
const VERSION = 1;

const STORE_TO_STORE = 1; //Доставка в ПВЗ
const STORE_TO_DOOR = 2; //Дотсавка курьером домой
const PICKUP_FROM_WAREHOUSE = 3; //Самовывоз со склада

const DELIVERY_COMPANY_CDEK = 'cdek';
const DELIVERY_COMPANY_PICK_POINT = 'pick_point';
const DELIVERY_COMPANY_IML = 'iml';
const PICKUP_FROM_BRATISLAVSKAYA = 'pickup_brat';
const DELIVERY_COMPANY_COURIER = 'courier';

const PAYMENT_TYPE_CASH = 1;
const PAYMENT_TYPE_CARD = 2;
const PAYMENT_TYPE_CHECKING = 3;
const PAYMENT_TYPE_CARD2CARD = 4;

var current_delivery_type = '';
var current_delivery_company = '';
var cdek_delivery = true;
var iml_delivery_pvz = true;
var iml_delivery_courier = true;
var pick_point_delivery = true;
var courier_delivery = true;
var disable_all = false;
var warning_message = 'Город не выбран, выберите из выпадающего списка';
var iml_pvz_data = [];
var scroll_to = {};
var commission = 0;
var addDayToDelivery = '';
var is_percent = Number($('#discount-sum').attr('data-discount-is-percent'));
var coupon_value = Number($('#discount-sum').attr('data-discount-value'));
var coupon_code = $('#discount-sum').attr('data-discount-code');

window.cityChosen = ($("#address-input").val().trim() !== '');

function setLoader(element) {
    if (window.cityChosen) {
        element.html('<div class="loader"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><p>&nbsp;Загрузка...</p></div> ')
    } else {
        element.html('<div class="loader"><i style="opacity: 0" class="fa fa-circle-o-notch fa-spin fa-fw"></i><p>&nbsp;Выберите город из выпадающего списка</p></div> ')
    }
}

function removeLoader(service) {
    if (service) {
        if (service === 'cdek') {
            $('#delivery-cdek-loader').html('');
            $('#delivery-pp-loader').html('');
            $('#delivery-iml-loader').html('');
        }
    } else {
        $('#address-loader').html('');
    }

}

window.selectCity = function (item) {
    window.cityCurrent = item;
    resetForm();
}

window.resetCity = function () {
    if (window.cityCurrent && window.cityCurrent.value === $('#address-input').val()) {
        return true;
    }
    console.log($('#address-input').val());
    $('#address-input').val('');
    window.cityChosen = false;
    $('.delivery-block .delivery-item').css('display', 'none');
    warning_message = 'Город не выбран, выберите из выпадающего списка';
}

function resetForm() {
    if ($('#cdek-city-code').val() == $('#courier_d').attr('data-native_city_id')) {
        $('#d-sam-wharehouse').css('display', 'flex');
    } else {
        $('#d-sam-wharehouse').hide();
    }

    $('.delivery-sam').hide();
    deactivateAllDelivery();
    deactivateTypeButtons();
    //Кнопки доставки и самовыввоза
    setLoader($('#d-desk-sam-both'));
    setLoader($('#d-desk-dost-both'));
    //CDEK
    setLoader($('#delivery-cdek-loader'));
    //СДЭК
    $('.delivery-form-tab').hide();
    resetCdek();
    resetCourier();
    //PickPoint
    setLoader($('#delivery-pp-loader'));
    $('#pick_d').find('.d-sam-item-data-wrapper').hide();
    resetPickPoint();
    //IML тарифы
    setLoader($('#delivery-iml-loader'));
    resetIml();
    //Варианты оплаты
    $('.pay__variant').hide();
    // //Сброс чекбоксов
    // $('.checkbox-f input').prop('checked', false);
    //Сброс суммы доставки
    $('#delivery-sum').html('0 <span class="rub">i</span>')
    // //Скрываем поле ввода адреса
    // $('.delivery-address').hide();

    $('#cdek_d').show();
    $('#pick_d').show();
    $('#iml_d').show();
    $('#courier_d').show();

    $('.hiddens').find('input').each(function () {
        if ($(this).attr('id') !== 'delivery-type') {
            $(this).val('');
        }
    });

    $('.address-pvz').html('');

    disable_all = false;
    warning_message = 'Выберите тип доставки';
}

function resetAllDeliveryBlocks() {
    resetCdek();
    resetCourier();
    resetIml();
    resetPickPoint();
}

function resetCdek() {
    var cdek_block = $('#cdek_d');
    cdek_block.find('input').each(function () {
        if (current_delivery_company !== DELIVERY_COMPANY_CDEK) {
            cdek_block.find('input').prop('checked', false);
            resetDeliverySum();
        }
    });
    $('#cdek_d .checkbox-f input').prop('checked', false);
    $('#open-pvz-cdek').html().replace('Изменить', 'Выбрать');
    $("#cdek_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
}

function resetCourier() {
    var courier_block = $('#courier_d');
    courier_block.find('input').each(function () {
        if (current_delivery_company !== DELIVERY_COMPANY_COURIER) {
            courier_block.find('input').prop('checked', false);
            resetDeliverySum();
        }
    });
    $('#courier_d .checkbox-f input').prop('checked', false);
    $("#courier_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
    $('#courier-time-interval').val('');
}

function resetPickPoint() {
    if ($('#open-pvz-pickpoint').length > 0) {
        $('#open-pvz-pickpoint').html().replace('Изменить', 'Выбрать');
        $("#pick_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
    }
}

function resetIml() {
    $('#open-pvz-iml').html().replace('Изменить', 'Выбрать');
    $("#iml_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
}

function resetOpenPvzLinks() {
    $('.select-pvz-link').each(function () {
        var link = $(this).find('a');
        var replace_result = link.html().replace('Изменить', 'Выбрать');
        link.html(replace_result);
    });
}

function resetSelectedAddresses() {
    $('#selected-address-CDEK').html('');
    $('#selected-address-iml').html('');
    $('#selected-address-PickPoint').html('');

}

function resetDeliverySum() {
    if (typeof window.intrpt !== "undefined") {
        undefinedFunc();
    }

    let product_cost = Number($('#product-sum').attr('data-product-price')) * 100;
    let discount_sum = Number($('#discount-sum').attr('data-discount-sum')) * 100;

    $('#delivery-sum').html('0 <span class="rub">i</span>');
    $('#delivery_sum').val(0);
    commission = 0;
    $('#commission-sum').html('0 <span class="rub">i</span>');
    //$('#total-sum').html($('#product-sum').html());
    $('#discount-sum').html((discount_sum / 100) + ' <span class="rub">i</span>');
    $('#total-sum').html(((product_cost - discount_sum) / 100) + ' <span class="rub">i</span>');
}

function cleanInputs(arr) {
    $.each(arr, function (index, value) {
        $('#' + value).val('');
    });
}

/** Доставка курьером */
function setCourier() {
    current_delivery_type = STORE_TO_DOOR;
    $('#delivery-type').val(2);
    resetSelectedAddresses();
    setViewsBlocks();
    resetDeliverySum();

    //Очищаем поля доставки в ПВЗ
    var ids = ['pvz-code', 'pvz-address', 'pvz-info', 'delivery-period-max', 'delivery-tariff-name', 'courier-time-interval'];
    cleanInputs(ids);
}

/** Самовывоз */
function setPickup() {
    current_delivery_type = STORE_TO_STORE;
    $('#delivery-type').val(1);
    resetSelectedAddresses();
    setViewsBlocks();
    resetDeliverySum();

    //Очищаем поля доставки курьером
    var ids = ['delivery-period-max', 'delivery-tariff-name', 'courier-time-interval'];
    cleanInputs(ids);

}

/** Самовывоз со склада*/
function setPickupFromWarehouse() {
    current_delivery_type = PICKUP_FROM_WAREHOUSE;
    $('#delivery-type').val(3);
    resetSelectedAddresses();
    setViewsBlocks();
    resetDeliverySum();

    //Очищаем поля доставки курьером
    var ids = ['delivery-region', 'pvz-code', 'pvz-address', 'pvz-info', 'delivery-period-max', 'delivery-tariff-name', 'courier-time-interval'];
    cleanInputs(ids);
    setActivePickupFromBrat();
}

function initMap(pvz_list) {
    $('#map').html('');
    var myMap;
    var city_name = pvz_list[0]['city_name'];
    var myPlacemarks = [];

    var myGeocoder = ymaps.geocode(city_name, {results: 1});
    myGeocoder.then(
        function (res) {
            // Выбираем первый результат геокодирования.
            var firstGeoObject = res.geoObjects.get(0);
            // Координаты геообъекта.
            myMap = new ymaps.Map("map", {
                // Координаты центра карты.
                // Порядок по умолчанию: «широта, долгота».
                // Чтобы не определять координаты центра карты вручную,
                // воспользуйтесь инструментом Определение координат.
                center: firstGeoObject.geometry.getCoordinates(),
                // Уровень масштабирования. Допустимые значения:
                // от 0 (весь мир) до 19.
                zoom: 11,
                controls: ['zoomControl']
            });
            // Создаем экземпляр класса ymaps.control.SearchControl
            var mySearchControl = new ymaps.control.SearchControl({
                options: {
                    noPlacemark: true,
                    provider: 'yandex#search'
                }
            });
            // Результаты поиска будем помещать в коллекцию.
            var mySearchResults = new ymaps.GeoObjectCollection(null, {
                hintContentLayout: ymaps.templateLayoutFactory.createClass('$[properties.name]')
            });
            myMap.controls.add(mySearchControl);
            myMap.geoObjects.add(mySearchResults);

            $.each(pvz_list, function (index, pvz) {
                var placemark = new ymaps.Placemark([pvz.y, pvz.x], {
                    // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                    balloonContentHeader: pvz['name'],
                    balloonContentBody: pvz['address'],
                    hintContent: pvz['address']
                });
                //Добавляем событие
                placemark.events.add('click', function (e) {
                    var props = e.get('target').properties;
                    var address = props.get('balloonContentBody');
                    var full_address = $('#address-input').val() + ', ' + address;
                    var region = $('#delivery-region').val();
                    var pvz_code = '';
                    var elem_address = '';

                    $('input[name="UserClientOrder[address_delivery_pvz]"]').val(region + ', ' + full_address);

                    //Ищем адрес в списке
                    $('.address-pvz-item').each(function () {
                        elem_address = $(this).find('.item-info-address-pvz').html();
                        if (elem_address.indexOf(address) !== -1) {
                            var target_element = $(this);
                            $('.address-pvz-item').hide();
                            $(target_element).show();
                            $('#back-to-list-pvz').show();
                            pvz_code = target_element.find('.item-info').attr('data-pvz-code');
                            $('#pvz-code').val(pvz_code);
                            //Инфо о ПВЗ
                            var info = [];
                            info[0] = target_element.find('.addresses-pvz-phone').html();
                            info[1] = target_element.find('.addresses-pvz-work-time').html();
                            $('#pvz-info').val(info.join(', '));
                            return false;
                        }
                    });

                    $('#pvz-address').val(elem_address);

                    resetSelectedAddresses();
                    resetOpenPvzLinks();
                    var link_open_pvz_list = '';
                    if (current_delivery_company === DELIVERY_COMPANY_CDEK) {
                        $('#selected-address-CDEK').html(elem_address);
                        link_open_pvz_list = $('#open-pvz-cdek');
                    } else if (current_delivery_company === DELIVERY_COMPANY_IML) {
                        $('#selected-address-iml').html(elem_address);
                        link_open_pvz_list = $('#open-pvz-iml');
                    } else if (current_delivery_company === DELIVERY_COMPANY_PICK_POINT) {
                        $('#selected-address-PickPoint').html(elem_address);
                        link_open_pvz_list = $('#open-pvz-pickpoint');
                    }
                    var result = link_open_pvz_list.html().replace('Выбрать', 'Изменить');
                    link_open_pvz_list.html(result);
                    //Кнопка "заберу здесь"
                    $('#selected-pvz').show();
                    showPayVariant();
                });
                myPlacemarks.push(placemark);
            });

            var clusterer = new ymaps.Clusterer({
                /**
                 * Через кластеризатор можно указать только стили кластеров,
                 * стили для меток нужно назначать каждой метке отдельно.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage.xml
                 */
                preset: 'islands#invertedVioletClusterIcons',
                /**
                 * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
                 */
                groupByCoordinates: true,
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
    );
}

window.listPvzCache = {};

function getPvz(ds, city_name) {
    let result = null;
    if (typeof window.listPvzCache[city_name + "__" + ds] === "undefined") {
        $.ajax({
            url: "/lk/pvz/",
            type: 'GET',
            async: false,
            data: {
                ds: ds,
                city_name: city_name
            },
            success: function (res) {
                window.listPvzCache[city_name + "__" + ds] = res;
            }
        });
    }
    const res = window.listPvzCache[city_name + "__" + ds];
    $('#addresses-pvz').html(res.html);
    $('#delivery-region').val(res.data[0]['region']);
    result = res.data;
    return result;
}

function setCityCodes(data) {
    if (data.cdek.status === true) {
        $('#cdek-city-code').val(data.cdek.data)
    }
}

function setOddEven(elements) {
    var counter = 1;
    elements.each(function () {
        if (counter % 2 === 1) {
            $(this).addClass('odd')
        } else {
            $(this).addClass('even')
        }
        counter++;
    });
}

window.refreshCityCodes = function () {
    var cdek_city_code = $('#cdek-city-code').val();
    var cdek_city_name = $('#address-input').val();
    window.cityChosen = true;
    $.ajax({
        url: '/lk/get-city-codes/?cdek_city_id=' + cdek_city_code + '&cdek_city_name=' + cdek_city_name,
        type: "GET",
        success: function (data) {
            window.city_codes = data;
            setCityCodes(data);
            getInfo();
        },
    });
};

function showMinMaxSysDeliv() {
    var text_pvz = '';
    //Кнопка курьер
    if ($('#cdek-city-code').val() == $('#courier_d').attr('data-native_city_id')) { // наш курьер
        let min_courier_cost = 0;

        $('#courier').find('.delivery-cost').each(function (index) {
            let current_min = +$(this).attr('data-delivery-cost');

            if (index == 0 || current_min < min_courier_cost) {
                min_courier_cost = current_min;
            }
        });
        text_pvz = '0 - 1 дн' + addDayToDelivery + ', от '
            + min_courier_cost + ' <span class="rub">i</span>';
    } else {
        if (window.minTimeDeliv && window.maxTimeDeliv && window.minCostDeliv) {
            text_pvz = window.minTimeDeliv
                + ' - '
                + window.maxTimeDeliv
                + ' дн' + addDayToDelivery + ', от '
                + window.minCostDeliv + ' <span class="rub">i</span>';
        } else {
            text_pvz = 'Нет доставки';
        }
    }
    //Текст на кнопке Курьер
    var d_desk_dost = $('#d-desk-dost-both');
    d_desk_dost.html(text_pvz);
    d_desk_dost.show();
}

function showMinMaxSysPvz() {
    //Текст на кнопке самовывоз
    if (window.minTimePvz && window.maxTimePvz && window.minCostPvz) {
        var text_courier = window.minTimePvz
            + ' - '
            + window.maxTimePvz
            + ' дн' + addDayToDelivery + ', от '
            + window.minCostPvz + ' <span class="rub">i</span>';

    } else {
        text_courier = 'Нет доставки'
    }
    //Текст на кнопке самовывоз
    var d_desk_sam = $('#d-desk-sam-both');
    d_desk_sam.html(text_courier);
    d_desk_sam.show();
}

function getInfo(city_codes = false) {
    if (!city_codes) city_codes = window.city_codes;
    resetForm();
    setCityCodes(city_codes);

    setTimeout(function () {
        window.minTimePvz = false;
        window.maxTimePvz = false;
        window.minTimeDeliv = false;
        window.maxTimeDeliv = false;
        window.minCostPvz = false;
        window.maxCostPvz = false;
        window.minCostDeliv = false;
        window.maxCostDeliv = false;
        window.delivSysCount = 2;
        window.pvzSysCount = 2;
        window.delivSysProcessed = 0;
        window.pvzSysProcessed = 0;
        window.cdekLoading = 2;
        window.cdekLoaded = 0;
        window.imlLoading = 2;
        window.imlLoaded = 0;

        var addDayToDelivery = $('#delivery-form').attr('data-add-day') == 1 ? ' (+1 раб. день)' : '';
        var add1Day = $('#delivery-form').attr('data-add-day') == 1 ? 1 : 0;
        var city = $('#address-input').val();
        var cdek_city_code = $('#cdek-city-code').val();
        if (typeof window.ajxSdekCour !== "undefined") {
            window.ajxSdekCour.abort();
        }
        window.ajxSdekCour = $.ajax({
            url: '/lk/get-sdek-courier-sum-and-period/?cdek_city_code=' + cdek_city_code,
            type: "GET",
            success: function (data) {
                window.delivSysProcessed++;
                window.cdekLoaded++;
                if (data.sum_min && !window.minCostDeliv) {
                    window.minCostDeliv = data.sum_min;
                }
                if (data.period_min && !window.minTimeDeliv) {
                    window.minTimeDeliv = data.period_min;
                }
                if (data.period_max && !window.maxTimeDeliv) {
                    window.maxTimeDeliv = data.period_max;
                }
                if (data.sum_min && data.sum_min < window.minCostDeliv) {
                    window.minCostDeliv = data.sum_min;
                }
                if (data.period_min && data.period_min < window.minTimeDeliv) {
                    window.minTimeDeliv = data.period_min;
                }
                if (data.period_max && data.period_max > window.maxTimeDeliv) {
                    window.maxTimeDeliv = data.period_max;
                }

                console.log(data);
                let html = '';
                if ("tariffs" in  data &&  data.tariffs.length > 0) {
                    $('#cdek > .delivery-form-tab').show();
                    $('#delivery-form-tab-courier').show().find(".delivery-params").remove();
                    for (let i in data.tariffs) {
                        let tariff = data.tariffs[i];
                        html += '<div class="delivery-params ' + (i % 2 === 0 ? 'even' : 'odd') + '">' +
                            '<div class="delivery-name">' +
                            '<label class="checkbox-f">' +
                            '<input name="delivery_cdek_rate" value="' + tariff.price + '" type="radio" delivery_sum="' + tariff.price + '">' +
                            '<i></i>' +
                            '<span>' + tariff.tariffName + '</span>' +
                            '</label>' +
                            '</div>' +
                            '<div class="delivery-timing" data-period-max="' + (+tariff.deliveryPeriodMax + add1Day) + '">' +
                            '' + tariff.deliveryPeriodMin + ' - ' + tariff.deliveryPeriodMax + ' дн' + addDayToDelivery +
                            '</div>' +
                            '<div data-delivery-cost="' + tariff.price + '" class="delivery-cost">' +
                            '' + tariff.price + ' ' +
                            '<span class="rub">i</span>' +
                            '</div>' +
                            '</div>';
                    }
                    $('#delivery-form-tab-courier').prepend($(html));
                }
                if (window.cdekLoaded === window.cdekLoading) {
                    removeLoader('cdek')
                }
                //if (window.delivSysProcessed === window.delivSysCount) {
                    showMinMaxSysDeliv();
                //}
            },
        });

        if (typeof window.ajxSdekPvz !== "undefined") {
            window.ajxSdekPvz.abort();
        }
        window.ajxSdekPvz = $.ajax({
            url: '/lk/get-sdek-pvz-sum-and-period/?cdek_city_code=' + cdek_city_code,
            type: "GET",
            success: function (data) {
                window.pvzSysProcessed++;
                window.cdekLoaded++;
                if (data.sum_min && !window.minCostPvz) {
                    window.minCostPvz = data.sum_min;
                }
                if (data.period_min && !window.minTimePvz) {
                    window.minTimePvz = data.period_min;
                }
                if (data.period_max && !window.maxTimePvz) {
                    window.maxTimePvz = data.period_max;
                }
                if (data.sum_min && data.sum_min < window.minCostPvz) {
                    window.minCostPvz = data.sum_min;
                }
                if (data.period_min && data.period_min < window.minTimePvz) {
                    window.minTimePvz = data.period_min;
                }
                if (data.period_max && data.period_max > window.maxTimePvz) {
                    window.maxTimePvz = data.period_max;
                }

                console.log(data);
                let html = '';
                if ("tariffs" in data && data.tariffs.length > 0) {
                    $('#cdek > .delivery-form-tab').show();
                    $('#delivery-form-tab-pvz').show().find(".delivery-params").remove();
                    for (let i in data.tariffs) {
                        let tariff = data.tariffs[i];
                        let price = tariff.price;
                        html += '<div class="delivery-params ' + (i % 2 === 0 ? 'even' : 'odd') + '">' +
                            '<div class="delivery-name">' +
                            '<label class="checkbox-f">' +
                            '<input name="delivery_cdek_rate" value="' + price + '" type="radio" delivery_sum="' + price + '">' +
                            '<i></i>' +
                            '<span>' + tariff.tariffName + '</span>' +
                            '</label>' +
                            '</div>' +
                            '<div class="delivery-timing" data-period-max="' + (+tariff.deliveryPeriodMax + add1Day) + '">' +
                            '' + tariff.deliveryPeriodMin + ' - ' + tariff.deliveryPeriodMax + ' дн' + addDayToDelivery +
                            '</div>' +
                            '<div data-delivery-cost="' + price + '" class="delivery-cost">' +
                            '' + price + ' ' +
                            '<span class="rub">i</span>' +
                            '</div>' +
                            '</div>';
                    }
                    $('#delivery-form-tab-pvz').prepend($(html));
                }
                if (window.cdekLoaded === window.cdekLoading) {
                    removeLoader('cdek')
                }
                showMinMaxSysPvz();
            },
        });

        if (typeof window.ajxSdekPvzInfo !== "undefined") {
            window.ajxSdekPvzInfo.abort();
        }
        window.ajxSdekPvzInfo = $.ajax({
            url: '/lk/get-sdek-pvz-info/?cdek_city_code=' + cdek_city_code,
            type: "GET",
            success: function (data) {
                if (data.pvz.length == 0) {
                    $('#pvz-cdek-not-found').html('Система не смогла найти адреса пунктов выдачи. Продолжайте оформление заказа - мы перезвоним Вам и поможем с выбором ПВЗ');
                    $('#open-pvz-cdek').html('');
                } else {
                    $('#open-pvz-cdek').html('Выбрать пункт выдачи (' + data.pvz.length + ')');
                    $('#pvz-cdek-not-found').html('');
                }
            },
        });

        if (cdek_city_code == $('#courier_d').attr('data-native_city_id')) {
            $('#courier_d .delivery-form-tab').show();
        } else {
            $('#courier_d .delivery-form-tab').hide();
        }

        if (cdek_city_code == $('#courier_d').attr('data-native_city_id')) {
            $('#d-sam-wharehouse').css('display', 'flex');
        } else {
            $('#d-sam-wharehouse').hide();
        }
    }, 60);
    return;

}

function showResponse(elem, data_object) {
    $.each(data_object, function (key, value) {
        if (typeof value === 'object') {
            showResponse(elem, value);
        } else {
            if (key === 'Job' && value === 'С24') {
                elem.append('========================== <br>')
            }
            elem.append('<div>' + key + ': ' + value + '</div>');
        }
    });
}

function setTotalPrice(delivery_cost) {
    let product_cost = Number($('#product-sum').attr('data-product-price')) * 100;
    let discount_sum = Number($('#discount-sum').attr('data-discount-sum')) * 100;
    let delivery_discount = is_percent ? delivery_cost / 100 * coupon_value : 0;

    $('#delivery_sum').val(delivery_cost);
    $('#delivery-sum').html((delivery_cost / 100) + ' <span class="rub">i</span>');
    commission = 0;
    $('#commission-sum').html('0 <span class="rub">i</span>');
    if (is_percent) {
        $('#discount-sum').html(Math.floor((delivery_discount + discount_sum) / 100) + ' <span class="rub">i</span>');
    }
    $('#total-sum').html(Math.ceil((delivery_cost + product_cost - discount_sum - delivery_discount) / 100) + ' <span class="rub">i</span>');
}

function setTotalWithCommission() {
    let product_cost = Number($('#product-sum').attr('data-product-price'));
    let delivery_cost = Number($('#delivery-sum').text().replace(/[^.\d]+/g, "").replace(/^([^\.]*\.)|\./g, '$1'));
    let commission_cost = Math.ceil((delivery_cost + product_cost) / 100 * commission);
    let discount_sum = Number($('#discount-sum').attr('data-discount-sum'));
    let delivery_discount = is_percent ? delivery_cost / 100 * coupon_value : 0;

    $('#commission-sum').html(commission_cost + ' <span class="rub">i</span>');
    $('#total-sum').html(Math.ceil(delivery_cost + product_cost + commission_cost - discount_sum - delivery_discount) + ' <span class="rub">i</span>');
}

function setViewsBlocks() {
    // debugger;
    if (disable_all === true) {
        //Сброс кнопок доставка и курьер
        $('#d-dostavka').removeClass("d-active");
        $('#d-dostavka .d-inp span').removeClass("s-span-active");
        $('#d-sam').removeClass("d-active");
        $('#d-sam .d-inp span').removeClass("s-span-active");
        $('#d-sam-wharehouse').removeClass("d-active");
        $('#d-sam-wharehouse .d-inp span').removeClass("s-span-active");
        $('#d-desk-sam-both').html('Нет доставки').show();
        $('#d-desk-dost-both').html('Нет доставки').show();

        return false;
    }

    if (current_delivery_type !== '' && current_delivery_type !== PICKUP_FROM_WAREHOUSE) {
        $('.delivery-sam').show();
        $('.delivery-sam-item').show();
    } else {
        $('.delivery-sam').hide();
        $('.delivery-sam-item').hide();
    }


    if (current_delivery_type === STORE_TO_STORE) {
        $('#delivery-form-tab-courier').hide();
        $('#delivery-form-tab-pvz').show();
        $('.delivery-address').hide();
        $('.select-pvz-link').show();
        if (iml_delivery_pvz) {
            $('#iml_d').show();
        } else {
            $('#iml_d').hide();
        }
        $('#iml-delivery-form-tab-pvz').show();
        $('#iml-delivery-form-tab-courier').hide();
    }

    if (current_delivery_type === STORE_TO_DOOR) {
        $('#delivery-form-tab-courier').show();
        $('#delivery-form-tab-pvz').hide();
        $('.delivery-address').show();
        $('.select-pvz-link').hide();
        if (iml_delivery_courier) {
            $('#iml_d').show();
        } else {
            $('#iml_d').hide();
        }
        $('#iml-delivery-form-tab-pvz').hide();
        $('#iml-delivery-form-tab-courier').show();
    }

    if (current_delivery_type === PICKUP_FROM_WAREHOUSE) {
        $('#delivery-form-tab-courier').hide();
        $('#delivery-form-tab-pvz').hide();
        $('.delivery-address').hide();
        $('.select-pvz-link').hide();
        $('#iml_d').hide();
        $('#iml-delivery-form-tab-pvz').hide();
        $('#iml-delivery-form-tab-courier').show();
    }

    if (cdek_delivery === false) {
        $('#cdek_d').hide();
    } else {
        $('#cdek_d').show();
    }

    if (pick_point_delivery === true && current_delivery_type === STORE_TO_STORE) {
        // debugger;
        $('#pick_d').show();
    } else {
        $('#pick_d').hide();
    }

    if (current_delivery_type === STORE_TO_DOOR && $('#cdek-city-code').val() == $('#courier_d').attr('data-native_city_id')) {
        $('#cdek_d').hide();
        $('#courier_d').show();
    } else {
        $('#cdek_d').show();
        $('#courier_d').hide();
    }
}

function setMaxPeriod(item, isCdek, isIml = false) {
    var max_period = 0;
    if (isCdek === false) {
        if (isIml === false) {
            max_period = item.find('.delivery-sam-item-data-date').attr('data-period-max');
        } else {
            if (iml_delivery_courier === true) {
                max_period = $('#iml-delivery-date-courier').attr('data-period-max');
            } else if (iml_delivery_pvz === true) {
                max_period = $('#iml-delivery-date-pvz').attr('data-period-max');
            }
        }
    } else {
        max_period = item.parents('.delivery-params').find('.delivery-timing').attr('data-period-max');
    }
    $('#delivery-period-max').val(max_period);
}

function setTariffName(element, dc) {
    var tariff_name = '';
    if (dc === DELIVERY_COMPANY_CDEK) {
        tariff_name = element.find('span').html();
    } else if (dc === DELIVERY_COMPANY_COURIER) {
        tariff_name = element.find('span').html();
    } else if (dc === DELIVERY_COMPANY_PICK_POINT) {
        tariff_name = element.attr('data-tariff-name');
    } else if (dc === DELIVERY_COMPANY_IML) {
        if (current_delivery_type === STORE_TO_STORE) {
            tariff_name = $('#iml-delivery-form-tab-pvz').attr('data-tariff-name');
        } else {
            tariff_name = $('#iml-delivery-form-tab-courier').attr('data-tariff-name');
        }
    }
    $('#delivery-tariff-name').val(tariff_name);
}

function showOrderBtn(show_submit) {
    let paymentType = parseInt($('input[name="UserClientOrder[payment_type]"]:checked').val());

    $.get({
        url: "/lk/get-commission-info/",
        data: {
            payment_type_id: paymentType,
        },
        success: function (data) {
            $('.pay__variant-info-text').html(data.info);
            if (show_submit) {
                commission = data.percent;
                setTotalWithCommission();
            }
        }
    });

    if (show_submit) {
        $('.pay__variant').css('display', 'flex');
        switch (paymentType) {
            case PAYMENT_TYPE_CASH:
            case PAYMENT_TYPE_CHECKING:
                $('#fake-submit-btn').hide();
                $('#card-pay-buttons').hide();
                $('#card-2-card-pay-buttons').hide();
                $('#real-submit-btn').show();
                break;
            case PAYMENT_TYPE_CARD:
                $('#fake-submit-btn').hide();
                $('#card-2-card-pay-buttons').hide();
                $('#real-submit-btn').hide();
                //$('#card-pay-buttons').css('display', 'flex');
                $('#card-pay-buttons').show();
                break;
            case PAYMENT_TYPE_CARD2CARD:
                $('#fake-submit-btn').hide();
                $('#real-submit-btn').hide();
                $('#card-pay-buttons').hide();
                $('#card-2-card-pay-buttons').show();
                break;
            default:
                $('#card-pay-buttons').hide();
                $('#card-2-card-pay-buttons').hide();
                $('#real-submit-btn').hide();
                $('#fake-submit-btn').show();
        }
    } else {
        $('.pay__variant').css('display', 'none');
        $('#card-pay-buttons').hide();
        $('#card-2-card-pay-buttons').hide();
        $('#real-submit-btn').hide();
        $('#fake-submit-btn').show();
    }
}

function showPayVariant() {
    var delivery_address_courier = '';
    var delivery_address_pvz = $('#pvz-address').val();
    if (current_delivery_type === STORE_TO_DOOR) {
        if (current_delivery_company === DELIVERY_COMPANY_CDEK) {
            delivery_address_courier = $('#cdek-delivery-address-courier').val();
        } else if (current_delivery_company === DELIVERY_COMPANY_COURIER) {
            delivery_address_courier = $('#courier-delivery-address-courier').val();
        } else if (current_delivery_company === DELIVERY_COMPANY_IML) {
            delivery_address_courier = $('#iml-delivery-address-courier').val().trim();
        }
    }

    var tariff = $('#delivery-tariff-name').val();
    if (current_delivery_company === DELIVERY_COMPANY_COURIER) {
        tariff = $('#courier-time-interval').val();
    }
    if (current_delivery_type === STORE_TO_STORE) {
        if (delivery_address_pvz && tariff) {
            showOrderBtn(true);
        } else if (tariff) {
            warning_message = 'Укажите пункт выдачи';
            showOrderBtn(true);
        } else {
            warning_message = 'Укажите пункт выдачи и тариф';
            showOrderBtn(false);
        }
    } else if (current_delivery_type === STORE_TO_DOOR) {
        if (delivery_address_courier && tariff) {
            showOrderBtn(true);
        } else if (delivery_address_courier && current_delivery_company === DELIVERY_COMPANY_IML) {
            showOrderBtn(true);
        } else if (tariff || current_delivery_company === DELIVERY_COMPANY_IML) {
            warning_message = 'Укажите адрес доставки';
            showOrderBtn(false);
        } else {
            warning_message = 'Укажите адрес доставки и тариф';
            showOrderBtn(false);
        }
    } else if (current_delivery_type === PICKUP_FROM_WAREHOUSE) {
        showOrderBtn(true);
    }
}

function deactivateAllDelivery() {
    $("#cdek_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
    $("#courier_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
    $("#pick_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
    $("#iml_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").removeClass("s-span-active");
}

function deactivateTypeButtons() {
    var d_sam = $('#d-sam');
    d_sam.find('.delivery-circle').removeClass('active');
    d_sam.removeClass("d-active");
    var d_dostavka = $('#d-dostavka');
    d_dostavka.find('.delivery-circle').removeClass('active');
    d_dostavka.removeClass("d-active");
    var d_sam_wharehouse = $('#d-sam-wharehouse');
    d_sam_wharehouse.find('.delivery-circle').removeClass('active');
    d_sam_wharehouse.removeClass("d-active");
}

function setActiveCdek() {
    $("#cdek_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    if (current_delivery_company === DELIVERY_COMPANY_CDEK) return;
    deactivateAllDelivery();
    resetIml();
    resetPickPoint();
    resetCourier();
    $('#delivery-service').val(DELIVERY_COMPANY_CDEK);
    current_delivery_company = DELIVERY_COMPANY_CDEK;
}

function setActiveIml() {
    $("#iml_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    if (current_delivery_company === DELIVERY_COMPANY_IML) return;

    deactivateAllDelivery();
    resetCdek();
    resetPickPoint();
    resetCourier();
    $("#iml_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    $('#delivery-service').val(DELIVERY_COMPANY_IML);
    current_delivery_company = DELIVERY_COMPANY_IML;
}

function setActivePickPoint() {
    if (current_delivery_company === DELIVERY_COMPANY_PICK_POINT) return;

    deactivateAllDelivery();
    resetCdek();
    resetIml();
    resetCourier();
    $("#pick_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    $('#delivery-service').val(DELIVERY_COMPANY_PICK_POINT);
    current_delivery_company = DELIVERY_COMPANY_PICK_POINT;
}

function setActivePickupFromBrat() {
    if (current_delivery_company === PICKUP_FROM_BRATISLAVSKAYA) return;
    deactivateAllDelivery();
    resetCdek();
    resetIml();
    resetPickPoint();
    resetCourier();
    $('#delivery-service').val(PICKUP_FROM_BRATISLAVSKAYA);
    current_delivery_company = PICKUP_FROM_BRATISLAVSKAYA;
}

function setActiveCourier() {
    if (current_delivery_company === DELIVERY_COMPANY_COURIER) return;

    deactivateAllDelivery();
    resetCdek();
    resetIml();
    resetPickPoint();
    $("#courier_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    $('#delivery-service').val(DELIVERY_COMPANY_COURIER);
    current_delivery_company = DELIVERY_COMPANY_COURIER;
}

function scroll(element) {
    var scrollTop = element.offset().top;
    $(document).scrollTop(scrollTop);
}

$("#cdek_d").click(function () {
    if ($(this).find('[name="UserClientOrder[delivery_service]"] + .s-span-active').length > 0) {
        return;
    }
    $("#cdek_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    resetIml();
    resetPickPoint();
    resetCourier();
    setActiveCdek();
    showPayVariant();
});
$("#courier_d").click(function () {
    if ($(this).find('[name="UserClientOrder[delivery_service]"] + .s-span-active').length > 0) {
        return;
    }
    $("#courier_d").find(".delivery-sam-item-title").find(".delivery-sam-radio").find("span").addClass("s-span-active");
    resetIml();
    resetPickPoint();
    resetCdek();
    setActiveCourier();
    showPayVariant();
});
$("#pick_d").click(function () {
    resetIml();
    resetCdek();
    resetCourier();
    setActivePickPoint();
    showPayVariant();
    if (current_delivery_type === 'store-to-store') {
        $(this).find('.select-pvz-link').show();
    }
    var delivery_cost = Number($('#pp-delivery-price').attr('data-delivery-cost')) * 100;
    setTariffName($(this), DELIVERY_COMPANY_PICK_POINT);
    setTotalPrice(delivery_cost);
    setMaxPeriod($(this), false);

});
$("#iml_d").click(function () {
    setActiveIml();
    resetCdek();
    resetPickPoint();
    resetCourier();
    var delivery_cost = 0;
    if (current_delivery_type === STORE_TO_STORE) {
        delivery_cost = Number($('#iml-delivery-price-pvz').attr('data-delivery-cost')) * 100;
    } else {
        delivery_cost = Number($('#iml-delivery-price-courier').attr('data-delivery-cost')) * 100;
    }
    setTariffName($(this), DELIVERY_COMPANY_IML);
    setTotalPrice(delivery_cost);
    setMaxPeriod($(this), false, true);
    showPayVariant();
});

$("#pay__visit").click(function () {
    $(this).addClass("pay__check");
    $("#pay__online").removeClass("pay__check");
})
$("#pay__online").click(function () {
    $(this).addClass("pay__check");
    $("#pay__visit").removeClass("pay__check");
});


$("#d-sam").click(function () {
    $(this).addClass("d-active");
    $(this).find('.delivery-circle').addClass('active');
    $('#d-dostavka').find('.delivery-circle').removeClass('active');
    $('#d-dostavka').removeClass("d-active");
    $('#d-sam-wharehouse').find('.delivery-circle').removeClass('active');
    $('#d-sam-wharehouse').removeClass("d-active");

    $('#delivery-type').val(1);
    warning_message = 'Выберите компанию (тариф доставки)';

    deactivateAllDelivery();
    resetAllDeliveryBlocks();
    setPickup();
    showPayVariant();
});
$("#d-dostavka").click(function () {
    $(this).addClass("d-active");
    $(this).find('.delivery-circle').addClass('active');
    $('#d-sam').find('.delivery-circle').removeClass('active');
    $('#d-sam').removeClass("d-active");
    $('#d-sam-wharehouse').find('.delivery-circle').removeClass('active');
    $('#d-sam-wharehouse').removeClass("d-active");

    $('#delivery-type').val(2);
    warning_message = 'Выберите компанию (тариф доставки)'

    deactivateAllDelivery();
    resetAllDeliveryBlocks();
    setCourier();
    showPayVariant();
});
$("#d-sam-wharehouse").click(function () {
    $(this).addClass("d-active");
    $(this).find('.delivery-circle').addClass('active');
    $('#d-dostavka').find('.delivery-circle').removeClass('active');
    $('#d-dostavka').removeClass("d-active");
    $('#d-sam').find('.delivery-circle').removeClass('active');
    $('#d-sam').removeClass("d-active");

    $('#delivery-type').val(3);
    warning_message = 'Выберите компанию (тариф доставки)';

    deactivateAllDelivery();
    resetAllDeliveryBlocks();
    setPickupFromWarehouse();
    showPayVariant();
});

$(document).on('change', 'input[name="UserClientOrder[delivery_service]"]', function () {
    resetOpenPvzLinks();
    resetSelectedAddresses();
});

$(document).on('click', '#selected-pvz', function (e) {
    e.preventDefault();
    $(this).hide();
    $('#back-to-list-pvz').hide();
    if (current_delivery_company !== DELIVERY_COMPANY_CDEK) {
        if ($('.address-pvz-item:visible').length === 1) {
            if ($('.address-pvz-item:visible').find('.item-info-delivery-price').length === 1) {
                $('#iml-delivery-price-pvz').html($('.address-pvz-item:visible').find('.item-info-delivery-price').html())
                    .attr("data-delivery-cost", $('.address-pvz-item:visible').find('.item-info-delivery-price span').text().replace(/\D/g, ''));
                if ($('#iml-delivery-form-tab-pvz:visible').length > 0) {
                    current_delivery_type = PICKUP_FROM_WAREHOUSE;
                    setTotalPrice($('.address-pvz-item:visible').find('.item-info-delivery-price span').text().replace(/\D/g, '') * 100)
                }
            }
            if ($('.address-pvz-item:visible').find('.item-info-delivery-date').length === 1) {
                $('#iml-delivery-date-pvz').html($('.address-pvz-item:visible').find('.item-info-delivery-date').html());
            }
        }
    }
    showPayVariant();
});

$(document).on('click', '.delivery-name .checkbox-f', function () {
    if (!$(this).find('input').prop("disabled")) {
        var delivery_sum = Number($(this).parents('.delivery-params').find('.delivery-cost').attr('data-delivery-cost')) * 100;
        if ($('#pvz-address').val() === '') {
            resetOpenPvzLinks();
            resetSelectedAddresses();
        }

        if (current_delivery_type == STORE_TO_DOOR) {
            setTariffName($(this), DELIVERY_COMPANY_COURIER);
            setMaxPeriod($(this), true);
            $('#courier-time-interval').val($(this).find('input').val());
            showPayVariant();
            setTotalPrice(delivery_sum);
        } else {
            setTariffName($(this), DELIVERY_COMPANY_CDEK);
            setMaxPeriod($(this), true);
            showPayVariant();
            setTotalPrice(delivery_sum);
        }
    }
});

window.selectItem = function () {
    resetForm();
    if ($('#address-input').val() !== 0) {
        $('.delivery-block .delivery-item').css('display', 'flex');

        //Получаем информцию по названию города
        getInfo()
    }
}

$(document).on('click', '#ui-id-1 li', window.selectItem);

$(function () {
    if ($('#address-input').val()) {
        $('.delivery-block .delivery-item').css('display', 'flex');
        getInfo();
    }
});

$(document).on('click', '#back-to-list-pvz', function () {
    $('.address-pvz-item').show();
    $('.footer-selected-pvz a').hide();
    $(this).hide();
});

$(document).on('click', '.address-pvz-item', function () {
    var address = $(this).find('.item-info-address-pvz').html();
    var region = $('#delivery-region').val();
    $('.address-pvz-item').hide();
    $(this).show();

    // $('.footer-selected-pvz').show();
    $('.footer-selected-pvz a').show();
    $('#back-to-list-pvz').show();
    $('input[name="UserClientOrder[address_delivery_pvz]"]').val(region + ', ' + address);
    resetSelectedAddresses();
    //Меняем все "Изменить на Выбрать"
    resetOpenPvzLinks();
    var link_open_pvz_list = '';
    if (current_delivery_company === 'cdek') {
        $('#selected-address-CDEK').html(address);
        link_open_pvz_list = $('#open-pvz-cdek');
        scroll_to = $('#cdek_d');
    } else if (current_delivery_company === 'iml') {
        $('#selected-address-iml').html(address);
        link_open_pvz_list = $('#open-pvz-iml');
        scroll_to = $('#iml_d');
    } else if (current_delivery_company === 'pick_point') {
        $('#selected-address-PickPoint').html(address);
        link_open_pvz_list = $('#open-pvz-pickpoint');
        scroll_to = $('#pick_d');
    }
    var result = link_open_pvz_list.html().replace('Выбрать', 'Изменить');
    link_open_pvz_list.html(result);

    var pvz_code = $(this).find('.item-info').attr('data-pvz-code');
    $('#pvz-code').val(pvz_code);

    var pvz_address = $(this).find('.item-info-address-pvz').html();
    $('#pvz-address').val(pvz_address);
    var info = [];
    info[0] = $(this).find('.addresses-pvz-phone').html();
    info[1] = $(this).find('.addresses-pvz-work-time').html();

    $('#pvz-info').val(info.join(', '));
});

$(document).on('click', '.select-pvz-link', function () {
    var pvz_label = $(this).find('a').attr('data-label');
    $('#pvz-code').val('');
    $('#pvz-address').val('');
    $('#pvz-info').val('');
    $('.ds-name').html(pvz_label + ':');
    $('#addresses-pvz').html('<div> Загрузка списка адресов...</div>');

    var dc = $(this).find('a').attr('data-pvz');
    current_delivery_company = dc;
    var city_name = $('#address-input').val();
    var data = getPvz(dc, city_name);
    initMap(data);
});

$(document).on('keyup', '#address-input', function () {
    window.cityChosen = false;
    setLoader($('#address-loader'));
    $('#address-loader').find('p').html('&nbsp;');
    resetForm();
});

$(document).on('click', '#fake-submit-btn', function () {
    $('#warning-modal').find('.modal-content').find('p').html(warning_message);
    $('#warning-modal').modal({
        fadeDuration: 100
    });
});

$(document).on('input', '#cdek-delivery-address-courier', function () {
    if (9 < $(this).val().length) {
        showPayVariant();
    } else {
        showOrderBtn(false);
    }
});

$(document).on('input', '#courier-delivery-address-courier', function () {
    if (9 < $(this).val().length) {
        showPayVariant();
    } else {
        showOrderBtn(false);
    }
});

$(document).on('input', '#iml-delivery-address-courier', function () {
    if (9 < $(this).val().length) {
        showPayVariant();
    } else {
        showOrderBtn(false);
    }
});

$(document).on('click', 'input[name="UserClientOrder[payment_type]"]', function () {
    showOrderBtn(true);
});
