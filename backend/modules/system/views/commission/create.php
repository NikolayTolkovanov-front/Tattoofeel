<?php

use common\models\PaymentTypes;

$this->title =t_b( 'Добавить');
$this->params['breadcrumbs'][]  = ['label' =>t_b( 'Комиссия'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'payment_types' => PaymentTypes::find()->all(),
    ]) ?>

</div>
