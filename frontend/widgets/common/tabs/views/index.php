<?php
/**
* @var $tabs
 */
?>
<?php if ( !empty($tabs) ) { ?>
<div class="tabs">
    <div class="tabs-nav">
        <?php foreach($tabs as $tab) { $tab = (object) $tab ?>
            <?php if(!(isset($tab->disable) && $tab->disable)) { ?>
                <a href="#<?= $tab->id ?>" <?= isset($tab->active) && $tab->active ? 'class="-act"' : '' ?>>
                    <?= $tab->label ?>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="tabs-content">
        <?php foreach($tabs as $tab) { $tab = (object) $tab ?>
            <?php if(!(isset($tab->disable) && $tab->disable)) { ?>
                <div class="tabs-content__item <?= isset($tab->active) && $tab->active ? '-open' : '' ?>" id="<?= $tab->id ?>">
                    <?= $tab->content ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php } ?>
