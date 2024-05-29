<?php
/**
 * @var $imgUrl
 * @var $title
 * @var $body
 * @var $body_short
 * @var $more_link_full_text
 * @var $more_link_short_text
 *
 */

$subdomainInfo = Yii::$app->subdomains->get();

use frontend\widgets\common\seoH1\SeoH1; ?>

<article class="articles__item" style="margin-bottom:0">
    <div class="articles__item__pict">
        <div style="background-image:url(<?= $imgUrl ?>);background-size: 80% auto;"></div>
    </div>
    <div class="articles__item__desc">
        <h1 class="h1" style="margin-top: 0">
            <?=SeoH1::widget([
                'seoH1' => $title,
                'subdomainInfo' => $subdomainInfo,
            ])?>
        </h1>
        <div class="articles__item__desc__short-text block-typo">
            <?= $body_short ?></div>
        <?php if( !empty( strip_tags($body) ) ) { ?>
            <div class="articles__item__desc__full-text block-typo">
                <?= $body ?>
            </div>
            <br />
            <div class="articles__item__more">
                <a class="btn _gray js-article-full-text"
                   href="#"
                   data-full-text-label="<?= $more_link_full_text ?>"
                   data-short-text-label="<?= $more_link_short_text ?>"
                ><?= $more_link_short_text ?></a>
            </div>
        <?php } ?>
    </div>
</article>
