<?php

use yii\helpers\Html;

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
/* @var $isSlider */

$models = $dataProvider->getModels();

?>

<div class="block-section">
    <?php if ($head) { ?>
        <div class="block-section__head">
            <h1 class="h1<?= $headCenter ? ' center' : '' ?>">
                <?= $headUrl
                    ? Html::a($head, $headUrl)
                    : Html::tag('span', $head)
                ?>
            </h1>
        </div>
    <?php } ?>
    <div class="block-section__list">
        <div class="category-tile<?= $outerTitle?'-title-outer':''?><?= !$simpleList?' _slider':''?>">

            <?php if( !empty($models) ) { ?>
                <?= $this->render('_list',[
                    'id' => $id,
                    'head' => $head,
                    'headCenter' => $headCenter,
                    'linkShowMore' => $linkShowMore,
                    'linkShowAll' => $linkShowAll,
                    'linkLoadMore' => $linkLoadMore,
                    'headUrl' => $headUrl,
                    'outerTitle' => $outerTitle,
                    'simpleList' => $simpleList,
                    'dataProvider' => $dataProvider
                ]) ?>
            <?php } else { ?>
                <div class="category-tile__empty-list">Ничего не найдено</div>
            <?php } ?>

        </div>
    </div>
</div>


