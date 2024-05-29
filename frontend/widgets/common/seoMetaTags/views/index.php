<?php

/**
 * @var string|null $seoUrl
 * @var $seoTitle
 * @var $seoDescription
 * @var $seoKeywords
 */

global $has_seo_desc;
$has_seo_desc = true;

$this->title = $seoTitle;
$this->registerMetaTag(['name' => 'description', 'content' => $seoDescription], 'description');
$this->registerMetaTag(['name' => 'keywords', 'content' => $seoKeywords], 'keywords');

if ($seoUrl) {
    $this->registerLinkTag([
        'rel' => 'canonical',
        'href' => sprintf("https://%s%s", Yii::$app->request->hostName, $seoUrl)
    ]);
}
