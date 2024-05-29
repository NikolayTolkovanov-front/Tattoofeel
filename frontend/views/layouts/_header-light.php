<?php
use frontend\widgets\common\Icon;
use yii\helpers\Url; ?>

<header class="header">
    <div class="container">
        <div class="header-inner">
            <div class="header__name">Интернет магазин для мастеров татуировки и татуажа.</div>
            <div class="header__phone">
                <div class="header-phone">
                    <a href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_1') ?>" class="header-phone__number">
                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <?= Yii::$app->keyStorageApp->get('phone_1') ?>
                    </a>
                    <div class="header-phone__time"><?= Yii::$app->keyStorageApp->get('time') ?></div>
                </div>
            </div>
            <div class="header__phone">
                <div class="header-phone">
                    <a href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_2') ?>" class="header-phone__number">
                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <?= Yii::$app->keyStorageApp->get('phone_2') ?>
                    </a>
                    <div class="header-phone__time">Только звонки</div>
                </div>
            </div>
            <div class="header__btn">
                <div class="header__btn__soc">
                    <?php /*
                    <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.insta') ?>">
                        <?= Icon::widget(['name' => 'insta','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                    */ ?>
                    <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.telega') ?>">
                        <?= Icon::widget(['name' => 'telega','width'=>'23px','height'=>'23px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                    <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.vk') ?>">
                        <?= Icon::widget(['name' => 'vk','width'=>'23px','height'=>'23px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                    <?php /*
                    <a target="_blank" href="<?= Yii::$app->keyStorageApp->get('head.link.fb') ?>">
                        <?= Icon::widget(['name' => 'fb','width'=>'23px','height'=>'23px',
                            'options'=>['fill'=>"#fff"]
                        ]) ?>
                    </a>
                    */ ?>
                </div>
            </div>
        </div>
    </div>
</header>
