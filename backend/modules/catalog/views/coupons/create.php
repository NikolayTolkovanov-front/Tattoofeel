<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Coupons
 * @var $brands     common\models\Brand[]
 * @var $categories common\models\ProductCategory[]
 */

$this->title = Yii::t('backend', 'Добавить купон');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Каталог'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
    'brands' => $brands,
    'categories' => $categories,
]) ?>
