<?php

/* @var $id */
/* @var $head bool */
/* @var $headCenter bool */
/* @var $linkShowMore */
/* @var $linkShowAll */
/* @var $linkLoadMore */
/* @var $headUrl */
/* @var $outerTitle */
/* @var $simpleList */
/* @var $dataProvider */


use frontend\widgets\common\Icon;
use yii\helpers\Url;

?>
    <div class="category-tile<?= $outerTitle?'-title-outer':''?>__list" id="<?= $id.'__list' ?>">

        <?= $this->render('_list-items', [
            'dataProvider' => $dataProvider
        ]) ?>

    </div>
    <ul class="category-tile__dots"></ul>

<?php if ($linkShowMore && $dataProvider->getTotalCount() > 12) {?>
    <div class="category-tile__show-more">
        <a
                class="put-link"
                href="#"
                data-toggle="#<?= $id.'__list' ?>"
                data-toggle-class="-open"
                data-toggle-self-class="-put-link-arw-act"
                data-toggle-self-label="Скрыть"
                data-toggle-self-def-label="Показать все"
        > 
            <span>Показать все</span>
            <?= Icon::widget(['name' => 'arw','width'=>'12px','height'=>'12px',
                'options'=>['stroke'=>"#363636", 'class'=>'icon r']
            ]) ?>
        </a>
    </div>
<?php } ?>

<?php if ($linkShowAll) {?>
    <div class="category-tile__show-all">
        <a href="<?= $linkShowAll ?>" class="btn-bord">Все категории</a>
    </div>
<?php } ?>

<?php if (
    $dataProvider->pagination &&
    $dataProvider->getTotalCount() >
    ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize &&
    $linkLoadMore
) { ?>
    <div class="btn-box center">
        <a href="#load-more"
           class="btn _wide"
           data-pjax="0"
           data-load-more-url="<?= $linkLoadMore ?>"
           data-load-more-container="#<?= $id.'__list' ?>"
        >
            Показать еще
            <?= Icon::widget(['name' => 'more-dots','width'=>'18px','height'=>'18px',
                'options'=>['fill'=>"#363636", 'class' => 'icon icon-dots']
            ]) ?>
            <?= Icon::widget(['name' => 'loader','width'=>'18px','height'=>'18px',
                'options'=>['stroke'=>"#363636", 'class' => 'icon icon-loader']
            ]) ?>
        </a>
    </div>
<?php } ?>
