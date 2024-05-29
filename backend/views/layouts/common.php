<?php
/**
 * @var $this yii\web\View
 * @var $content string
 */

use backend\assets\BackendAsset;
use backend\modules\system\models\SystemLog;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\log\Logger;
use yii\widgets\Breadcrumbs;

$bundle = BackendAsset::register($this);
Yii::info(Yii::$app->components["i18n"]["translations"]['*']['class'], 'test');

?>

<?php $this->beginContent('@backend/views/layouts/base.php'); ?>

<div class="wrapper">
    <!-- header logo: style can be found in header.less -->
    <header class="main-header">
        <a href="<?php echo Yii::$app->urlManagerFrontend->createAbsoluteUrl('/') ?>" class="logo"
           style="text-align:left;padding:0 0 0 7px;"
        >
            <!-- Add the class icon to your logo image or logo icon to add the margining -->
            <svg width="35" height="35" viewBox="0 0 120 121" fill="#333" xmlns="http://www.w3.org/2000/svg"
                 style="vertical-align: middle">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2.88733 15.9583H8.05032V4.98954H10.961V1.1179H0V4.98954H2.88733V15.9583ZM61.4675 20.9854V105.579L81.7188 105.577V76.4722H100.501V57.274H81.7188V40.1836H102.518V20.9854H61.4675ZM57.3014 105.579V20.9854H16.5469V40.1836H37.0501V105.579H57.3014ZM8.77854 15.9583H13.7062L14.2224 14.6239H18.9636L19.4797 15.9583H24.4085L18.4707 1.1179H14.7164L8.77854 15.9583ZM15.5367 11.3532L16.5935 8.74963L17.6492 11.3532H15.5367ZM25.1123 15.9583H30.2764V4.98954H33.1859V1.1179H22.2261V4.98954H25.1123V15.9583ZM35.2984 15.9583H40.4614V4.98954H43.3721V1.1179H32.4122V4.98954H35.2984V15.9583ZM43.2078 8.54968C43.2078 9.61782 43.4231 10.626 43.8527 11.5752C44.2834 12.5244 44.8707 13.3516 45.6133 14.0556C46.3571 14.7607 47.2285 15.3174 48.2298 15.7247C49.2322 16.133 50.2957 16.3371 51.4224 16.3371C52.5491 16.3371 53.6126 16.133 54.6139 15.7247C55.6152 15.3174 56.4877 14.7607 57.2315 14.0556C57.9741 13.3516 58.5602 12.5244 58.9909 11.5752C59.4216 10.626 59.637 9.61782 59.637 8.54968C59.637 7.4668 59.4216 6.45443 58.9909 5.51256C58.5602 4.5707 57.9741 3.74775 57.2315 3.04267C56.4877 2.33864 55.6152 1.78194 54.6139 1.37468C53.6126 0.966365 52.5491 0.762207 51.4224 0.762207C50.2957 0.762207 49.2322 0.966365 48.2298 1.37468C47.2285 1.78194 46.3571 2.33864 45.6133 3.04267C44.8707 3.74775 44.2834 4.5707 43.8527 5.51256C43.4231 6.45443 43.2078 7.4668 43.2078 8.54968ZM48.1365 8.54968C48.1365 8.11926 48.222 7.71515 48.3941 7.3363C48.5673 6.95851 48.8015 6.62807 49.099 6.34709C49.3965 6.06505 49.7439 5.84195 50.1436 5.67884C50.5421 5.51572 50.9684 5.43469 51.4224 5.43469C51.8764 5.43469 52.3027 5.51572 52.7012 5.67884C53.0997 5.84195 53.4483 6.06505 53.7458 6.34709C54.0433 6.62807 54.2775 6.95851 54.4496 7.3363C54.6217 7.71515 54.7082 8.11926 54.7082 8.54968C54.7082 8.98009 54.6217 9.3842 54.4496 9.762C54.2775 10.1398 54.0433 10.4702 53.7458 10.7523C53.4483 11.0343 53.0997 11.2563 52.7012 11.4195C52.3027 11.5826 51.8764 11.6647 51.4224 11.6647C50.9684 11.6647 50.5421 11.5826 50.1436 11.4195C49.7439 11.2563 49.3965 11.0343 49.099 10.7523C48.8015 10.4702 48.5673 10.1398 48.3941 9.762C48.222 9.3842 48.1365 8.98009 48.1365 8.54968ZM60.575 8.54968C60.575 9.61782 60.7904 10.626 61.2211 11.5752C61.6507 12.5244 62.2379 13.3516 62.9806 14.0556C63.7243 14.7607 64.5969 15.3174 65.5982 15.7247C66.5994 16.133 67.6629 16.3371 68.7896 16.3371C69.9164 16.3371 70.9798 16.133 71.9811 15.7247C72.9824 15.3174 73.8549 14.7607 74.5987 14.0556C75.3414 13.3516 75.9286 12.5244 76.3582 11.5752C76.7889 10.626 77.0043 9.61782 77.0043 8.54968C77.0043 7.4668 76.7889 6.45443 76.3582 5.51256C75.9286 4.5707 75.3414 3.74775 74.5987 3.04267C73.8549 2.33864 72.9824 1.78194 71.9811 1.37468C70.9798 0.966365 69.9164 0.762207 68.7896 0.762207C67.6629 0.762207 66.5994 0.966365 65.5982 1.37468C64.5969 1.78194 63.7243 2.33864 62.9806 3.04267C62.2379 3.74775 61.6507 4.5707 61.2211 5.51256C60.7904 6.45443 60.575 7.4668 60.575 8.54968ZM65.5038 8.54968C65.5038 8.11926 65.5904 7.71515 65.7624 7.3363C65.9345 6.95851 66.1687 6.62807 66.4662 6.34709C66.7637 6.06505 67.1112 5.84195 67.5108 5.67884C67.9093 5.51572 68.3356 5.43469 68.7896 5.43469C69.2437 5.43469 69.6699 5.51572 70.0685 5.67884C70.4681 5.84195 70.8155 6.06505 71.113 6.34709C71.4105 6.62807 71.6448 6.95851 71.8168 7.3363C71.9889 7.71515 72.0755 8.11926 72.0755 8.54968C72.0755 8.98009 71.9889 9.3842 71.8168 9.762C71.6448 10.1398 71.4105 10.4702 71.113 10.7523C70.8155 11.0343 70.4681 11.2563 70.0685 11.4195C69.6699 11.5826 69.2437 11.6647 68.7896 11.6647C68.3356 11.6647 67.9093 11.5826 67.5108 11.4195C67.1112 11.2563 66.7637 11.0343 66.4662 10.7523C66.1687 10.4702 65.9345 10.1398 65.7624 9.762C65.5904 9.3842 65.5038 8.98009 65.5038 8.54968ZM78.3419 15.9583H83.3406V10.3513H87.4945V6.65858H83.3406V5.01164H87.5656V1.1179H78.3419V15.9583ZM88.739 15.9583H98.1269V12.0646H93.7377V10.3513H97.8916V6.65858H93.7377V5.01164H97.9626V1.1179H88.739V15.9583ZM99.6056 15.9583H108.992V12.0646H104.604V10.3513H108.758V6.65858H104.604V5.01164H108.829V1.1179H99.6056V15.9583ZM110.471 15.9583H120V12.1098H115.635V1.1179H110.471V15.9583ZM5.55819 19.9625V115.046H114.454V19.9625H120V119.72V120.305H119.384H0.628307H0.0122109V119.72L0 19.9625H5.55819Z"/>
            </svg>
            <span style="margin-left:5px"><?= Yii::$app->name ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only"><?php echo Yii::t('backend', 'Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php /*
                    <li id="timeline-notifications" class="notifications-menu">
                        <a href="<?php echo Url::to(['/timeline-event/index']) ?>">
                            <i class="fa fa-bell"></i>
                            <span class="label label-success">
                                <?php echo TimelineEvent::find()->today()->count() ?>
                            </span>
                        </a>
                    </li>
                    */ ?>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li id="log-dropdown" class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-warning"></i>
                            <span class="label label-danger">
                                <?php echo SystemLog::find()->count() ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><?php echo Yii::t('backend', 'У вас {num} лог(ов)',
                                    ['num' => SystemLog::find()->count()]) ?></li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php foreach (SystemLog::find()->orderBy(['log_time' => SORT_DESC])->limit(5)->all() as $logEntry): ?>
                                        <li>
                                            <a href="<?php echo Yii::$app->urlManager->createUrl([
                                                '/system/log/view',
                                                'id' => $logEntry->id
                                            ]) ?>">
                                                <i class="fa fa-warning <?php echo $logEntry->level === Logger::LEVEL_ERROR ? 'text-red' : 'text-yellow' ?>"></i>
                                                <?php echo $logEntry->category ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li class="footer">
                                <?php echo Html::a(Yii::t('backend', 'View all'), ['/system/log/index']) ?>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle,
                                'img/anonymous.jpg')) ?>"
                                 class="user-image">
                            <span><?php echo Yii::$app->user->identity->username ?> <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header light-blue">
                                <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle,
                                    'img/anonymous.jpg')) ?>"
                                     class="img-circle" alt="User Image"/>
                                <p>
                                    <?php echo Yii::$app->user->identity->username ?>
                                    <small>
                                        <?php echo Yii::t('backend', 'Последний вход {0, date, short}',
                                            Yii::$app->user->identity->created_at) ?>
                                    </small>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Профиль'), ['/sign-in/profile'],
                                        ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Аккаунт'), ['/sign-in/account'],
                                        ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-right">
                                    <?php echo Html::a(Yii::t('backend', 'Выход'), ['/sign-in/logout'],
                                        ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <?php echo Html::a('<i class="fa fa-cogs"></i>', ['/system/settings']) ?>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle,
                        'img/anonymous.jpg')) ?>" class="img-circle"/>
                </div>
                <div class="pull-left info">
                    <p><?php echo Yii::t('backend', 'Привет, {username}',
                            ['username' => Yii::$app->user->identity->getPublicIdentity()]) ?></p>
                    <a href="<?php echo Url::to(['/sign-in/profile']) ?>">
                        <i class="fa fa-circle text-success"></i>
                        <?php echo Yii::$app->formatter->asDatetime(time()) ?>
                    </a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php try {
                echo Menu::widget([
                    'options' => ['class' => 'sidebar-menu tree', 'data' => ['widget' => 'tree']],
                    'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                    'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                    'activateParents' => true,
                    'items' => [
                        [
                            'label' => Yii::t('backend', 'Основной'),
                            'options' => ['class' => 'header'],
                        ],
                        /*
                        [
                            'label' => Yii::t('backend', 'Timeline'),
                            'icon' => '<i class="fa fa-bar-chart-o"></i>',
                            'url' => ['/timeline-event/index'],
                            'badge' => TimelineEvent::find()->today()->count(),
                            'badgeBgClass' => 'label-success',
                        ],
                        */
                        [
                            'label' => Yii::t('backend', 'Клиенты/заказы'),
                            'icon' => '<i class="fa fa-shopping-cart"></i>',
                            'url' => '#',
                            'options' => ['class' => 'treeview'],
                            'active' => Yii::$app->controller->module->id === 'client',
                            //'visible' => Yii::$app->user->can('administrator'),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Клиенты'),
                                    'url' => ['/client/user/index'],
                                    'icon' => '<i class="fa fa-users"></i>',
                                    'active' => Yii::$app->controller->module->id === 'client' &&
                                        Yii::$app->controller->id === 'user',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Заказы'),
                                    'url' => ['/client/orders/index'],
                                    'icon' => '<i class="fa fa-files-o"></i>',
                                    'active' => Yii::$app->controller->module->id === 'client' &&
                                        Yii::$app->controller->id === 'orders',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Отложенные товары'),
                                    'url' => ['/client/deferred/index'],
                                    'icon' => '<i class="fa fa-check"></i>',
                                    'active' => Yii::$app->controller->module->id === 'client' &&
                                        Yii::$app->controller->id === 'deferred',
                                ],
                            ]
                        ],
                        [
                            'label' => Yii::t('backend', 'Каталог'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-barcode"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => 'catalog' === Yii::$app->controller->module->id,
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Синх. продуктов'),
                                    'url' => ['/catalog/products-sync/index'],
                                    'icon' => '<i class="fa fa-refresh"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'products-sync',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Товары'),
                                    'url' => ['/catalog/product/index'],
                                    'icon' => '<i class="fa fa-file-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'product',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Конфигурации'),
                                    'url' => ['/catalog/config/index'],
                                    'icon' => '<i class="fa fa-folder-open-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'config',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Категории'),
                                    'url' => ['/catalog/category/index'],
                                    'icon' => '<i class="fa fa-folder-open-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'category',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Цены (Скидки)'),
                                    'url' => ['/catalog/price-template/index'],
                                    'icon' => '<i class="fa fa-money"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'price-template',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Валюты'),
                                    'url' => ['/catalog/currency/index'],
                                    'icon' => '<i class="fa fa-rub"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'currency',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Вопросы'),
                                    'url' => ['/catalog/question/index'],
                                    'icon' => '<i class="fa fa-question"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'question',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Бренд'),
                                    'url' => ['/catalog/brand/index'],
                                    'icon' => '<i class="fa fa-outdent"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'brand',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Купоны'),
                                    'url' => ['/catalog/coupons/index'],
                                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'coupons',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Отзывы'),
                                    'url' => ['/catalog/reviews/index'],
                                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'reviews',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Тип оборудования'),
                                    'url' => ['/catalog/type-eq/index'],
                                    'icon' => '<i class="fa fa-wrench"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'type-eq',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Фильтры'),
                                    'url' => '#',
                                    'icon' => '<i class="fa fa-wrench"></i>',
                                    'options' => ['class' => 'treeview'],
                                    'active' => in_array(Yii::$app->controller->id,
                                        ['product-filters', 'product-filters-category']),
                                    'items' => [
                                        [
                                            'label' => Yii::t('backend', 'Значения'),
                                            'url' => ['/catalog/product-filters/index'],
                                            'icon' => '<i class="fa fa-circle-o"></i>',
                                            'active' => 'catalog' === Yii::$app->controller->module->id &&
                                                Yii::$app->controller->id === 'product-filters'
                                                && Yii::$app->controller->action->id == 'index',
                                        ],
                                        [
                                            'label' => Yii::t('backend', 'Категории'),
                                            'url' => ['/catalog/product-filters-category/index'],
                                            'icon' => '<i class="fa fa-circle-o"></i>',
                                            'active' => 'catalog' === Yii::$app->controller->module->id &&
                                                Yii::$app->controller->id === 'product-filters-category',
                                        ],
                                        [
                                            'label' => Yii::t('backend', 'Импорт/Экспорт'),
                                            'url' => ['/catalog/product-filters/import'],
                                            'icon' => '<i class="fa fa-circle-o"></i>',
                                            'active' => 'catalog' === Yii::$app->controller->module->id &&
                                                Yii::$app->controller->id === 'product-filters'
                                                && Yii::$app->controller->action->id == 'import'
                                            ,
                                        ],
                                    ]
                                ],
                                [
                                    'label' => Yii::t('backend', 'SEO Мета теги'),
                                    'url' => ['/catalog/seo-meta-tags/index'],
                                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                                    'active' => 'catalog' === Yii::$app->controller->module->id &&
                                        Yii::$app->controller->id === 'seo-meta-tags',
                                ],

                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Контент'),
                            'options' => ['class' => 'header'],
                        ],
                        [
                            'label' => Yii::t('backend', 'Виджеты'),
                            'url' => ['/content/block-widget/index'],
                            'icon' => '<i class="fa fa-file-text"></i>',
                            'active' => Yii::$app->controller->id === 'block-widget',
                        ],
                        [
                            'label' => Yii::t('backend', 'Галерея на главной'),
                            'url' => ['/content/slider-main/index'],
                            'icon' => '<i class="fa fa-image"></i>',
                            'active' => Yii::$app->controller->id === 'slider-main',
                        ],
                        [
                            'label' => Yii::t('backend', 'Статичные страницы'),
                            'url' => ['/content/page/index'],
                            'icon' => '<i class="fa fa-thumb-tack"></i>',
                            'active' => Yii::$app->controller->id === 'page',
                        ],
                        [
                            'label' => Yii::t('backend', 'Новости'),
                            'url' => ['/content/news/index'],
                            'icon' => '<i class="fa fa-newspaper-o"></i>',
                            'active' => Yii::$app->controller->id === 'news',
                        ],
                        [
                            'label' => Yii::t('backend', 'Акции'),
                            'url' => ['/content/stock/index'],
                            'icon' => '<i class="fa fa-gift"></i>',
                            'active' => Yii::$app->controller->id === 'stock',
                        ],
                        [
                            'label' => Yii::t('backend', 'Статьи'),
                            'url' => ['/content/article-n/index'],
                            'icon' => '<i class="fa fa-files-o"></i>',
                            'active' => Yii::$app->controller->id === 'article-n',
                        ],
                        [
                            'label' => Yii::t('backend', 'Ключ/значение (фронт)'),
                            'url' => ['/system/key-storage-app/index'],
                            'icon' => '<i class="fa fa-arrows-h"></i>',
                            'active' => (Yii::$app->controller->id == 'key-storage-app'),
                        ],
                        /*
                        [
                            'label' => Yii::t('backend', 'Статьи'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-files-o"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => 'content' === Yii::$app->controller->module->id &&
                                ('article' === Yii::$app->controller->id || 'category' === Yii::$app->controller->id),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Статьи'),
                                    'url' => ['/content/article/index'],
                                    'icon' => '<i class="fa fa-file-o"></i>',
                                    'active' => Yii::$app->controller->id === 'article',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Категории'),
                                    'url' => ['/content/category/index'],
                                    'icon' => '<i class="fa fa-folder-open-o"></i>',
                                    'active' => Yii::$app->controller->id === 'category',
                                ],
                            ],
                        ],
                        */
                        /*
                        [
                            'label' => Yii::t('backend', 'Widgets'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-code"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => Yii::$app->controller->module->id === 'widget',
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Text Blocks'),
                                    'url' => ['/widget/text/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                    'active' => Yii::$app->controller->id === 'text',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Menu'),
                                    'url' => ['/widget/menu/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                    'active' => Yii::$app->controller->id === 'menu',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Carousel'),
                                    'url' => ['/widget/carousel/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                    'active' => in_array(Yii::$app->controller->id, ['carousel', 'carousel-item']),
                                ],
                            ],
                        ],
                        */
                        /*
                        [
                            'label' => Yii::t('backend', 'Translation'),
                            'options' => ['class' => 'header'],
                            'visible' => Yii::$app->components["i18n"]["translations"]['*']['class'] === \yii\i18n\DbMessageSource::class,
                        ],
                        [
                            'label' => Yii::t('backend', 'Translation'),
                            'url' => ['/translation/default/index'],
                            'icon' => '<i class="fa fa-language"></i>',
                            'active' => (Yii::$app->controller->module->id == 'translation'),
                            'visible' => Yii::$app->components["i18n"]["translations"]['*']['class'] === \yii\i18n\DbMessageSource::class,
                        ],
                        */
                        [
                            'label' => Yii::t('backend', 'Система'),
                            'options' => ['class' => 'header'],
                        ],
                        [
                            'label' => Yii::t('backend', 'Пользователи админки'),
                            'icon' => '<i class="fa fa-user"></i>',
                            'url' => ['/user/index'],
                            'active' => Yii::$app->controller->id === 'user',
                            'visible' => Yii::$app->user->can('administrator'),
                        ],
                        [
                            'label' => Yii::t('backend', 'Разрешенные IP адреса'),
                            'url' => ['/system/admin-ip/index'],
                            'icon' => '<i class="fa fa-thumb-tack"></i>',
                            'active' => Yii::$app->controller->id === 'admin-ip',
                        ],
                        [
                            'label' => Yii::t('backend', 'Поддомены'),
                            'url' => ['/system/subdomains/index'],
                            'icon' => '<i class="fa fa-folder-open-o"></i>',
                            'active' => Yii::$app->controller->id === 'subdomains',
                        ],
                        [
                            'label' => Yii::t('backend', 'Службы доставки'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-delicious"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => in_array(Yii::$app->controller->id, [
                                'pick-point',
                                'cdek',
                                'iml'
                            ]),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'PickPoint'),
                                    'url' => ['/import/pick-point/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],
                                //                            [
                                //                                'label' => Yii::t('backend', 'СДЭК'),
                                //                                'url' => ['/import/cdek/index'],
                                //                                'icon' => '<i class="fa fa-circle-o"></i>',
                                //                            ],
                                [
                                    'label' => Yii::t('backend', 'IML'),
                                    'url' => ['/import/iml/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],

                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Банковские карты'),
                            'url' => ['/system/bank-cards/index'],
                            'icon' => '<i class="fa fa-newspaper-o"></i>',
                            'active' => Yii::$app->controller->id === 'bank-cards',
                        ],
                        [
                            'label' => Yii::t('backend', 'Комиссия'),
                            'url' => ['/system/commission/index'],
                            'icon' => '<i class="fa fa-percent"></i>',
                            'active' => Yii::$app->controller->id === 'commission',
                        ],
                        [
                            'label' => Yii::t('backend', 'Статусы заказов'),
                            'url' => ['/system/order-statuses/index'],
                            'icon' => '<i class="fa fa-outdent"></i>',
                            'active' => Yii::$app->controller->id === 'order-statuses',
                        ],
                        [
                            'label' => Yii::t('backend', 'Шаблоны писем'),
                            'url' => ['/system/email-template/index'],
                            'icon' => '<i class="fa fa-file-text"></i>',
                            'active' => Yii::$app->controller->id === 'email-template',
                        ],
                        [
                            'label' => Yii::t('backend', 'RBAC Правила'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-flag"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => in_array(Yii::$app->controller->id,
                                ['rbac-auth-assignment', 'rbac-auth-item', 'rbac-auth-item-child', 'rbac-auth-rule']),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Соответствие ролей'),
                                    'url' => ['/rbac/rbac-auth-assignment/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Роли и разрешения'),
                                    'url' => ['/rbac/rbac-auth-item/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Наследования'),
                                    'url' => ['/rbac/rbac-auth-item-child/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Правила'),
                                    'url' => ['/rbac/rbac-auth-rule/index'],
                                    'icon' => '<i class="fa fa-circle-o"></i>',
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Файлы'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-th-large"></i>',
                            'options' => ['class' => 'treeview'],
                            'active' => (Yii::$app->controller->module->id == 'file'),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Хранилище'),
                                    'url' => ['/file/storage/index'],
                                    'icon' => '<i class="fa fa-database"></i>',
                                    'active' => (Yii::$app->controller->id == 'storage'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Менеджер'),
                                    'url' => ['/file/manager/index'],
                                    'icon' => '<i class="fa fa-television"></i>',
                                    'active' => (Yii::$app->controller->id == 'manager'),
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Ключ/значение (система)'),
                            'url' => ['/system/key-storage/index'],
                            'icon' => '<i class="fa fa-arrows-h"></i>',
                            'active' => (Yii::$app->controller->id == 'key-storage'),
                        ],
                        [
                            'label' => Yii::t('backend', 'Кеш'),
                            'url' => ['/system/cache/index'],
                            'icon' => '<i class="fa fa-refresh"></i>',
                        ],
                        [
                            'label' => Yii::t('backend', 'Системная информация'),
                            'url' => ['/system/information/index'],
                            'icon' => '<i class="fa fa-dashboard"></i>',
                        ],
                        [
                            'label' => Yii::t('backend', 'Логи'),
                            'url' => ['/system/log/index'],
                            'icon' => '<i class="fa fa-warning"></i>',
                            'badge' => SystemLog::find()->count(),
                            'badgeBgClass' => 'label-danger',
                        ],
//                        [
//                            'label' => Yii::t('backend', 'Импорт'),
//                            'url' => ['/import/pick-point/index'],
//                            'icon' => '<i class="fa fa-image"></i>',
//                        ],
                    ],
                ]);
            } catch (Exception $e) {
                Yii::error($e->getMessage(), 'error');
                echo $e->getMessage();
            } ?>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $this->title ?>
                <?php if (isset($this->params['subtitle'])): ?>
                    <small><?php echo $this->params['subtitle'] ?></small>
                <?php endif; ?>
            </h1>

            <?php try {
                echo Breadcrumbs::widget([
                    'tag' => 'ol',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);
            } catch (Exception $e) {
                echo $e->getMessage();
            } ?>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php if (Yii::$app->session->hasFlash('alert')): ?>
                <?php try {
                    echo Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                } ?>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('reload')): ?>
                <?php
                $reload = Yii::$app->session->getFlash('reload');
                $this->registerJs("
                    try {
                        if ($reload)
                            setInterval(function(){
                                window.location.reload(true);
                            }, $reload)
                    } catch(e) {console.error(e)}
                "); ?>
            <?php endif; ?>
            <?php echo $content ?>
        </section><!-- /.content -->
    </div><!-- /.right-side -->

    <footer class="main-footer">
        <strong>&copy; <?= Yii::$app->name ?> <?php echo date('Y') ?></strong>
        <div class="pull-right"><?= Yii::powered() ?></div>
    </footer>
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>
