<?php
use frontend\widgets\common\Icon;
use yii\helpers\Url;

$subdomainInfo = Yii::$app->subdomains->get();
if (is_null($subdomainInfo)) {
    Yii::$app->response->redirect(env('FRONTEND_HOST_INFO'), 301);
}
$keys = Yii::$app->keyStorageApp->getAll(['head.link.telega', 'head.link.vk', 'phone_1']);

?>
<?php if (env('HEADER_WARNING_SHOW')) { ?>
    <div class="header-temporary-text" style="background: <?='#'.env('HEADER_WARNING_MSG_COLOR_1')?>;text-align: center;padding: 6px 0;">
        <div class="container">
            <?=env('HEADER_WARNING_MSG_TEXT_1')?>
        </div>
    </div>
<?php } ?>

<header class="header">
    <div class="container">
        <div class="header-inner">
            <div class="header__name">Интернет магазин для мастеров татуировки и татуажа в <span><?=$subdomainInfo['word_form']?></span></div>

            <div class="header__phone">
                <div class="header-address">
                    <?= Icon::widget(['name' => 'point','width'=>'20px','height'=>'20px',
                        'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                    <div class="contact__block-data__desc">Адрес: <?=$subdomainInfo['address']?></div>
                    <div class="contact__block-data__desc">Подписывайтесь: <a target="_blank" href="<?= $keys['head.link.telega'] ?>">Telegram</a> | <a target="_blank" href="<?= $keys['head.link.vk'] ?>">VK</a></div>
                    <div class="contact__block-data__desc">Ваш город: <?=$subdomainInfo['city']?> <a href="javascript:void(0);" class="your-city js-select-your-city">Изменить</a></div>
                </div>
            </div>

            <div class="header__phone">
                <div class="header-phone">
                    <a rel="nofollow" href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_1') ?>" class="header-phone__number" onclick="ym(73251517,'reachGoal','phone_header'); return true;">
                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <?=$keys['phone_1']?>
                    </a>
                    <div class="header-phone__time">Только звонки</div>
                    <a target="_blank" rel="nofollow" href="https://wa.me/+79152968604" class="header-phone__number" onclick="ym(73251517,'reachGoal','phone_header'); return true;">
                        <svg style="position: absolute; left: 0px; width: 20px; height: auto; top: 45px;" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1047_967)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9965 0C5.38113 0 0 5.38264 0 11.9999C0 14.6242 0.846321 17.058 2.285 19.0335L0.789795 23.4919L5.40222 22.0178C7.2993 23.2735 9.56321 24 12.0035 24C18.6189 24 24 18.6172 24 12.0001C24 5.38285 18.6189 0.000198364 12.0035 0.000198364L11.9965 0ZM8.64642 6.09541C8.41374 5.53814 8.23739 5.51704 7.88489 5.5027C7.76486 5.49573 7.63111 5.48877 7.48282 5.48877C7.02422 5.48877 6.54473 5.62276 6.25552 5.91902C5.90302 6.27879 5.02841 7.11819 5.02841 8.8396C5.02841 10.561 6.28379 12.2259 6.45297 12.4588C6.62932 12.6914 8.90039 16.2752 12.4266 17.7357C15.1842 18.8786 16.0025 18.7726 16.63 18.6387C17.5468 18.4412 18.6965 17.7636 18.9857 16.9455C19.2749 16.127 19.2749 15.4286 19.1901 15.2805C19.1055 15.1323 18.8726 15.0479 18.5201 14.8713C18.1676 14.6949 16.4537 13.8483 16.1292 13.7354C15.8118 13.6156 15.5086 13.658 15.269 13.9967C14.9304 14.4693 14.599 14.9492 14.3309 15.2383C14.1193 15.464 13.7736 15.4923 13.4846 15.3722C13.0966 15.2102 12.0107 14.8289 10.6705 13.6367C9.63369 12.7127 8.92848 11.5629 8.72406 11.2172C8.51944 10.8646 8.70294 10.6598 8.86496 10.4694C9.04131 10.2506 9.21052 10.0955 9.38687 9.89084C9.56322 9.68636 9.66195 9.58045 9.77481 9.34053C9.89483 9.10778 9.81003 8.86787 9.72543 8.69147C9.64084 8.51507 8.93563 6.79365 8.64642 6.09541Z" fill="#363636"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_1047_967">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        8 (915) 296-86-04
                    </a>
                    <div class="header-phone__time">Только Whatsapp</div>
                    <div class="header-phone__time"><?=$subdomainInfo['work_time']?></div>
                </div>
            </div>
            <div class="header__btn">
                <div class="header__btn__soc">
                    <a target="_blank" href="<?= $keys['head.link.telega'] ?>">
                        <?= Icon::widget(['name' => 'telega','width'=>'23px','height'=>'23px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                    <a target="_blank" href="<?= $keys['head.link.vk'] ?>">
                        <?= Icon::widget(['name' => 'vk','width'=>'23px','height'=>'23px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                </div>
                <?php if(Yii::$app->user->isGuest) {?>
                    <a class="btn" href="<?= Url::to(['/lk/login']) ?>">Вход <span class="header__btn__ext-text">в личный кабинет</span></a>
                <?php } else { ?>
                    <a href="<?= Url::to(['/lk']) ?>">
                        <?= Icon::widget(['name' => 'lk','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'style'=>'vertical-align:middle']]) ?>

                        <?= Yii::$app->user->identity->username ?>
                    </a>
                    <a href="<?= Url::to(['/lk/logout']) ?>">Выход</a>
                <?php } ?>
            </div>
        </div>
    </div>

</header>
