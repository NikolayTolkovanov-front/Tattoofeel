<?php use yii\helpers\Url;

if (
!(
    Yii::$app->controller->id == 'site' &&
    Yii::$app->controller->action->id == 'index'
) && !empty($this->params['breadcrumbs'])
) { ?>
    <div class="container">
        <ul class="breadcrumbs <?= isset($this->params['breadcrumbsClass'])?$this->params['breadcrumbsClass']:'' ?>" itemscope="" itemtype="http://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                <a href="<?= Url::home() ?>" itemprop="item"><span itemprop="name">Главная</span></a>
                <meta itemprop="position" content="1">
            </li>
            <?php foreach($this->params['breadcrumbs'] as $key => $item) { ?>
                <?php if(isset($item['url'])) {?>
                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <a href="<?= $item['url'] ?>" itemprop="item"><span itemprop="name"><?= $item['label'] ?></span></a>
                        <meta itemprop="position" content="<?=$key + 2?>">
                    </li>
                <?php } else { ?>
                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <span><?= $item['label'] ?></span>
                        <a style="display: none;" href="<?= $item['url'] ?>" itemprop="item"><span itemprop="name"><?= $item['label'] ?></span></a>
                        <meta itemprop="position" content="<?=$key + 2?>">
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
