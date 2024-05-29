<?php
/**
 * @var $id
 * @var $title
 * @var $price
 * @var $count
 */

if (empty($price))
    $price = (object) [
            'one' => (object) [
                    'floor' => 0,
                    'cent' => 0,
                    'value' => 0
            ],
            'floor' => 0,
            'cent' => 0,
            'cur' => '',
            'cur_cent' => ''
    ];
?>
<div class="op-row" id="<?= $id ?>">
    <div class="op-col-name">
        <h4><span class="op-name"><?= $title ?></span><div class="small">
                <span class="op-price"><?= $price->one->floor ?></span> <span class="op-cur"><?= $price->cur ?></span>
                <span class="op-price-cent"><?= $price->one->cent ?></span> <span class="op-cur-cent"><?= $price->cur_cent ?></span>
            </div></h4>
    </div>
    <div class="op-col-price">
        <span class="op-total"><?= $price->floor ?></span> <span class="op-cur"><?= $price->cur ?></span>
        <span class="op-total-cent"><?= $price->cent ?></span> <span class="op-cur-cent"><?= $price->cur_cent ?></span>
    </div>
    <div class="op-col-count">
        <div class="form-group">
            <input name="UserClientOrder[products][<?= $id ?>]"
                   class="form-control op-count" type="number" value="<?= $count ?>"
                   data-price="<?= $price->one->value ?>" min="1" />
        </div>
    </div>
    <div class="op-col-del">
        <a href="#" class="op-del btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
    </div>
</div>
