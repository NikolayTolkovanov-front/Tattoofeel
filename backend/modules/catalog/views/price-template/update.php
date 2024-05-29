<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\ProductPriceTemplate
 * @var $categories common\models\ProductPriceTemplate[]
 */

$this->title = t_b('Редактировать скидку: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Цены (скидки)'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
]) ?>
