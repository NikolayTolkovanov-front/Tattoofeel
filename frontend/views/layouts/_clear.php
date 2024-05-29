<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

frontend\assets\FrontendAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta name="yandex-verification" content="a5631f1b000f5636">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php echo Html::csrfMetaTags() ?>
    <link rel="stylesheet" href="/vue/css/app.css">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <script type="text/javascript">!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src='https://vk.com/js/api/openapi.js?169',t.onload=function(){VK.Retargeting.Init("VK-RTRG-1675718-d6rHL"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script>
    <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1675718-d6rHL" style="position:fixed; left:-999px;" alt=""/></noscript>

    <script type="text/javascript" >
        window.dataLayer = window.dataLayer || [];
    </script>

    <?php if (Yii::$app->user->isGuest):
        if (preg_match('/^\/stock\/(.+)?/', Yii::$app->getRequest()->url)) {
            $quizId = '645d28a279d15b00253a019b';
            $quizAutoOpen = 12;
        } else {
            $quizId = '642b2944533d9b0025d6f74b';
            $quizAutoOpen = 30;
        }?>
        <!-- Marquiz script start -->
        <script>
            (function(w, d, s, o){
                var j = d.createElement(s); j.async = true; j.src = '//script.marquiz.ru/v2.js';j.onload = function() {
                    if (document.readyState !== 'loading') Marquiz.init(o);
                    else document.addEventListener("DOMContentLoaded", function() {
                        Marquiz.init(o);
                    });
                };
                d.head.insertBefore(j, d.head.firstElementChild);
            })(window, document, 'script', {
                    host: '//quiz.marquiz.ru',
                    region: 'eu',
                    id: <?=json_encode($quizId)?>,
                    autoOpen: <?=(int)$quizAutoOpen?>,
                    autoOpenFreq: 'once',
                    openOnExit: false,
                    disableOnMobile: false
                }
            );
        </script>
        <!-- Marquiz script end -->
    <?php endif;?>
</head>
<body id="body">
<?php $this->beginBody() ?>
    <?php echo $content ?>
<?php $this->endBody() ?>
<?php
        global $has_seo_desc;
        if (!isset($has_seo_desc) || $has_seo_desc != true ) {
            foreach(array_merge(
                        [
                            'description' => ['name' => 'description', 'content' => Yii::$app->params['description']],
                            'keywords' => ['name' => 'keywords', 'content' => Yii::$app->params['keywords']]
                        ], \Yii::$app->params['meta']
                    ) as $meta)
                echo $this->registerMetaTag($meta);
        }
?>
<script type="module" src="/vue/js/app.js"></script>
</body>
</html>
<?php $this->endPage() ?>
