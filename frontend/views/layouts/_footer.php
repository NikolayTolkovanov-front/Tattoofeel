<?php

use common\models\Subdomains;
use frontend\widgets\common\Icon;
use yii\helpers\Url;

$subdomainInfo = Yii::$app->subdomains->get();

$keys = Yii::$app->keyStorageApp->getAll([
    'footer.logo-info',
    'head.link.telega',
    'head.link.vk',
    'email',
    'footer.info',
]);
?>
<footer class="footer-box">
    <div class="container">
        <div class="footer">

            <div class="footer__left">
                <?php if ('/' == Yii::$app->request->url):?>
                    <div href="<?= Url::home()?>" class="footer__left__logo">
                        <?= Icon::widget(['name' => 'logo','width'=>'63px','height'=>'63px', 'options' => ['fill'=>'#fff']]) ?>
                    </div>
                <?php else:?>
                    <a href="<?= Url::home()?>" class="footer__left__logo">
                        <?= Icon::widget(['name' => 'logo','width'=>'63px','height'=>'63px', 'options' => ['fill'=>'#fff']]) ?>
                    </a>
                <?php endif;?>

                <div class="footer__left__desc">
                    <?=$keys['footer.logo-info'] ?>
                    <div class="footer__left__desc__soc">
                        <a target="_blank" href="<?= $keys['head.link.telega'] ?>">
                            <?= Icon::widget(['name' => 'telega','width'=>'16px','height'=>'16px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                        <a target="_blank" href="<?= $keys['head.link.vk'] ?>">
                            <?= Icon::widget(['name' => 'vk','width'=>'16px','height'=>'16px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer__pay">
                <div class="footer__pay__img">
                    <a href="/pay-card/" class="img-master-visa"></a>
                </div>
                <div class="footer__pay__link">
                    <a href="<?= Url::to(['/payment']) ?>">Оплата</a>
                    <a href="<?= Url::to(['/warranty']) ?>">Гарантии</a>
                </div>
            </div>
            <div class="footer__spr__contact"></div>
            <div class="footer__contact">
                <a class="footer__contact__mail" href="mailto:<?= $keys['email'] ?>">
                    <?= Icon::widget(['name' => 'mail','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?= $keys['email']?>
                </a>
                <span class="footer__contact__time">
                    <?= Icon::widget(['name' => 'time','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?=$subdomainInfo['work_time']?>
                </span>
            </div>
            <div class="footer__spr__contact__phone"></div>
            <div class="footer__contact footer__contact__phone">
                <a rel="nofollow" class="footer__contact__tel" href="tel:<?=Yii::$app->keyStorageApp->getPhoneValueEx($subdomainInfo['phone'])?>" onclick="ym(73251517,'reachGoal','phone_footer'); return true;">
                    <?= Icon::widget(['name' => 'phone','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?=$subdomainInfo['phone']?>
                </a>
                <span class="footer__contact__address">
                    <?=Icon::widget(['name' => 'point','width'=>'22px','height'=>'22px', 'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]])?>
                    <?=$subdomainInfo['address']?>
                </span>
            </div>
            <ul class="footer__menu">
                <li><a href="<?= Url::to(['/catalog']) ?>">Каталог</a></li>
                <li><a href="<?= Url::to(['/article']) ?>">Статьи</a></li>
                <li><a href="<?= Url::to(['/contact']) ?>">Контакты</a></li>
                <li><a href="<?= Url::to(['/brands']) ?>">Бренды</a></li>
                <li><a href="<?= Url::to(['/news']) ?>">Новости</a></li>
                <li><a href="<?= Url::to(['/delivery']) ?>">Доставка</a></li>
                <li><a href="<?= Url::to(['/stock']) ?>">Акции</a></li>
                <li><a href="<?= Url::to(['/team']) ?>">Наша команда</a></li>
            </ul>
            <div class="footer__copy">
                <?= $keys['footer.info'] ?>
                <address>©&nbsp;Copyright&nbsp;<?= date('Y') ?> tattoofeel.ru. All Rights Reserved.</address>
            </div>
            <div class="footer__btn">
                <a class="btn _wide js-show-reviews-form" href="#">Что нам улучшить ?</a>
                <a class="footer__btn__link" href="<?= Url::to(['/privacy-policy']) ?>">Политика конфиденциальности</a>
            </div>
        </div>
    </div>

    <div id="toTop">
        <div class="arrow-top"></div>
    </div>

    <div class="my-modal">
        <div id="reviews-form" class="modal-form"></div>
    </div>

    <div class="my-modal">
        <div id="not-found-search-form" class="modal-form"></div>
    </div>

    <div class="my-modal">
        <div id="select-your-city-form" class="modal-form">
            <div class="modal-close"></div>
            <div class="contact-form__fields">
                <input type="text" class="search-select-your-city" />

                <?php $subdomains = Subdomains::find()->asArray()->all();?>
                <?php if (!empty($subdomains)):?>
                    <?php
                    $subdomains[] = array(
                        'id' => 0,
                        'subdomain' => '',
                        'city' => 'Москва',
                    );

                    usort($subdomains, function($a, $b){
                        return strnatcmp($a['city'], $b['city']);
                    });

                    $cols = array_chunk($subdomains, ceil(count($subdomains) / 4));

                    $arUrl = parse_url(env('FRONTEND_HOST_INFO'));
                    ?>
                    <div class="select-your-city-list">
                        <?php foreach ($cols as $col):?>
                            <div class="select-your-city-col">
                                <?php foreach ($col as $item):?>
                                    <div class="select-your-city-item">
                                        <a class="select-your-city-link" href="<?=$arUrl['scheme'].'://'.(!empty($item['subdomain']) ? $item['subdomain'].'.' : '').$arUrl['host']?>"><?=$item['city']?></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        $(document).on('keyup', '.search-select-your-city', function(e) {
                            let inputVal = $(this).val().toLowerCase();
                            $('.select-your-city-link').each(function(index, value) {
                                let currentVal = $(this).html().toLowerCase();
                                let opacity = '0.6';
                                if (currentVal.indexOf(inputVal) === 0) {
                                    opacity = '1';
                                }
                                $(this).css('opacity', opacity);
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Roistat Counter Start -->
    <script>
        (function(w, d, s, h, id) {
            w.roistatProjectId = id; w.roistatHost = h;
            var p = d.location.protocol == "https:" ? "https://" : "http://";
            var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init?referrer="+encodeURIComponent(d.location.href);
            var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
        })(window, document, 'script', 'cloud.roistat.com', 'cd92cd6875fdf2405e2ccde4961972a1');
    </script>
    <!-- Roistat Counter End -->
</footer>
