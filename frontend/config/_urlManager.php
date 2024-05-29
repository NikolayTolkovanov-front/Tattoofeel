<?php

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'suffix' => '/',
    'normalizer' => [
        'class' => 'yii\web\UrlNormalizer',
        'normalizeTrailingSlash' => true,
        'collapseSlashes' => true,
    ],
    'rules' => [

        // Lk
        ['pattern' => 'lk', 'route' => 'lk/default'],
        ['pattern' => 'lk/<action>', 'route' => 'lk/default/<action>'],

        // API
        [
            'pattern' => 'vue-api',
            'route' => 'api/default/docs'
        ],[
            'pattern' => 'vue-api/<controller:[\w-]+>/<action:[\w-]+>',
            'route' => 'api/<controller>/<action>'
        ],

        //Site
        ['pattern' => 'search', 'route' => 'site/search'],
        ['pattern' => 'show-reviews-form', 'route' => 'site/show-reviews-form'],
        ['pattern' => 'send-review', 'route' => 'site/send-review'],
        ['pattern' => 'show-buy-one-click-form', 'route' => 'site/show-buy-one-click-form'],
        ['pattern' => 'send-buy-one-click', 'route' => 'site/send-buy-one-click'],
        ['pattern' => 'show-buy-one-click-cart-form', 'route' => 'site/show-buy-one-click-cart-form'],
        ['pattern' => 'send-buy-one-click-cart', 'route' => 'site/send-buy-one-click-cart'],
        ['pattern' => 'show-not-found-search-form', 'route' => 'site/show-not-found-search-form'],
        ['pattern' => 'send-not-found-search', 'route' => 'site/send-not-found-search'],
        ['pattern' => 'show-pay-form', 'route' => 'site/show-pay-form'],
        ['pattern' => 'change-pay', 'route' => 'site/change-pay'],
        ['pattern' => 'tinkoff-notification', 'route' => 'site/tinkoff-notification'],
        ['pattern' => 'send-notification-to-admin', 'route' => 'site/send-notification-to-admin'],
        ['pattern' => 'pay-card', 'route' => 'site/pay-card'],

        // Brands
        ['pattern' => 'brands', 'route' => 'brands/index'],
        ['pattern' => 'brands/<slug>', 'route' => 'brands/detail'],

        // News
        ['pattern' => 'news', 'route' => 'news/index'],
        ['pattern' => 'news/<slug>', 'route' => 'news/detail'],

        // Article
        ['pattern' => 'article', 'route' => 'article/index'],
        ['pattern' => 'article/<slug>', 'route' => 'article/detail'],

        // Stock
        ['pattern' => 'stock', 'route' => 'stock/index'],
        ['pattern' => 'stock/<slug>', 'route' => 'stock/detail'],

        //catalog
        ['pattern' => 'catalog', 'route' => 'catalog/index'],
        //['pattern' => 'catalog/discount', 'route' => 'catalog/discount'],
        //['pattern' => 'catalog/filter', 'route' => 'catalog/filter'],
        ['pattern' => 'catalog/search/<slug:[\w_\/-]+>', 'route' => 'catalog/routing'],
        ['pattern' => 'catalog/generate-url', 'route' => 'catalog/generate-url'],
        ['pattern' => 'catalog/deferred', 'route' => 'catalog/deferred'],
        ['pattern' => 'catalog/get-cart', 'route' => 'catalog/get-cart'],
        ['pattern' => 'catalog/add-cart', 'route' => 'catalog/add-cart'],
        ['pattern' => 'catalog/remove-cart', 'route' => 'catalog/remove-cart'],
        ['pattern' => 'catalog/change-cart', 'route' => 'catalog/change-cart'],
        ['pattern' => 'catalog/add-cart-configs', 'route' => 'catalog/add-cart-configs'],
        ['pattern' => 'catalog/add-products-ecommerce', 'route' => 'catalog/add-products-ecommerce'],
        ['pattern' => 'catalog/remove-products-ecommerce', 'route' => 'catalog/remove-products-ecommerce'],
        ['pattern' => 'catalog/change-products-ecommerce', 'route' => 'catalog/change-products-ecommerce'],
        ['pattern' => 'catalog/add-review', 'route' => 'catalog/add-review'],
        ['pattern' => 'catalog/detail/<slugProduct>', 'route' => 'catalog/product'],
        ['pattern' => 'catalog/config/<id>', 'route' => 'catalog/config'],
        //['pattern' => 'catalog/<slug>', 'route' => 'catalog/category'],
        //['pattern' => 'catalog/<slug>/<slugProduct>', 'route' => 'catalog/product'],
        ['pattern' => 'catalog/<slug:[\w_\/-]+>', 'route' => 'catalog/routing'],


        //page
        ['pattern' => 'contact', 'route' => 'page/contact'],
        ['pattern' => '<slug>', 'route' => 'page/view'],

        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'feed/<token:[\w_\/-]+>',
            'route' => 'site/feed',
            'suffix' => '.xml'
        ],

        /*
        // Pages
        ['pattern' => 'page/<slug>', 'route' => 'page/view'],

        // Articles
        ['pattern' => 'article/index', 'route' => 'article/index'],
        ['pattern' => 'article/attachment-download', 'route' => 'article/attachment-download'],
        ['pattern' => 'article/<slug>', 'route' => 'article/view'],

        // Sitemap
        ['pattern' => 'sitemap.xml', 'route' => 'site/sitemap', 'defaults' => ['format' => Sitemap::FORMAT_XML]],
        ['pattern' => 'sitemap.txt', 'route' => 'site/sitemap', 'defaults' => ['format' => Sitemap::FORMAT_TXT]],
        ['pattern' => 'sitemap.xml.gz', 'route' => 'site/sitemap', 'defaults' => ['format' => Sitemap::FORMAT_XML, 'gzip' => true]],
        */
    ]

];
