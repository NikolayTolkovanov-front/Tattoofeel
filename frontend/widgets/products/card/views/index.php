<?php

/**
 * @var $reviews
 */

use cheatsheet\Time;
use frontend\widgets\common\seoH1\SeoH1;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use frontend\helpers\All;

$seoH1 = $model->title;
$subdomainInfo = Yii::$app->subdomains->get();

$rating_sum = 0;
foreach ($reviews as $item) {
    $rating_sum += $item->rating;
}
$average_rating = count($reviews) ? round($rating_sum / count($reviews), 0) : 0;

function BITGetDeclNum($value = 1, $status = array('', 'а', 'ов')) {
    $array = array(2, 0, 1, 1, 1, 2);
    return $status[($value % 100 > 4 && $value % 100 < 20) ? 2 : $array[($value % 10 < 5) ? $value % 10 : 5]];
}
?>

<script>
    window.esc = function(v) {
        return v;
        return v.replace("'","\'").replace(/\r?\n/g, " ");
    };
    window.products_configs = {};
    <?php if($model->isConfig) { ?>
    <?php foreach($model->configs as $cf) {

    $warranty = $cf->warranty ? Html::tag('p',
        "Гарантия: ".All::asWarranty(floor($cf->warranty * Time::SECONDS_IN_A_YEAR))
    ) : null;
    $manufacturer = $cf->manufacturer ? Html::tag('p', "Страна производителя: ".$cf->manufacturer) : null;

    $brand = $cf->brand_ ? "<p>Бренд: <a href=\"".
        Url::to(['/brands/'.$cf->brand_->slug])."\">". $cf->brand_->title ."</a></p>" : null;

    $currentPrice = $cf->clientPriceValue;
    $oldPrice = $cf->retailPrice->price;
    $salePercent = $oldPrice ? number_format(floor(100 - (100 * $currentPrice / $oldPrice)), 0, '.', ' ') : 0;

    //$bigImages = json_encode( !empty($cf->bigImages_) ? ArrayHelper::getColumn($cf->bigImages_,'url') : [] );
    $bigImages = array();
    if (!empty($cf->bigImages_)) {
        foreach ($cf->bigImages_ as $img) {
            $bigImages[] = $img->getBigImgUrl();
        }
    }
    ?>

            window.products_configs['<?=  str_replace("'", "\'", $cf->id) ?>'] = {
                'id': '<?=  str_replace("'", "\'", $cf->id) ?>',
                'ms_id': '<?=  str_replace("'", "\'", $cf->ms_id) ?>',
                'title': window.esc('<?=  str_replace("'", "\'", $cf->title) ?>'),
                'title_short':  window.esc('<?=  str_replace("'", "\'", $cf->title_short) ?>'),
                'article': '<?=  str_replace("'", "\'", $cf->article) ?>',
                'slug': '<?=  str_replace("'", "\'", $cf->slug) ?>',
                'route': '<?=  str_replace("'", "\'", $cf->route) ?>',
                'canonical': '<?=  str_replace("'", "\'", Url::canonical()) ?>',
                'bigImages_': '<?=  str_replace("'", "\'", json_encode($bigImages)) ?>',
                'defaultPict': '<?=  str_replace("'", "\'", Yii::$app->params['default_pict']) ?>',
                'body_short': window.esc('<?=  str_replace("'", "\'", All::rn($cf->bodyShort_)) ?>'),
                'brand_title': window.esc('<?=  str_replace("'", "\'", isset($cf->brand_) ? $cf->brand_->title : '') ?>'),
                'brand_url': '<?=  str_replace("'", "\'", isset($cf->brand_) ? Url::to(['/brands/'.$cf->brand_->slug]) : '') ?>',
                'amount': '<?=  str_replace("'", "\'", $cf->amount) ?>',
                'amountIndex': '<?=  str_replace("'", "\'", $cf->getAmountIndex()) ?>',
                'amountTitle': window.esc('<?=  str_replace("'", "\'", $cf->getAmountTitle()) ?>'),
                'frontendOldPrice': '<?=  str_replace("'", "\'", $cf->getFrontendOldPrice()) ?>',
                'frontendCurrentPrice': '<?=  str_replace("'", "\'", $cf->getFrontendCurrentPrice()) ?>',
                //'sale': '<?=  str_replace("'", "\'", $cf->clientSalePercent) ?>',
                'sale': '<?=  str_replace("'", "\'", $salePercent) ?>',
                'video_code_encode': window.esc('<?=  str_replace("'", "\'", $cf->video_code_) ?>'),
                'isDeferred': '<?=  str_replace("'", "\'", (int) Yii::$app->client->identity->isDeferred($cf->id)) ?>',
                'body': window.esc('<?= All::esc(All::rn(
                    HtmlPurifier::process($cf->body . $warranty . $manufacturer . $brand)
                )) ?>')
            };
        <?php /* 'question': '<?=  str_replace("'", "\'", json_encode(ArrayHelper::map($cf->pubQuestions_,'title', 'answer'))) ?>' */ } ?>
    <?php } ?>
</script>
<div class="product-card" itemscope="" itemtype="http://schema.org/Product">
    <h1 class="h1 product-card__title" id="pr-title" title="<?= SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo]) ?>"
        itemprop="name"><?= SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo]) ?></h1>

    <div class="product-card__title-art" id="pr-article">
        Код: <span data-empty="не задано"><?= $model->article?$model->article:'не задано' ?></span>
    </div>

    <div class="product-card-data__vendor <?= $model->brand_? '' : 'none' ?>">
                    Бренд:
                    <?php if($model->brand_) {?>
                        <a id="pr-brand" href="<?= Url::to(['/brands/'.$model->brand_->slug]) ?>"><?= $model->brand_->title ?></a>
                    <?php } else { ?>
                        <a id="pr-brand" href="#"></a>
                    <?php } ?>
                </div>

    <div class="product-card__body">
        <div class="product-card__body__pict">

            <?=$this->render('_pict',[
                'model' => $model
            ])?>

            <?=$this->render('_share',[
                'model' => $model
            ])?>

        </div>
        <div class="product-card__body__data">
            <div class="product-card-data">
                <div class="product-card-data__rating" itemprop="aggregateRating" itemscope="" itemtype="https://schema.org/AggregateRating">
                    <div class="review__rating">
                        <?php for ($i = 1; $i <= 5; $i++):?>
                            <?php if ($i <= $average_rating):?>
                                <div class="review__full-star">
                                    <img src="/img/full_star.png">
                                </div>
                            <?php else:?>
                                <div class="review__empty-star">
                                    <img src="/img/empty_star.png">
                                </div>
                            <?php endif;?>
                        <?php endfor;?>

                        <a href="#" class="review__rating-link" data-product_id="<?=$model->id?>">
                            <?php if (count($reviews)):?>
                                <?=count($reviews)?> отзыв<?=BITGetDeclNum(count($reviews));?>
                            <?php else:?>
                                Добавить отзыв
                            <?php endif;?>
                        </a>
                    </div>

                    <meta itemprop="bestRating" content="5">
                    <meta itemprop="ratingValue" content="<?=$average_rating?>">
                    <span itemprop="ratingCount" style="display: none;"><?=count($reviews)?></span>
                </div>

                <div class="product-card-data__desc" id="pr-body_short" itemprop="description">
                    <?= $model->bodyShort_ ?>
                    <?php if ($model->is_oversized):?>
                        <div class="product-card-data__desc-is-oversized">Крупногабаритный товар.</div>
                    <?php endif;?>
                </div>

                <?= !empty($model->isConfig) && count($model->configs) ?
                    $this->render('_config',[
                        'model' => $model,
                        'configs' => $model->configs,
                        'count' => count($model->configs)
                    ]) : null ?>

                <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                <div class="product-card-data__amount">
                    <div id="pr-amount" class="product-card-amount _v_<?= $model->amountIndex ?>">
                        Наличие: <span id="pr-amount-text"><?= $model->amountTitle ?></span>
                    </div>

                        <link itemprop="availability" href="http://schema.org/<?=(int)$model->amountIndex > 0 ? "InStock" : "OutOfStock"?>">
                </div>

                <?= $this->render('_price',[
                    'model' => $model
                ]) ?>

            </div>
        </div>
    </div>

</div>

</div>
