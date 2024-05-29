<?php
use frontend\widgets\common\Icon;
use yii\helpers\Url;
?>

<footer class="footer-box">
    <div class="container">
        <div class="footer">

            <div class="footer__left">
                <a href="<?= Url::home()?>" class="footer__left__logo">
                    <?= Icon::widget(['name' => 'logo','width'=>'63px','height'=>'63px', 'options' => ['fill'=>'#fff']]) ?>
                </a>
                <div class="footer__left__desc">
                    <?= Yii::$app->keyStorageApp->get('footer.logo-info') ?>
                    <div class="footer__left__desc__soc">
                        <?php /*
                        <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.insta') ?>">
                            <?= Icon::widget(['name' => 'insta','width'=>'18px','height'=>'18px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                        */ ?>
                        <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.telega') ?>">
                            <?= Icon::widget(['name' => 'telega','width'=>'16px','height'=>'16px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                        <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.vk') ?>">
                            <?= Icon::widget(['name' => 'vk','width'=>'16px','height'=>'16px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                        <?php /*
                        <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.fb') ?>">
                            <?= Icon::widget(['name' => 'fb','width'=>'16px','height'=>'16px',
                                'options'=>['fill'=>"#363636"]
                            ]) ?>
                        </a>
                        */ ?>
                    </div>
                </div>
            </div>
            <div class="footer__pay">

            </div>
            <div class="footer__spr__contact"></div>
            <div class="footer__contact">
                <a class="footer__contact__mail" href="mailto:<?= Yii::$app->keyStorageApp->get('email') ?>">
                    <?= Icon::widget(['name' => 'mail','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?= Yii::$app->keyStorageApp->get('email') ?>
                </a>
                <span class="footer__contact__time">
                    <?= Icon::widget(['name' => 'time','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?= Yii::$app->keyStorageApp->get('time') ?>
                </span>
            </div>
            <div class="footer__spr__contact__phone"></div>
            <div class="footer__contact footer__contact__phone">
                <a class="footer__contact__tel" href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_1') ?>">
                    <?= Icon::widget(['name' => 'phone','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?= Yii::$app->keyStorageApp->get('phone_1') ?>
                </a>
                <a class="footer__contact__tel" href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_2') ?>">
                    <?= Icon::widget(['name' => 'phone','width'=>'22px','height'=>'22px',
                        'options'=>['fill'=>"#F8CD4F",'stroke'=>"#F8CD4F"]]) ?>
                    <?= Yii::$app->keyStorageApp->get('phone_2') ?>
                </a>
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
