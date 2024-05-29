<?php

/* @var $id */
/* @var $linkLoadMore */
/* @var $dataProvider */

?>

<?= $this->render('_list',[
    'id' => $id,
    'linkLoadMore' => $linkLoadMore,
    'dataProvider' => $dataProvider
]) ?>
