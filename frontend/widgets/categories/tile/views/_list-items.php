<?php
/* @var $dataProvider */
?>

<?php foreach($dataProvider->getModels() as $model) {
    echo $this->render('_item', ['model' => $model]);
} ?>

<div class="_empty"></div>
<div class="_empty"></div>
<div class="_empty"></div>
<div class="_empty"></div>

<?php if (
        $dataProvider->pagination &&
        $dataProvider->getTotalCount() <=
        ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize
) { ?>
    <div class="none last-page"></div>
<?php } ?>
