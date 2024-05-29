<?php

/**
 * @var $productDataProvider frontend\models\Product
 * @var $isAjax bool
 * @var $brandPage bool
 */

use common\components\CustomPager;
use frontend\widgets\products\plist\ProductsList;

$brandPage = isset($brandPage) ? $brandPage : false;
?>

<?= ProductsList::widget([
    'isAjax' => isset($isAjax) ? $isAjax : false,
    'dataProvider' => $productDataProvider,
    'linkLoadMore' => isset($linkLoadMore) ? $linkLoadMore : false,
    'emptyListShow' => isset($emptyListShow) ? $emptyListShow : false,
    'hasFilter' => isset($hasFilter) ? $hasFilter : false,
    'brandPage' => $brandPage,
    'pagePost' => 0,
]) ?>

<?php if (!$brandPage):?>
    <div class="btn-box center js-link-pager-pagination">
        <?php echo CustomPager::widget([
            'pagination' => $productDataProvider->pagination,
            'hideOnSinglePage' => true,
            'maxButtonCount' => 5,

            // Настройки классов css для ссылок
            'linkOptions' => ['class' => 'btn pager-link'],
            'separatorPageCssClass' => 'pager-separator',
        ]); ?>
    </div>
<?php endif;?>