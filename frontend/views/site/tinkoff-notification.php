<?php
/* @var $this yii\web\View */
/* @var $tinkoffRequest */
/* @var $msg */

$this->title = Yii::$app->params['title'];

$this->params['breadcrumbs'][] = ['label' => $model->title];

?>

<section>
    <div class="container" style="margin-top:25px">
        <h1><?=$msg?></h1>
        <pre>
            <?php print_r($tinkoffRequest);?>
        </pre>
    </div>
</section>
