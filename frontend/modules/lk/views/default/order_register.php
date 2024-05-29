<?php

use common\models\Coupons;
use common\models\UserClientOrder;
use frontend\modules\lk\models\Delivery;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

/**
 * @var UserClientOrder $cart
 * @var Coupons $coupon
 * @var $productsRecently
 * @var Delivery $delivery
 * @var int $delivery_city
 */

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Оформление заказа';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Оформление заказа'];

$this->registerJsFile('/js/order_register.js');
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?apikey=a0918dc8-0aa3-4828-8296-38ab02f88c79&lang=ru_RU');

$addDayToDelivery = $cart->checkAddDayToDelivery();
?>

<section style="padding-top:20px;">
    <div class="container">
        <?php /** @var String $cartInfoMessage */
        if ($cartInfoMessage) { ?>
            <div class="lk-cart-info-message">
                <div class="lk-cart-info-message-title">
                    К сожалению, часть товаров закончилась, мы убрали их из корзины:
                </div>
                <br/>
                <?=$cartInfoMessage?>
            </div>
        <?php } ?>
        <?php $form = ActiveForm::begin() ?>
        <div class="lk__box">
            <h1 class="h3">Оформление заказа</h1>
            <?php if ($cart->hasErrors()) { ?>
                <div class="lk-register-smg">
                    <?php foreach ($cart->getErrors() as $ee)
                        foreach ($ee as $e)
                            echo "<p class='help-block-error'>{$e}</p>" ?>
                </div>
            <?php } ?>
            <p>1. Где и как вы хотите получить заказ?</p>
            <div class="address">
                <div class="address-label">
                    <label for="address-input">Город *</label>
                </div>
                <div class="address-input">
                    <div id="address-loader" style="position: relative"></div>
                    <?php try {
                        echo AutoComplete::widget([
                            'name' => 'UserClientOrder[delivery_city]',
                            'value' => $delivery_city,
                            'options' => [
                                'id' => 'address-input',
                                'placeholder' => "Выберите город из выпадающего списка",
                                'class' => 'form-control',
                                'onBlur' => new JsExpression(" window.resetCity();"),
                                'onFocus' => new JsExpression(" $(this).autocomplete('search');  ")
                            ],
                            'clientOptions' => [
                                'source' =>
                                    new JsExpression("function(request,response) {
                                              var delivery_service = 'cdek';
                                              window.getCityXhr && window.getCityXhr.abort();
                                              window.getCityXhr = $.get({
                                                url: '" . Url::to(['/lk/get-cities']) . "',
                                                data: {
                                                    term: function () { return $('#address-input').val() }
                                                },
                                                success: function(data) {
                                                  $('#address-loader').html('');
                                                  response($.map(data.cities.data, function(item) {
                                                    return {
                                                      label: item.name,
                                                      value: item.name,
                                                      id: item.id,
                                                      src: item,
                                                    }
                                                  }));
                                                }
                                              });
                                            }"),
                                'minLength' => '2',
                                'autoFocus' => true,
                                'classes' => [
                                    "ui-autocomplete" => "ui-autocomplete-simple-complete"
                                ],
                                'select' => new JsExpression("function(e, ui) {
                                          setTimeout(function(){
                                            $('#cdek-city-code').val(ui.item.id);
                                            window.refreshCityCodes();
                                          }, 50);
                                            console.log(ui.item.id, ui.item); 
                                            window.selectCity(ui.item);
                                            window.resetForm();
                                        }"),
                            ],
                        ]);
                    } catch (Exception $e) {
                        Yii::error($e->getMessage(), 'error');
                    } ?>

                </div>
            </div>
            <div class="hiddens" style="display:none; flex-direction: column;">
                <script>
                    window.city_codes = <?=json_encode($city_codes)?>;
                </script>
                <input type="text" id="delivery-type" placeholder="delivery_type"
                       name="UserClientOrder[delivery_type]"/>
                <input type="text" id="delivery-service" placeholder="delivery_service"
                       name="UserClientOrder[delivery_service]"/>
                <input type="text" id="delivery-region" placeholder="delivery_region"
                       name="UserClientOrder[delivery_region]"/>
                <input type="text" id="pvz-code" placeholder="pvz_code" name="UserClientOrder[pvz_code]"/>
                <input type="text" id="pvz-address" placeholder="pvz_address"
                       name="UserClientOrder[address_delivery_pvz]"/>
                <input type="text" id="pvz-info" placeholder="pvz_info" name="UserClientOrder[pvz_info]"/>
                <input type="text" id="cdek-city-code" placeholder="cdek_city_code"
                       name="UserClientOrder[cdek_city_code]" <?= ($delivery_city_id ? 'value="' . $delivery_city_id . '"' : '') ?>/>
                <input type="text" id="pp-city-code" placeholder="pp_city_code"
                       name="UserClientOrder[pp_city_code]"/>
                <input type="text" id="iml-city-code" placeholder="iml_city_code"
                       name="UserClientOrder[iml_city_code]"/>
                <input type="text" id="delivery-period-max" placeholder="delivery_period_max"
                       name="UserClientOrder[delivery_period_max]"/>
                <input type="text" id="delivery-tariff-name" placeholder="delivery_tariff_name"
                       name="UserClientOrder[delivery_tariff_name]"/>
                <input type="text" id="courier-time-interval" placeholder="courier_time_inteval"
                       name="UserClientOrder[courier_time_interval]"/>
            </div>

            <div id="delivery-form" class="delivery-form" data-add-day="<?= $addDayToDelivery ?>">
                <div class="delivery-form-inner">
                    <div class="delivery-block">
                        <!-- блок-кнопка Самовывоз-->
                        <div id="d-sam" class="delivery-item">
                            <div class="d-svg">
                                <svg width="38" height="32" viewBox="0 0 38 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M36.2535 30.3171H35.6203V13.8032C37.0501 12.8831 38 11.2787 38 9.45562C38 9.35876 37.9747 9.26354 37.9267 9.17945L33.5097 1.44971C33.1995 0.906952 32.6184 0.569702 31.9933 0.569702H6.00675C5.3816 0.569702 4.80054 0.906878 4.49031 1.44971L0.0733281 9.17945C0.0253086 9.26354 0 9.35884 0 9.45562C0 11.2787 0.949851 12.8831 2.37975 13.8032V30.3171H1.74652C1.4391 30.3171 1.18988 30.5663 1.18988 30.8737C1.18988 31.1812 1.4391 31.4304 1.74652 31.4304H36.2535C36.561 31.4304 36.8101 31.1812 36.8101 30.8737C36.8101 30.5663 36.5609 30.3171 36.2535 30.3171ZM1.15165 10.0123H2.93617C3.24358 10.0123 3.49281 9.76303 3.49281 9.45562C3.49281 9.1482 3.24358 8.89898 2.93617 8.89898H1.51584L5.45693 2.00205C5.56945 1.80522 5.78008 1.68298 6.00675 1.68298H31.9933C32.2199 1.68298 32.4306 1.80522 32.5431 2.00205L36.4841 8.89898H35.0635C34.756 8.89898 34.5068 9.1482 34.5068 9.45562C34.5068 9.76303 34.756 10.0123 35.0635 10.0123H36.8483C36.5761 11.9853 34.8791 13.5098 32.8324 13.5098C30.7859 13.5098 29.0897 11.9851 28.8174 10.0123H32.6839C32.9914 10.0123 33.2406 9.76303 33.2406 9.45562C33.2406 9.1482 32.9914 8.89898 32.6839 8.89898H5.31636C5.00895 8.89898 4.75972 9.1482 4.75972 9.45562C4.75972 9.76303 5.00895 10.0123 5.31636 10.0123H9.18249C8.91026 11.9851 7.21399 13.5098 5.16748 13.5098C3.12082 13.5098 1.42381 11.9853 1.15165 10.0123ZM27.6259 10.0123C27.3536 11.9851 25.6573 13.5098 23.6108 13.5098C21.5643 13.5098 19.8681 11.9851 19.5958 10.0123H27.6259ZM18.4042 10.0123C18.1319 11.9851 16.4357 13.5098 14.3892 13.5098C12.3427 13.5098 10.6464 11.9851 10.3741 10.0123H18.4042ZM31.5321 30.3172H25.5061V28.4557H31.5321V30.3172ZM31.5321 27.3424H25.5061V17.1517H31.5321V27.3424ZM34.507 30.3172H32.6455V16.595C32.6455 16.2876 32.3963 16.0384 32.0888 16.0384H24.9495C24.642 16.0384 24.3929 16.2876 24.3929 16.595V30.3171H3.49303V14.3431C4.01857 14.5236 4.58145 14.6231 5.16748 14.6231C7.17844 14.6231 8.92473 13.4686 9.77832 11.7874C10.6319 13.4685 12.3782 14.6231 14.3892 14.6231C16.4001 14.6231 18.1464 13.4686 19 11.7874C19.8536 13.4685 21.5999 14.6231 23.6108 14.6231C25.6218 14.6231 27.3681 13.4686 28.2217 11.7874C29.0753 13.4685 30.8216 14.6231 32.8325 14.6231C33.4186 14.6231 33.9814 14.5236 34.507 14.3431V30.3172Z"
                                          fill="#363636"/>
                                </svg>

                            </div>
                            <div class="d-info">
                                <span class="d-title">Самовывоз из пунктов выдачи</span>
                                <p class="d-desk">Из партнерских пунктов выдачи и постматов</p>
                                <span id="d-desk-sam-both" class="d-desk-both">Загрузка данных...</span>
                            </div>
                            <div>
                                <div class="delivery-circle"></div>
                            </div>
                        </div>
                        <!-- блок-кнопка Курьер-->
                        <div id="d-dostavka" class="delivery-item">
                            <div class="d-svg">
                                <svg width="45" height="29" viewBox="0 0 45 29" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M41.8498 12.3347L40.6845 7.6734C41.0031 7.60573 41.2421 7.32307 41.2421 6.98443V6.23288C41.2421 4.6011 39.9146 3.2736 38.2829 3.2736H32.975V1.72338C32.975 0.920415 32.3218 0.267212 31.5188 0.267212H4.46238C3.65941 0.267212 3.00621 0.920415 3.00621 1.72338V14.5C3.00621 14.8891 3.32165 15.2047 3.71083 15.2047C4.09992 15.2047 4.41545 14.8892 4.41545 14.5V1.72338C4.41545 1.69746 4.43645 1.67645 4.46238 1.67645H31.5187C31.5446 1.67645 31.5657 1.69746 31.5657 1.72338V14.5002C31.5657 14.8893 31.8811 15.2048 32.2703 15.2048C32.6594 15.2048 32.9749 14.8894 32.9749 14.5002V13.7015H41.2866C41.2873 13.7015 41.2878 13.7016 41.2885 13.7016C41.2892 13.7016 41.2898 13.7016 41.2904 13.7016C42.3132 13.7023 43.1816 14.3736 43.4799 15.2986H41.289C40.8999 15.2986 40.5844 15.6141 40.5844 16.0032V17.5063C40.5844 18.7237 41.5747 19.7141 42.7921 19.7141H43.5907V22.8142H41.7483C41.1431 21.0667 39.4819 19.8079 37.5311 19.8079C35.5804 19.8079 33.9191 21.0667 33.314 22.8142H32.9747V17.5063C32.9747 17.1172 32.6593 16.8016 32.2701 16.8016C31.881 16.8016 31.5655 17.1171 31.5655 17.5063V22.8141H16.9468C16.3416 21.0665 14.6804 19.8078 12.7296 19.8078C10.7789 19.8078 9.1176 21.0665 8.51247 22.8141H4.46238C4.43645 22.8141 4.41545 22.7931 4.41545 22.7671V21.217H7.46868C7.85777 21.217 8.1733 20.9016 8.1733 20.5124C8.1733 20.1232 7.85786 19.8078 7.46868 19.8078H0.704619C0.315527 19.8078 0 20.1232 0 20.5124C0 20.9016 0.315439 21.217 0.704619 21.217H3.0063V22.7671C3.0063 23.5701 3.6595 24.2233 4.46247 24.2233H8.26849C8.26831 24.2389 8.26726 24.2545 8.26726 24.2702C8.26726 26.7308 10.2691 28.7326 12.7296 28.7326C15.1901 28.7326 17.192 26.7308 17.192 24.2702C17.192 24.2544 17.191 24.2389 17.1908 24.2233H33.07C33.0698 24.2389 33.0688 24.2545 33.0688 24.2702C33.0688 26.7308 35.0706 28.7326 37.5311 28.7326C39.9916 28.7326 41.9935 26.7308 41.9935 24.2702C41.9935 24.2544 41.9925 24.2389 41.9923 24.2233H44.2953C44.6844 24.2233 44.9999 23.9079 44.9999 23.5187V16.0031C45 14.1475 43.6309 12.6058 41.8498 12.3347ZM32.975 4.68275H38.2829C39.1376 4.68275 39.833 5.37814 39.833 6.23288V6.27981H32.975V4.68275ZM32.975 12.2923V7.68896H39.2359L40.3867 12.2923H32.975ZM12.7296 27.3236C11.0461 27.3236 9.67641 25.954 9.67641 24.2704C9.67641 22.5868 11.0461 21.2172 12.7296 21.2172C14.4132 21.2172 15.7829 22.5868 15.7829 24.2704C15.7829 25.954 14.4132 27.3236 12.7296 27.3236ZM37.5313 27.3236C35.8478 27.3236 34.4781 25.954 34.4781 24.2704C34.4781 22.5868 35.8478 21.2172 37.5313 21.2172C39.2149 21.2172 40.5845 22.5868 40.5845 24.2704C40.5845 25.954 39.2149 27.3236 37.5313 27.3236ZM43.5908 18.3048H42.7923C42.3519 18.3048 41.9937 17.9466 41.9937 17.5063V16.7077H43.5908V18.3048H43.5908Z"
                                          fill="#363636"/>
                                </svg>
                            </div>
                            <div class="d-info">
                                <span class="d-title">Курьер</span>
                                <p class="d-desk">Доставит до Ваших дверей</p>
                                <p id="d-desk-dost-both" class="d-desk-both">Загрузка данных...</p>
                            </div>
                            <div>
                                <div class="delivery-circle"></div>
                            </div>
                        </div>
                        <!-- блок-кнопка Самовывоз со склада-->
                        <?php
                        //echo '<pre>';print_r($delivery_city_id);echo '</pre>';
                        //echo '<pre>';print_r(Yii::$app->keyStorageApp->get('courier_native_city_id'));echo '</pre>';
                        ?>
                        <div id="d-sam-wharehouse" class="delivery-item" <?=$delivery_city_id != Yii::$app->keyStorageApp->get('courier_native_city_id') ? 'style="display:none;"' : ''?>>
                            <div class="d-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="38" viewBox="0 0 40 38"
                                     fill="none">
                                    <g clip-path="url(#clip0)">
                                        <path d="M0.847656 6.36175C0.847656 5.97204 1.1636 5.6561 1.55331 5.6561C1.94302 5.6561 2.25896 5.97204 2.25896 6.36175V36.3009H37.971V6.43584C37.971 6.04613 38.2869 5.73019 38.6766 5.73019C39.0663 5.73019 39.3823 6.04613 39.3823 6.43584V37.0062C39.3823 37.3959 39.0663 37.7119 38.6766 37.7119H1.55331C1.1636 37.7119 0.847656 37.3959 0.847656 37.0062V6.36143V6.36175Z"
                                              fill="#363636"/>
                                        <path d="M21.8953 7.70793V33.2156L26.9275 33.2149V24.657C26.9275 24.2673 27.2435 23.9514 27.6332 23.9514H32.9038V19.254H27.6332C27.2435 19.254 26.9275 18.938 26.9275 18.5483V13.1106C26.9275 12.7209 27.2435 12.405 27.6332 12.405H33.5456V7.70761H21.8953V7.70793ZM20.484 33.9184V7.00228C20.484 6.61257 20.7999 6.29663 21.1896 6.29663H34.2512C34.6409 6.29663 34.9569 6.61257 34.9569 7.00228V13.1106C34.9569 13.5004 34.6409 13.8163 34.2512 13.8163H28.3388V17.8427H33.6094C33.9991 17.8427 34.3151 18.1586 34.3151 18.5483V24.657C34.3151 25.0467 33.9991 25.3627 33.6094 25.3627H28.3388V33.9177H28.3359C28.3359 34.3058 28.0213 34.6205 27.6332 34.6205L21.2525 34.6211L21.19 34.624C20.8002 34.624 20.4843 34.3081 20.4843 33.9184H20.484ZM18.061 33.2127V7.70793H6.50505V12.4053H12.3231C12.7128 12.4053 13.0288 12.7213 13.0288 13.111V33.213H18.0614L18.061 33.2127ZM19.4723 7.00228V33.9184C19.4723 34.3081 19.1564 34.624 18.7667 34.624H12.3231C11.9334 34.624 11.6175 34.3081 11.6175 33.9184V13.8163H5.7994C5.40969 13.8163 5.09375 13.5004 5.09375 13.1106V7.00228C5.09375 6.61257 5.40969 6.29663 5.7994 6.29663H18.7667C19.1564 6.29663 19.4723 6.61257 19.4723 7.00228Z"
                                              fill="#363636"/>
                                        <path d="M17.1651 0.0561313C17.745 0.0561313 18.2711 0.291883 18.6521 0.672934H18.6541C19.0351 1.05399 19.2709 1.58066 19.2709 2.16186C19.2709 2.74306 19.0351 3.26973 18.6541 3.65078L18.621 3.68093C18.2425 4.04434 17.7293 4.26758 17.1648 4.26758C16.5849 4.26758 16.0589 4.03183 15.6778 3.65078H15.6759C15.2948 3.26973 15.0591 2.74306 15.0591 2.16186C15.0591 1.58194 15.2948 1.05591 15.6759 0.674858L15.6778 0.672934C16.0589 0.291883 16.5849 0.0561313 17.1648 0.0561313H17.1651ZM17.907 1.42028C17.7178 1.23136 17.4554 1.11461 17.1651 1.11461C16.8752 1.11461 16.6128 1.23136 16.4232 1.42028C16.2343 1.60952 16.1176 1.8719 16.1176 2.16218C16.1176 2.45149 16.235 2.71355 16.4242 2.90279L16.4232 2.90375C16.6125 3.09267 16.8748 3.20943 17.1651 3.20943C17.4442 3.20943 17.6976 3.10133 17.8849 2.92492L17.9057 2.90279C18.095 2.71355 18.2124 2.45149 18.2124 2.16218C18.2124 1.87286 18.095 1.61081 17.9057 1.42124L17.9067 1.42028H17.907ZM21.9687 0C22.5486 0 23.0746 0.235751 23.4557 0.616803H23.4576C23.8387 0.997854 24.0744 1.52453 24.0744 2.10572C24.0744 2.68724 23.8387 3.2136 23.4576 3.59465L23.4246 3.6248C23.0461 3.98821 22.5329 4.21145 21.9684 4.21145C21.3884 4.21145 20.8624 3.9757 20.4814 3.59465H20.4794C20.0984 3.2136 19.8626 2.68692 19.8626 2.10572C19.8626 1.52453 20.0984 0.997854 20.4794 0.616803L20.5125 0.586652C20.891 0.223563 21.4042 0 21.9687 0ZM22.7106 1.36415C22.5213 1.17523 22.259 1.05848 21.9687 1.05848C21.6896 1.05848 21.4362 1.16657 21.2489 1.34298L21.2281 1.36511C21.0388 1.55436 20.9214 1.81641 20.9214 2.10572C20.9214 2.39504 21.0388 2.65709 21.2281 2.84666L21.2271 2.84762C21.4164 3.03654 21.6787 3.15329 21.969 3.15329C22.2481 3.15329 22.5014 3.0452 22.6888 2.86879L22.7096 2.84666C22.8989 2.65742 23.0163 2.39536 23.0163 2.10572C23.0163 1.81641 22.8989 1.55436 22.7096 1.36511L22.7106 1.36415Z"
                                              fill="#363636"/>
                                        <path d="M33.1115 1.60663H34.3807C34.6729 1.60663 34.9099 1.84366 34.9099 2.13586C34.9099 2.42807 34.6729 2.6651 34.3807 2.6651H33.1115V3.17189H34.4567C34.7489 3.17189 34.9859 3.40892 34.9859 3.70113C34.9859 3.99333 34.7489 4.23036 34.4567 4.23036H32.5826C32.2904 4.23036 32.0533 3.99333 32.0533 3.70113V0.570925C32.0533 0.278721 32.2904 0.041687 32.5826 0.041687H34.4448C34.737 0.041687 34.9741 0.278721 34.9741 0.570925C34.9741 0.863128 34.737 1.10016 34.4448 1.10016H33.1115V1.60663ZM35.7769 0.570925C35.7769 0.278721 36.0139 0.041687 36.3061 0.041687C36.5984 0.041687 36.8354 0.278721 36.8354 0.570925V3.17189H38.5556C38.8478 3.17189 39.0848 3.40892 39.0848 3.70113C39.0848 3.99333 38.8478 4.23036 38.5556 4.23036H36.3061C36.0139 4.23036 35.7769 3.99333 35.7769 3.70113V0.570925ZM29.3414 1.60663H30.6109C30.9031 1.60663 31.1401 1.84366 31.1401 2.13586C31.1401 2.42807 30.9031 2.6651 30.6109 2.6651H29.3414V3.17189H30.6866C30.9788 3.17189 31.2158 3.40892 31.2158 3.70113C31.2158 3.99333 30.9788 4.23036 30.6866 4.23036H28.8125C28.5203 4.23036 28.2832 3.99333 28.2832 3.70113V0.570925C28.2832 0.278721 28.5203 0.041687 28.8125 0.041687H30.6747C30.9669 0.041687 31.204 0.278721 31.204 0.570925C31.204 0.863128 30.9669 1.10016 30.6747 1.10016H29.3414V1.60663ZM25.7211 1.60663H26.9653C27.2575 1.60663 27.4945 1.84366 27.4945 2.13586C27.4945 2.42807 27.2575 2.6651 26.9653 2.6651H25.7211V3.70113C25.7211 3.99333 25.484 4.23036 25.1918 4.23036C24.8996 4.23036 24.6626 3.99333 24.6626 3.70113V0.570925C24.6626 0.278721 24.8996 0.041687 25.1918 0.041687H26.9248C27.2171 0.041687 27.4541 0.278721 27.4541 0.570925C27.4541 0.863128 27.2171 1.10016 26.9248 1.10016H25.7211V1.60663Z"
                                              fill="#363636"/>
                                        <path d="M0.529238 1.09601C0.237034 1.09601 0 0.858978 0 0.566774C0 0.274571 0.237034 0.0375366 0.529238 0.0375366H3.11673C3.40893 0.0375366 3.64597 0.274571 3.64597 0.566774C3.64597 0.858978 3.40893 1.09601 3.11673 1.09601H2.34917V3.70082C2.34917 3.99303 2.11214 4.23006 1.81994 4.23006C1.52773 4.23006 1.2907 3.99303 1.2907 3.70082V1.09601H0.528917H0.529238ZM13.5094 3.70082C13.5094 3.99303 13.2723 4.23006 12.9801 4.23006C12.6879 4.23006 12.4509 3.99303 12.4509 3.70082V1.09601H11.6888C11.3966 1.09601 11.1595 0.858978 11.1595 0.566774C11.1595 0.274571 11.3966 0.0375366 11.6888 0.0375366H14.2763C14.5685 0.0375366 14.8055 0.274571 14.8055 0.566774C14.8055 0.858978 14.5685 1.09601 14.2763 1.09601H13.509V3.70082H13.5094ZM9.10898 3.70082C9.10898 3.99303 8.87195 4.23006 8.57975 4.23006C8.28754 4.23006 8.05051 3.99303 8.05051 3.70082V1.09601H7.28873C6.99652 1.09601 6.75949 0.858978 6.75949 0.566774C6.75949 0.274571 6.99652 0.0375366 7.28873 0.0375366H9.87654C10.1687 0.0375366 10.4058 0.274571 10.4058 0.566774C10.4058 0.858978 10.1687 1.09601 9.87654 1.09601H9.10898V3.70082ZM5.07587 2.18945H5.25966L5.16632 1.97455L5.07587 2.18945ZM5.71898 3.24793H4.62971L4.35258 3.90578C4.23968 4.17393 3.93047 4.29966 3.66233 4.18676C3.39418 4.07386 3.26844 3.76465 3.38135 3.49651L4.67365 0.429814C4.78655 0.161667 5.09576 0.0359329 5.36391 0.148837C5.4983 0.205289 5.59677 0.311137 5.64745 0.436229L6.97375 3.49234C7.0889 3.75952 6.96605 4.06969 6.69887 4.18484C6.43168 4.29999 6.12152 4.17714 6.00637 3.90995L5.7193 3.24793H5.71898Z"
                                              fill="#363636"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M5.39336 2.57178L5.10725 1.50336L4.78906 2.57178H5.39336Z"
                                              fill="white"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0">
                                            <rect width="39.3824" height="37.7122" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <div class="d-info">
                                <span class="d-title">Самовывоз со склада</span>
                                <p class="d-desk">
                                    Самовывоз из магазина TATTOOFEEL.RU<br>
                                    Забор возможен завтра, после 12:00<br>
                                    Электродная ул., дом 2, стр. 33<br>
                                    в тату-студии Barn house tattoo<br>
                                    Время работы: 11:00 - 19:00<br>
                                </p>
                            </div>
                            <div>
                                <div class="delivery-circle"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Доставка-->
                    <div class="delivery-sam">
                        <div class="delivery-info">Нет доставки в выбранный населенный пункт</div>

                        <!--СДЭК-->
                        <div class="delivery-sam-item" id="cdek_d">
                            <div class="delivery-sam-item-title">
                                <label class="delivery-sam-radio">
                                    <input name="UserClientOrder[delivery_service]" type="radio"
                                           value="<?= $delivery::DELIVERY_CDEK; ?>" data-owner-id="CDEK"/><span
                                            class="s-span"></span> </label>
                                <span class="delivery-sam-title">СДЭК</span>
                            </div>
                            <!--Список тарифов-->
                            <div class="delivery-sam-item-data">
                                <div id="delivery-cdek-loader"></div>
                                <div class="delivery-form-inner-col-2">
                                    <div id="cdek">
                                        <div class="delivery-form-tab">
                                            <!--                                                    <div class="header-tariffs">-->
                                            <!--                                                        <div class="header-tariffs-text">-->
                                            <!--                                                            <p>тип</p>-->
                                            <!--                                                            <p>срок</p>-->
                                            <!--                                                            <p>стоимость</p>-->
                                            <!--                                                        </div>-->
                                            <!--                                                    </div>-->
                                            <!-- Курьер-->
                                            <div id="delivery-form-tab-courier">
                                                <?php foreach ($delivery->getCdekTariffList($delivery::CDEK_SD_DELIVERY_TYPE) as $tariff_code => $tariff_name): ?>
                                                    <div class="delivery-params">
                                                        <div class="delivery-name">
                                                            <label class="checkbox-f">
                                                                <input name="delivery_cdek_rate"
                                                                       value="<?= $tariff_code; ?>"
                                                                       type="radio"/><i></i>
                                                                <span><?= $tariff_name; ?></span></label>
                                                        </div>
                                                        <div class="delivery-timing" data-period-max="0">
                                                            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                                                            <span class="sr-only">Загрузка...</span>
                                                        </div>
                                                        <div data-delivery-cost="0" class="delivery-cost">
                                                            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                                                            <span class="sr-only">Загрузка...</span>
                                                        </div>
                                                    </div>
                                                    <?php $counter++ ?>
                                                <?php endforeach; ?>
                                                <div class="delivery-address">
                                                    <label>Адрес доставки *:</label>
                                                    <textarea
                                                            onkeyup="$('#iml-delivery-address-courier').val($(this).val());$('#courier-delivery-address-courier').val($(this).val());"
                                                            id="cdek-delivery-address-courier"
                                                            name="UserClientOrder[address_delivery]"
                                                            class="placeholder"
                                                            placeholder="Город, улица, дом, квартира/офис"><?=
                                                        !Yii::$app->client->isGuest && Yii::$app->client->identity->profile->address_delivery ?
                                                            Yii::$app->client->identity->profile->address_delivery : null
                                                        ?></textarea>
                                                </div>
                                            </div>
                                            <!-- Самовывоз-->
                                            <div id="delivery-form-tab-pvz">
                                                <?php foreach ($delivery->getCdekTariffList($delivery::CDEK_SS_DELIVERY_TYPE) as $tariff_code => $tariff_name): ?>
                                                    <div class="delivery-params">
                                                        <div class="delivery-name">
                                                            <label class="checkbox-f">
                                                                <input name="delivery_cdek_rate"
                                                                       value="<?= $tariff_code; ?>"
                                                                       type="radio"/><i></i>
                                                                <span><?= $tariff_name; ?></span></label>
                                                        </div>
                                                        <div class="delivery-timing" data-period-max="0">
                                                            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                                                            <span class="sr-only">Загрузка...</span>
                                                        </div>
                                                        <div data-delivery-cost="0" class="delivery-cost">
                                                            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                                                            <span class="sr-only">Загрузка...</span>
                                                        </div>
                                                    </div>
                                                    <?php $counter++ ?>
                                                <?php endforeach; ?>
                                                <div class="address-pvz" id="selected-address-CDEK"></div>
                                                <div class="select-pvz-link">
                                                    <a id="open-pvz-cdek" data-label="СДЭК" data-pvz="cdek"
                                                       href="#pvz-modal"
                                                       rel="modal:open">
                                                        Выбрать пункт выдачи
                                                    </a>
                                                    <div id="pvz-cdek-not-found"></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!--Наш Курьер-->
                        <div class="delivery-sam-item" id="courier_d"
                             data-native_city_id="<?= Yii::$app->keyStorageApp->get('courier_native_city_id') ?>">
                            <div class="delivery-sam-item-title">
                                <label class="delivery-sam-radio">
                                    <input name="UserClientOrder[delivery_service]" type="radio"
                                           value="<?= $delivery::DELIVERY_COURIER; ?>" data-owner-id="COURIER"/><span
                                            class="s-span"></span> </label>
                                <span class="delivery-sam-title">Курьер</span>
                            </div>
                            <!--Список тарифов-->
                            <div class="delivery-sam-item-data">

                                <div class="d-sam-item-data-wrapper" style="margin-bottom: 10px;">
                                    <?= Yii::$app->keyStorageApp->get('courier_comments') ?>
                                </div>

                                <div id="delivery-courier-loader"></div>
                                <div class="delivery-form-inner-col-2">
                                    <div id="courier">
                                        <div class="delivery-form-tab">
                                            <!-- Курьер-->
                                            <div id="delivery-form-tab-courier">

                                                <?php
                                                $hide_hours = (int)Yii::$app->keyStorageApp->get('courier_hours_hide_today');
                                                if (!$addDayToDelivery && intval(date("H")) < $hide_hours): // после 15:00 интервал становится недоступен
                                                    ?>
                                                    <script>
                                                        setTimeout(function () {
                                                                if ($('.delivery-params:contains(Сегодня срочно)').length > 0 && $('.delivery-params:contains(Сегодня срочно)').parent().find('.delivery-params span:contains(Завтра)').length > 0) {
                                                                    $('.delivery-params:contains(Сегодня срочно)').parent().find('.delivery-params span:contains(Завтра)')[0].click();
                                                                    //$('.delivery-params:contains(Сегодня срочно)').remove();
                                                                    $('.delivery-params:contains(Сегодня срочно)').addClass('interval-disabled');
                                                                    $('.delivery-params:contains(Сегодня срочно) input').prop('disabled', true);
                                                                }
                                                            }, <?php
                                                            //вычисляем сколько секунд до закрытия "окна" сегодняшней доставки в секундах
                                                            $secs = strtotime("Today " . $hide_hours . ":00") - time();
                                                            echo($secs * 1000);
                                                            ?>);
                                                    </script>
                                                <?php endif; ?>

                                                <?php $timeInterval = 'today_' . Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_3') ?>
                                                <div class="delivery-params odd<?php if ($addDayToDelivery || intval(date("H")) >= $hide_hours) echo ' interval-disabled'; ?>">
                                                    <div class="delivery-name">
                                                        <label class="checkbox-f">
                                                            <input name="delivery_courier_rate"
                                                                   value="<?= $timeInterval ?>"
                                                                   type="radio"<?php if ($addDayToDelivery || intval(date("H")) >= $hide_hours) echo ' disabled'; ?> /><i></i>
                                                            <span>Сегодня срочно</span></label>
                                                    </div>
                                                    <div data-period-max="0"
                                                         class="delivery-timing"><?= Yii::$app->keyStorageApp->get('courier_time_interval_3') ?></div>
                                                    <div data-delivery-cost="<?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>"
                                                         class="delivery-cost"><?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>
                                                        <span class="rub">i</span></div>
                                                </div>

                                                <?php $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_1') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_1') ?>
                                                <div class="delivery-params even">
                                                    <div class="delivery-name">
                                                        <label class="checkbox-f">
                                                            <input name="delivery_courier_rate"
                                                                   value="<?= $timeInterval ?>"
                                                                   type="radio"/><i></i>
                                                            <span>Завтра</span></label>
                                                    </div>
                                                    <div data-period-max="1"
                                                         class="delivery-timing"><?= Yii::$app->keyStorageApp->get('courier_time_interval_1') ?></div>
                                                    <div data-delivery-cost="<?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>"
                                                         class="delivery-cost"><?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>
                                                        <span class="rub">i</span></div>
                                                </div>

                                                <?php $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_2') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_2') ?>
                                                <div class="delivery-params odd">
                                                    <div class="delivery-name">
                                                        <label class="checkbox-f">
                                                            <input name="delivery_courier_rate"
                                                                   value="<?= $timeInterval ?>"
                                                                   type="radio"/><i></i>
                                                            <span>Завтра</span></label>
                                                    </div>
                                                    <div data-period-max="1"
                                                         class="delivery-timing"><?= Yii::$app->keyStorageApp->get('courier_time_interval_2') ?></div>
                                                    <div data-delivery-cost="<?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>"
                                                         class="delivery-cost"><?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>
                                                        <span class="rub">i</span></div>
                                                </div>

                                                <?php $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_3') ?>
                                                <div class="delivery-params even">
                                                    <div class="delivery-name">
                                                        <label class="checkbox-f">
                                                            <input name="delivery_courier_rate"
                                                                   value="<?= $timeInterval ?>"
                                                                   type="radio"/><i></i>
                                                            <span>Завтра</span></label>
                                                    </div>
                                                    <div data-period-max="1"
                                                         class="delivery-timing"><?= Yii::$app->keyStorageApp->get('courier_time_interval_3') ?></div>
                                                    <div data-delivery-cost="<?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>"
                                                         class="delivery-cost"><?= ($delivery->getCourierClientPrice($timeInterval)) / 100 ?>
                                                        <span class="rub">i</span></div>
                                                </div>

                                                <div class="delivery-address">
                                                    <label>Адрес доставки и ближайшая станция метро *:</label>
                                                    <textarea
                                                            onkeyup="$('#cdek-delivery-address-courier').val($(this).val());$('#iml-delivery-address-courier').val($(this).val());"
                                                            id="courier-delivery-address-courier"
                                                            name="UserClientOrder[address_delivery]"
                                                            class="placeholder"
                                                            placeholder="Город, улица, дом, квартира/офис"><?=
                                                        !Yii::$app->client->isGuest && Yii::$app->client->identity->profile->address_delivery ?
                                                            Yii::$app->client->identity->profile->address_delivery : null
                                                        ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--PickPoint-->
                        <!--div class="delivery-sam-item" id="pick_d" data-tariff-name="">
                                    <div class="delivery-sam-item-title">
                                        <label class="delivery-sam-radio">
                                            <input name="UserClientOrder[delivery_service]" type="radio"
                                                   value="<?= $delivery::DELIVERY_PICK_POINT; ?>"
                                                   data-owner-id="PinkPoint"/><span></span> </label>
                                        <span class="delivery-sam-title">PickPoint - Постаматы</span>
                                    </div>
                                    <div class="delivery-sam-item-data">
                                        <div id="delivery-pp-loader"></div>
                                        <div class="d-sam-item-data-wrapper">
                                            <span id="pp-delivery-date"
                                                  class="delivery-sam-item-data-date" data-period-max="0">Срок: -</span>
                                            <span id="pp-delivery-price" data-delivery-cost="0"
                                                  class="delivery-sam-item-data-price">Стоимость: -</span>
                                            <div class="address-pvz" id="selected-address-PickPoint"></div>
                                            <div class="select-pvz-link">
                                                <a id="open-pvz-pickpoint" data-pvz="pick_point" data-label="Pick Point"
                                                   href="#pvz-modal" rel="modal:open">
                                                    Выбрать пункт выдачи
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div-->

                        <!--IML-->
                        <div style="display: none;">
                            <div class="delivery-sam-item" id="iml_d">
                                <div class="delivery-sam-item-title">
                                    <label class="delivery-sam-radio">
                                        <input name="UserClientOrder[delivery_service]" type="radio"
                                               value="<?= $delivery::DELIVERY_IML; ?>"
                                               data-owner-id="IML"/><span></span> </label>
                                    <span class="delivery-sam-title">IML</span>
                                </div>
                                <div id="delivery-iml-loader"></div>
                                <div class="delivery-form-tab">
                                    <!-- IML Самовывоз-->
                                    <div id="iml-delivery-form-tab-pvz" data-tariff-name="">
                                        <div class="delivery-sam-item-data">
                                            <div class="d-sam-item-data-wrapper">
                                                    <span id="iml-delivery-date-pvz" class="delivery-sam-item-data-date"
                                                          data-period-max="0">Срок: -</span>
                                                <span id="iml-delivery-price-pvz" data-delivery-cost="0"
                                                      class="delivery-sam-item-data-price">Стоимость: -</span>
                                                <div class="address-pvz" id="selected-address-iml"></div>
                                                <div class="select-pvz-link">
                                                    <a id="open-pvz-iml" data-pvz="iml" data-label="IML"
                                                       href="#pvz-modal" rel="modal:open">
                                                        Выбрать пункт выдачи
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- IML Курьер-->
                                    <div id="iml-delivery-form-tab-courier" data-tariff-name="">
                                        <div class="delivery-sam-item-data">
                                            <div class="d-sam-item-data-wrapper">
                                            <span id="iml-delivery-date-courier"
                                                  class="delivery-sam-item-data-date" data-period-max="0">Срок: -</span>
                                                <span id="iml-delivery-price-courier" data-delivery-cost="0"
                                                      class="delivery-sam-item-data-price">Стоимость: -</span>
                                            </div>
                                        </div>
                                        <div class="delivery-address">
                                            <label>Адрес доставки *:</label>
                                            <textarea
                                                    onkeyup="$('#cdek-delivery-address-courier').val($(this).val());$('#courier-delivery-address-courier').val($(this).val());"
                                                    id="iml-delivery-address-courier"
                                                    name="UserClientOrder[address_delivery]"
                                                    class="placeholder delivery-address-courier"
                                                    placeholder="Город, улица, дом, квартира/офис"><?=
                                                !Yii::$app->client->isGuest && Yii::$app->client->identity->profile->address_delivery ?
                                                    Yii::$app->client->identity->profile->address_delivery : null
                                                ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="final-block">
                        <!--Варианты оплаты-->
                        <div class="left-side-final-block">
                            <div class="pay__variant">
                                <p>2. Как вам будет удобнее оплатить заказ?</p>


                                <?php
                                $is_checked = false;
                                foreach ($paymentTypes as $key => $item):?>
                                    <?php
                                    if (4 == $item->id) { // не показывать способ оплаты "перевод с карты на карту"
                                        //if (4 == $item->id && (!Yii::$app->client->identity->profile->sale_ms_id || Yii::$app->client->identity->profile->sale_ms_id == 'Скидка 1')) {
                                        continue;
                                    }

                                    if ((1 == $item->id && Yii::$app->client->identity->profile->hide_cash) || (2 == $item->id && Yii::$app->client->identity->profile->hide_card)) {
                                        continue;
                                    }

                                    ?>
                                    <label style="display: block;"
                                           class="pay__variant-item  <?php if (!$key) echo 'pay__check'; ?>">
                                        <?php
                                        if (1 == $item->id) {
                                            $img = '/img/nalik.svg';
                                            $title = "Наличными";
                                            $subtitle = "При получении товара";
                                        } elseif (2 == $item->id) {
                                            $img = '/img/visa_card.svg';
                                            $title = "VISA, MasterCard, МИР";
                                            $subtitle = "На сайте, после подтверждения";
                                        } elseif (3 == $item->id) {
                                            $img = '/img/checking.svg';
                                            $title = "Расчётный счёт";
                                            $subtitle = "Отсрочка или предоплата";
                                        } elseif (4 == $item->id) {
                                            $img = '/img/card2card.svg';
                                            $title = "Перевод";
                                            $subtitle = "С карты на карту";
                                        }
                                        ?>
                                        <div class="pay__variant-img" style="background-image: url(<?= $img ?>)"></div>
                                        <div class="pay__variant-item-wrap">
                                            <div class="pay__variant-item-left">
                                                <span class="pay__variant-item-left-title"><?= $title ?></span>
                                                <span class="pay__variant-item-left-desk"><?= $subtitle ?></span>
                                            </div>
                                        </div>
                                        <div class="pay__variant-item-right">
                                            <span></span>
                                        </div>
                                        <input style="opacity: 0; position: absolute;"
                                               name="UserClientOrder[payment_type]" type="radio"
                                               value="<?= $item->id ?>" <?php if (!$is_checked) echo 'checked'; ?> />
                                        <span class="balon"></span>
                                    </label>
                                    <?php
                                    $is_checked = true;
                                endforeach;
                                ?>
                                <!--                                <div class="pay__variant-info-text"><span class="red-text">Комиссия 3%</span> за наложенный платёж. Если хотите оплатить без комиссии, выберите <span class="underline-text">VISA</span>, либо получите <span class="green-text">скидку 2%</span>, оплатив переводом на карту.</div>-->
                                <div class="pay__variant-info-text"></div>
                            </div>

                            <!-- Комментарий к заказу-->

                            <div class="delivery-block">
                                    <label>Комментарий к заказу:</label>
                                    <textarea id="order-user-comment"
                                              name="UserClientOrder[comment]"
                                              class="placeholder"
                                              placeholder=""
                                              style="border: 2px solid #cbcbcb"
                                    ><?=$cart->comment?></textarea>
                            </div>

                        </div>
                        <div class="right-side-final-block">
                            <div class="order__result">
                                <div class="order__result-item">
                                    <span class="order__result-item-title">Товары</span>
                                    <span id="product-sum" data-product-price="<?= $cart->sum / 100 ?>"
                                          class="order__result-item-price"><?= $cart->sumFormat ?></span>
                                </div>
                                <div class="order__result-item">
                                    <span class="order__result-item-title">Доставка</span>
                                    <span id="delivery-sum" class="order__result-item-price">0 <span
                                                class="rub">i</span></span>
                                </div>
                                <!--                                <div class="order__result-item">-->
                                <!--                                    <span class="order__result-item-title">Комиссия</span>-->
                                <!--                                    <span id="commission-sum" class="order__result-item-price">0 <span class="rub">i</span></span>-->
                                <!--                                </div>-->
                                <div class="order__result-item">
                                    <span class="order__result-item-title">Скидка по промокоду</span>
                                    <span id="discount-sum"
                                          data-discount-code="<?= $coupon ? $coupon->coupon_code : 0 ?>"
                                          data-discount-sum="<?= floor(((int)$cart->sum_discount + (int)$cart->sum_delivery_discount) / 100) ?>"
                                          data-discount-is-percent="<?= $coupon ? $coupon->is_percent : 0 ?>"
                                          data-discount-value="<?= $coupon ? $coupon->coupon_value : 0 ?>"
                                          class="order__result-item-price"><?= $cart->sumDiscountFormat ?></span>
                                </div>
                                <div class="order__result-item">
                                    <span class="order__result-item-title last">Итого к оплате</span>
                                    <span id="total-sum"
                                          class="order__result-item-price"><?= $cart->sumWithoutDiscountFormat ?></span>
                                </div>
                            </div>
                            <input id="delivery_sum" type="hidden" value="0" name="UserClientOrder[sum_delivery]">
                            <div style="padding: 0 0 20px 0; font-size: .75em;">
                                Сумма доставки и комиссии могут отличаться от данных сайта. Точную сумму назовет
                                оператор.
                            </div>
                            <div class="order_load">
                                <div class="insurance-info-block">
                                    <div class="insurance-info-block-title">Страховка за наш счёт</div>
                                    <div class="insurance-info-block-text">Если транспортная компания потеряет или
                                        повредит Ваш заказ, мы оперативно вернём деньги, либо вышлем новый товар.
                                    </div>
                                </div>
                                <button id="real-submit-btn" class="btn _wide" type="submit" onclick="$(this).prop('disabled', true);$(this).closest('form').submit();">Оформить</button>
                                <a id="fake-submit-btn" class="btn _wide no-active">Оформить</a>

                                <?php /*
                                <div id="card-pay-buttons" class="card-pay-buttons">
                                    <button class="btn _wide" type="submit" name="action" value="card_now">Оплатить сейчас</button>
                                    <button class="btn _wide" type="submit" name="action" value="card_later">Оплатить позже</button>
                                    <button class="btn _wide" type="submit" name="action" value="card_later">Оформить</button>
                                </div>
                                */ ?>

                                <button id="card-pay-buttons" class="btn _wide" type="submit" name="action"
                                        value="card_later">Оформить
                                </button>

                                <?php /*
                                <div id="card-2-card-pay-buttons" class="card-2-card-pay-buttons">
                                    <button class="btn _wide" type="submit" name="action" value="card2card_now">Оплатить сейчас</button>
                                    <button class="btn _wide" type="submit" name="action" value="card2card_later">Оплатить позже</button>
                                    <button class="btn _wide" type="submit" name="action" value="card2card_later">Оформить</button>
                                </div>
                                */ ?>

                                <button id="card-2-card-pay-buttons" class="btn _wide" type="submit" name="action"
                                        value="card2card_later">Оформить
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>

    <div class="container" style="display: none;">
        <div class="response" style="display: flex; margin: 10px 0; width: 100%">
            <div id="iml-response" data-label="IML-response" style="width: 100%"></div>
            <div id="pp-response" data-label="PickPoint-response" style="width: 100%"></div>
        </div>
    </div>
</section>

<!--Modal https://jquerymodal.com/-->
<div id="pvz-modal" class="modal">
    <div class="modal-head">
        <div class="modal-header-title">Выберите пункт выдачи заказа:</div>
        <div class="modal-one-reset">
            <a href="#close-modal" rel="modal:close" class="close-modal-2">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1L1.45542 13.5753M1.45542 1L13 13.5753" stroke="#363636" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>
        </div>
    </div>
    <div id="map" style="width: 100%; height: 300px;">
        <div class="loader" style="width: 100%; height: auto; display: flex; justify-content: center;">
            <i class="fa fa-circle-o-notch fa-spin fa-fw"
               style="display: flex; justify-content: center; align-items: center;"></i>
            <div style="display: flex;"> Загрузка карты</div>
            <span class="sr-only">Загрузка...</span>
        </div>
    </div>

    <div class="ds-name">
        ...
    </div>
    <p><a class="back-to-list-pvz" id="back-to-list-pvz" href="#">Назад</a></p>
    <div id="addresses-pvz">

    </div>
    <div class="footer-selected-pvz">
        <a class="btn _wide" href="#" id="selected-pvz" rel="modal:close">Заберу здесь</a>
    </div>
</div>

<!--Modal-warning-->
<div id="warning-modal" class="modal">
    <div class="modal-head">
        <div class="modal-header-title">Ошибка</div>
        <div class="modal-one-reset">
            <a href="#close-modal" rel="modal:close" class="close-modal-2">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1L1.45542 13.5753M1.45542 1L13 13.5753" stroke="#363636" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>
        </div>
    </div>
    <div class="modal-content">
        <p>Укажите тип доставки</p>
    </div>
</div>

<style type="text/css">
    .delivery-sam-radio span {
        cursor: pointer;
    }

    .pay__check {
        border: 2px solid #f8cd4f;
    }

    #delivery_city {
        margin-left: 15px !important;
    }

    .delivery-form-tab-nav {
        margin: 0 0px;
        margin-top: 10px;
    }

    @media (max-width: 420px) {
        .delivery-block {
            flex-wrap: wrap;
        }

        .delivery-item {
            width: 100%;
            margin-bottom: 10px;
        }

        .d-svg {
            width: 20%;
        }

        .d-info {
            width: 70%;
            padding: 0px 15px;
        }

        .d-desk {
            font-size: 14px;
        }

        .pay__variant-item {
            width: 100%;
            margin-bottom: 10px;
        }

        .order__result-item {
            width: 100%;
        }

        .delivery-form {
            margin: 30px 0 20px;
        }
    }
</style>
