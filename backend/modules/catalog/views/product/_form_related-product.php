<?php
/**
 * @var $id
 * @var $title
 */
?>
<div class="op-row" id="<?= $id ?>">
    <div class="op-col-name">
        <h4><span class="op-name"><?= $title ?></span></h4>
        <input class="op-hid" name="Product[productRelated][]" type="hidden" value="<?= $id ?>" />
    </div>
    <div class="op-col-del">
        <a href="#" class="op-del btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
    </div>
</div>
