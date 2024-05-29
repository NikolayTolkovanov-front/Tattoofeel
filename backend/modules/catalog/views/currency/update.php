<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Currency
 * @var $categories common\models\Currency[]
 */

$this->title = t_b('Редактировать валюту: ') . ' ' . $model->code_iso;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Валюты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->code_iso;

?>

<?php echo $this->render('_form', [
    'model' => $model,
]) ?>
