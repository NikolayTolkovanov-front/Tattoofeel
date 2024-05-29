<?php
/**
 * @var $cat_ms_id
 * @var array $category
 * @var $filterBrands
 * @var $filterManufacturer
 * @var $filterTypeEq
 * @var $filtersCommon
 * @var $minMaxPrices
 * @var $discount
 * @var $search
 * @var $arFilter
 */

use yii\helpers\Inflector; ?>

<div class="category-filter js-catalog-filter">
    <div class="category-filter__block -open">
        <div class="category-filter__block__title">Цены</div>
        <div class="category-filter__block__form">
            <div class="slider-price">
                <input name="price" type="hidden" value="<?=isset($arFilter['prices']) ? implode(',', $arFilter['prices']) : ''?>" default-value="<?=implode(',', $minMaxPrices)?>" />
                <span class="fl">от</span>
                <span class="fr">до</span>
            </div>
        </div>
    </div>

    <input type="hidden" name="is_discount" value="<?= $discount ? 1 : 0?>" />

    <div class="category-filter__block
    <?= $search || $discount ? '' : 'none' ?>
    ">
        <div class="category-filter__block__title">Категория</div>
        <div class="category-filter__block__form">
            <?php foreach($category as $item) { ?>
                <label class="checkbox" <?php if ($item->slug == 'discount' && ($search || $discount)) echo 'style="display:none;"';?>>
                    <input name="category" type="radio"
                        <?= $item->ms_id == $cat_ms_id ? 'checked="checked"' : '' ?>
                           value="<?= $item->slug ?>"
                    />
                    <i></i> <?= $item->title ?></label>
            <?php } ?>
        </div>
    </div>
    <?php /* if (false && !empty($filterTypeEq)) { ?>
    <div class="category-filter__block">
        <div class="category-filter__block__title">Тип оборудования</div>
        <div class="category-filter__block__form">
            <?php foreach($filterTypeEq as $item) { ?>
                <label class="checkbox"><input name="type_eq" type="checkbox"
                                               value="<?= $item->id ?>"
                    /><i></i> <?= $item->title ?></label>
            <?php } ?>
        </div>
    </div>
    <?php } */ ?>
    <?php if (!empty($filterBrands) && !$search && !$discount) { ?>
    <div class="category-filter__block">
        <div class="category-filter__block__title">Бренд</div>
        <div class="category-filter__block__form">
            <?php foreach($filterBrands as $item) { ?>
                <label class="checkbox">
                    <input name="brand"
                           type="checkbox"
                           value="<?= $item->slug ?>"
                           <?php if (isset($arFilter['brand']) && in_array($item->slug, $arFilter['brand'])) echo 'checked'?>
                    /><i></i> <?= $item->title ?>
                </label>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php if (!empty($filterManufacturer) && !$search && !$discount) { ?>
    <div class="category-filter__block">
        <div class="category-filter__block__title">Страна произв.</div>
        <div class="category-filter__block__form">
            <?php foreach($filterManufacturer as $item) { ?>
                <?php if($item->manufacturer) { ?>
                    <label class="checkbox">
                        <input name="manufacturer"
                               type="checkbox"
                               value="<?= $item->manufacturer ?>"
                               data-value="<?= Inflector::slug($item->manufacturer) ?>"
                                <?php if (isset($arFilter['manufacturer']) && in_array($item->manufacturer, $arFilter['manufacturer'])) echo 'checked'?>
                        /><i></i> <?= $item->manufacturer ?>
                    </label>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php if (!$search && !$discount):?>
        <?php foreach($filtersCommon as $key => $fc) { ?>
            <?php if(!empty($fc->getPubFiltersByCatId($cat_ms_id))) { ?>
                <div class="category-filter__block">
                    <div class="category-filter__block__title"><?= $fc->title ?></div>
                    <div class="category-filter__block__form">
                        <?php foreach($fc->getPubFiltersByCatId($cat_ms_id) as $key1 => $item) { ?>
                            <label class="checkbox">
                                <input name="filter_id[<?=$key?>][]"
                                       type="checkbox"
                                       value="<?= $item->id ?>"
                                       <?php if (isset($arFilter['filters']) && in_array($item->id, $arFilter['filters'][$item->category_id])) echo 'checked'?>
                                /><i></i> <?= $item->title ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php endif;?>

    <a href="#clear-filter" class="btn js-reset-filter">Очистить фильтр</a>
</div>
