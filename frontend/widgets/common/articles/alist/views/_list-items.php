<?php
/* @var $dataProvider */
?>

<?php foreach($dataProvider->getModels() as $model) {
    echo $this->render('_item', ['model' => $model]);
} ?>

<?php if (
        $dataProvider->getTotalCount() <=
        ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize
) { ?>
    <div class="none last-page"></div>
<?php } ?>
