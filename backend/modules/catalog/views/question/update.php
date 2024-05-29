<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\TypeEq
 * @var $categories common\models\TypeEq[]
 */

$this->title = t_b('Редактировать вопрос: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Вопросы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
