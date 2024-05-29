<?php
use common\models\Currency;
use common\models\ProductPrice;
use yii\helpers\Url;

if ($lp->product->is_fixed_amount) {
    $real_count = $lp->product->ms_get_product_by_store($lp->product->ms_id);
}
$has_qty_err = false;
if (isset($_GET['ms_error'])) {
    $ms_error = base64_decode(trim($_GET['ms_error'], "/"));
    $ms_error = json_decode($ms_error, true);
    if ($ms_error['errors_qty']) {
        foreach ($ms_error['errors_qty'] as $msid => $qty) {
            if ($msid == $lp->product->ms_id) {
                $has_qty_err = true;
                $qty_real = $qty;
            }
        }
    }
}
?>
<tr class="lk-table__row" data-uuid="<?=$lp->product->ms_id?>">
    <td class="lk-table-fd-name">
        <a href="<?= Url::to($lp->product->route)?>" class="lk-table-card">
            <span style="background-image: url('<?=$lp->product->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($lp->product->getImgUrl() )?>'); "></span>
            <h2 class="lk-table-card__head">
                <?=mb_strimwidth($lp->product->title, 0, 75, "...")?>
                <span><?= $lp->product->article ? 'Арт. '.$lp->product->article : '' ?></span>
            </h2>
        </a>
    </td>
    <td class="lk-table-fd-qty">

        <div class="number _flex _avg js-lk-change-cart" data-product-id="<?= $lp->product->id ?>" data-max="<?= $lp->product->amount ?>" <?php if (isset($real_count)) echo 'data-real-count="'.$real_count.'"';?>>
            <span class="number__minus">-</span>
            <input type="number" class="number__value" value="<?=$lp->count?>" step="1" min="1">
            <input type="hidden" value="<?= $lp->count ?>" />
            <span class="number__plus">+</span>
        </div>
        <?php if($lp->count > $lp->product->amount) {?>
            <span class="in-stock">
                Нет в наличии (дост. <?= $lp->product->amount ?> шт.)
            </span>
        <?php } else if($has_qty_err) {?>
            <span class="in-stock">
                Нет в наличии (дост. <?= $qty_real ?> шт.)
            </span>
        <?php } ?>
    </td>
    <td class="lk-table-fd-sum">
        <?php $pp = ProductPrice::getParsePrice(
            $lp->is_gift ? 0 : $lp->product->clientPriceValue,
            Currency::DEFAULT_CART_PRICE_CUR_ISO,
            $to = null,
            $addSale = 0,
            (int) $lp->count
        ); echo $lp->is_gift ? '<span style="font-family: \'Museo Sans Cyrl\',sans-serif;">Подарок</span>' : ($pp->ceil_fr.' '.$pp->cur) ?>
    </td>
    <td class="lk-table-fd-def js-lk-cart-deferred <?= Yii::$app->client->identity->isDeferred($lp->product->id) ? ' -act' : '' ?>"
         data-product-id="<?= $lp->product->id ?>">
        <a class="link-def lk-table-fd-def__put" href="#put" title="Хочешь получать актуальную информацию о поступлении любимых товаров, а также акциях и скидках? Добавь товар в любимые!">
            <svg class="icon" width="17px" height="17px" stroke="#000" fill="none"><use xlink:href="/img/svg/icons.svg#like"></use></svg>
        </a>
        <a class="link-def lk-table-fd-def__unput" href="#unput">
            <svg class="icon" width="17px" height="17px" stroke="#000" fill="#000"><use xlink:href="/img/svg/icons.svg#like"></use></svg>
        </a>
    </td>
    <td class="lk-table-fd-del">
        <a class="link-del js-lk-cart-del" href="#remove" data-product-id="<?= $lp->product->id ?>">Удалить</a>
    </td>
</tr>

<?php if ($lp->product->is_fixed_amount):?>
    <tr class="lk-table-additional-row <?php if (isset($real_count) && $real_count >= $lp->count) echo 'hidden-row';?>">
        <td colspan="5" class="lk-table-info-text"><?=Yii::$app->keyStorageApp->get('cart_info_text')?></td>
    </tr>
<?php endif;?>
