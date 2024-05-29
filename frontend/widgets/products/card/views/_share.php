<?php

use yii\helpers\Url;
use frontend\widgets\common\Icon;

?>
<div class="product-card-share">
    <span><span>Поделиться</span></span>
    <div>
        <a href="<?= Yii::$app->keyStorageApp->get('head.link.insta') ?>">
            <?= Icon::widget(['name' => 'soc-inst','width'=>'27px','height'=>'27px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
        <a href="http://vkontakte.ru/share.php?url=<?= Url::canonical() ?>"
           id="pr-vk-link"
           data-base="http://vkontakte.ru/share.php?url="
        >
            <?= Icon::widget(['name' => 'soc-vk','width'=>'27px','height'=>'27px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
        <a href="http://www.facebook.com/sharer.php?u=<?= Url::canonical() ?>"
           id="pr-fb-link"
           data-base="http://www.facebook.com/sharer.php?u="
        >
            <?= Icon::widget(['name' => 'soc-fb','width'=>'27px','height'=>'27px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
        <a href="mailto::?subject=<?= $model->title ?>&body=<?= Url::canonical() ?>"
           id="pr-ml-link"
        >
            <?= Icon::widget(['name' => 'soc-ml','width'=>'27px','height'=>'27px',
                'options'=>['fill'=>"#363636", 'stroke'=>"#363636"]
            ]) ?>
        </a>
        <div class="product-card-share__link">Ссылка на товар<br />
            <input readonly id="pr-copy" type="text" value="<?= Url::canonical() ?>" />
        </div>
        <div class="product-card-share__btn">
            <button id="pr-copy-btn" class="btn" onclick="
                document.getElementById('pr-copy').select();
                document.execCommand('pr-copy');
                window.getSelection().removeAllRanges();
                this.setAttribute('disable','disable')
            ">Копировать</button>
        </div>
    </div>
</div>

<?php /*
                     <a href="#">
                        <?= Icon::widget(['name' => 'soc-inst','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-vk','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-fb','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-wa','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-vb','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-tg','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-ml','width'=>'27px','height'=>'27px',
                            'options'=>['fill'=>"#363636", 'stroke'=>"#363636"]
                        ]) ?>
                    </a>
                    <a href="#">
                        <?= Icon::widget(['name' => 'soc-copy','width'=>'27px','height'=>'27px',
                            'options'=>['stroke'=>"#363636"]
                        ]) ?>
                    </a>

 */ ?>
