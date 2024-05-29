<?php

/* @var $id */
/* @var $linkLoadMore */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $emptyListShow */
/* @var $sorted */
/* @var $inStock */
/* @var $brandPage */
/* @var int $pagePost */
/* @var $models Product[] */
/* @var $isConfigInStockByMsIds array */
/* @var $mappedRetailPrices array */



use common\components\CustomPager;
use common\models\Product;
use frontend\widgets\common\Icon;

?>
<?php if ($dataProvider->getTotalCount() && !$brandPage) { ?>
    <div class="product-list__sorted">
        <div class="product-list__sorted__inner">
            <div class="sorted js-stock-product-list">
                Показывать: <span><?=$inStock ? 'В наличии' : 'Все'?></span>
                <ul>
                    <li <?=$inStock ? '' : 'class="-act"'?>><a href="#all" data-prop="stock_all">Все</a></li>
                    <li <?=$inStock ? 'class="-act"' : ''?>><a href="#in-stock" data-prop="in_stock">В наличии</a></li>
                </ul>
                <input type="hidden" id="js-stock-product-list__hidden" value="<?=$inStock ? 'in_stock' : 'stock_all'?>"/>
            </div>

            <?php
            $sortList = array(
                'brand-desc' => 'Бренду',
                'title-asc' => 'Названию',
                'views-desc' => 'Популярности',
                'price-asc' => 'Цене ↑',
                'price-desc' => 'Цене ↓',
            );
            ?>
            <div class="sorted js-sorted-product-list">
                Сортировать по: <span><?=$sorted ? $sortList[$sorted]: 'Умолчанию'?></span>
                <ul>
                    <li <?php if (!$sorted) echo 'class="-act"'?>><a href="#default" data-prop="default">Умолчанию</a></li>
                    <?php foreach ($sortList as $key => $item):?>
                        <li <?php if ($key == $sorted) echo 'class="-act"'?>><a href="#<?=$key?>" data-prop="<?=$key?>"><?=$item?></a></li>
                    <?php endforeach;?>
                </ul>
                <input type="hidden" id="js-sorted-product-list__hidden" value="<?=$sorted ?: ''?>"/>
            </div>
        </div>
    </div>
<?php } ?>

<div class="product-list" id="<?= $id . '__list' ?>" page-size="<?= $dataProvider->pagination->pageSize ?>">
    <?php if ($emptyListShow && empty($models)):?>
        <p class="empty">Товаров не найдено.</p>
    <?php else:?>
        <?php

        foreach($models as $model) {
            echo $this->render(
                    '_item-is-config',
                    [
                        'model' => $model,
                        'isConfigInStockByMsIds' => $isConfigInStockByMsIds,
                        'mappedRetailPrices' => $mappedRetailPrices,
                    ]
            );
        }
        ?>

        <?php if (!$brandPage):?>
            <div class="btn-box center js-link-pager-pagination">
                <?php echo CustomPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'hideOnSinglePage' => true,
                    'maxButtonCount' => 5,

                    // Настройки классов css для ссылок
                    'linkOptions' => ['class' => 'btn pager-link'],
                    'separatorPageCssClass' => 'pager-separator',
                ]); ?>
            </div>
        <?php endif;?>

        <?php if ($dataProvider->getTotalCount() <= ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize):?>
            <div class="none last-page"></div>
        <?php endif;?>
    <?php endif;?>
</div>

<?php if ($dataProvider->getTotalCount() > ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize && $linkLoadMore):?>
    <div class="btn-box center">
        <a href="#load-more"
           class="btn _wide js-catalog-load-more"
           data-pjax="0"
           data-page="<?=$pagePost?>"
           data-load-more-url-new="<?= $linkLoadMore ?>"
        >
            Показать еще
            <?= Icon::widget(['name' => 'more-dots', 'width' => '18px', 'height' => '18px',
                'options' => ['fill' => "#363636", 'class' => 'icon icon-dots']
            ]) ?>
            <?= Icon::widget(['name' => 'loader', 'width' => '18px', 'height' => '18px',
                'options' => ['stroke' => "#363636", 'class' => 'icon icon-loader']
            ]) ?>
        </a>
    </div>
<?php endif;?>
