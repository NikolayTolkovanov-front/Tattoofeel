<?php

/* @var $id */
/* @var $linkLoadMore */
/* @var $dataProvider */


use frontend\widgets\common\Icon;

$models = $dataProvider->getModels();

?>
<div class="articles" id="<?= $id.'__list' ?>">
    <?php if( !empty($models) ) { ?>
        <?= $this->render('_list-items',[
            'id' => $id,
            'linkLoadMore' => $linkLoadMore,
            'dataProvider' => $dataProvider
        ]) ?>
    <?php } else { ?>
        <div class="article__empty-list">Ничего не найдено</div>
    <?php } ?>
</div>

<?php if (
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
