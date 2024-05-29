<?php /**
 * @var $filterBrands common\models\Brand
*/
use yii\helpers\Url;

$this->params['slug'] =
    isset(Yii::$app->controller->actionParams['slug']) ?
        Yii::$app->controller->actionParams['slug'] : null;
?>

<div class="category-filter">
    <div class="category-filter__block js-catalog-filter -open-laptop">
        <div class="category-filter__block__title">Бренды</div>
        <div class="category-filter__block__form">
            <?php foreach ($filterBrands as $brand) { ?>
                <a href="<?= Url::to(['/brands/'.$brand->slug]) ?>"
                   class="link-checkbox <?= $this->params['slug'] == $brand->slug ?
                        ' -act' : '' ?>">
                    <input style="opacity: 0; position: absolute;" value="<?= $brand->slug ?>" type="checkbox" name="brand" <?= $this->params['slug'] == $brand->slug ?
                        'checked' : '' ?>>
                    <i></i><?= $brand->title ?>
                </a>
            <?php }?>
        </div>
    </div>
</div>
