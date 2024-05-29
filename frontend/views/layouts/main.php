<?php
/* @var $this \yii\web\View */

/* @var $content string */
$this->beginContent('@frontend/views/layouts/base.php');
?>

<div class="main" <?php if (strstr(Yii::$app->request->url, '/lk/cart')) : ?>id="app"<?php endif ?>>
    <?= $content ?>
</div>

<!-- Facebook Pixel Code -->
<script>
    // !function(f,b,e,v,n,t,s)
    // {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    //     n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    //     if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    //     n.queue=[];t=b.createElement(e);t.async=!0;
    //     t.src=v;s=b.getElementsByTagName(e)[0];
    //     s.parentNode.insertBefore(t,s)}(window, document,'script',
    //     'https://connect.facebook.net/en_US/fbe...s.js');
    // fbq('init', '1074937982947506');
    // fbq('track', 'PageView');
</script>
<!--<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1074937...ipt=1";/></noscript>-->
<!-- End Facebook Pixel Code -->
<!-- Top.Mail.Ru counter -->
<script type="text/javascript">
    var _tmr = window._tmr || (window._tmr = []);
    _tmr.push({id: "3391442", type: "pageView", start: (new Date()).getTime()});
    (function (d, w, id) {
        if (d.getElementById(id)) return;
        var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
        ts.src = "https://top-fwz1.mail.ru/js/code.js";
        var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
        if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
    })(document, window, "tmr-code");
</script>
<noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3391442;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
<!-- /Top.Mail.Ru counter -->
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(73251517, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/73251517" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    window.onReadyState = (e, t) => {
        const a = ["loading", "interactive", "complete"],
            o = a.slice(a.indexOf(e)),
            n = () => o.includes(document.readyState);
        n() ? t() : document.addEventListener("readystatechange", (() => n() && t()))
    }
    window.onReadyState("complete",function(){
        //product slider
        $('.product-row:not(.-init)').each(function(t_i){
            if( !$(this).hasClass('-init') ) {
                $(this).addClass('-init');

                var NTH_OFFSET_LG_BY2 = 0.05;
                var NTH_OFFSET_BY2 = 0.08;

                var t = $(this);

                var $prev = t.find('.product-row__prev');
                var $next = t.find('.product-row__next');
                var $dots = t.find('.product-row__dots');
                var $container = t.find('.product-row-list');

                var count = 0;
                var by_item = 4;//4,3,2
                var nth_offset = NTH_OFFSET_LG_BY2;
                var last_device = null;

                function init() {
                    for (var i = 0; i < Math.ceil($container.find('>*').length/4); i++)
                        $dots.append('<li />');
                    $dots.find('li').eq(0).addClass('-act');
                }

                function move() {
                    $container.css('transform', 'translate(-' +
                        count * t.width() * (1 + nth_offset)
                        + 'px)');
                    dotsAct();
                    arwDis();
                }

                function divide_item() {
                    var $newCon1 = [];
                    var $newCon2 = [];

                    $container.eq(0).find(' > *').each(function(i){
                        if (i % 2 == 0)
                            $newCon1.push($(this));
                        else
                            $newCon2.push($(this));
                    });

                    $container.eq(0).empty();
                    $container.eq(1).empty();
                    $container.eq(0).append($newCon1);
                    $container.eq(1).append($newCon2);
                }

                function join_item() {
                    var $newCon = [];

                    $container.eq(0).find(' > *').each(function(i){
                        $newCon.push($(this));
                        $newCon.push($container.eq(1).find(' > *').eq(i));
                    });

                    $container.eq(0).empty();
                    $container.eq(1).empty();
                    $container.eq(0).html($newCon);
                }

                function getDevice() {
                    var ww = $(window).width();

                    if (ww < 576) return 'mob';
                    if (ww < 725) return 'mob-lg';
                    if (ww < 992) return 'tab';
                    if (ww < 1200) return 'laptop';

                    return 'laptop-lg';
                }

                function dotsAct() {
                    $dots.find('li').removeClass('-act');
                    $dots.find('li').eq( count ).addClass('-act');
                }

                function arwDis() {
                    $prev.removeClass('-dis');
                    $next.removeClass('-dis');
                    if (count <= 0) $prev.addClass('-dis');
                    if (count >= Math.ceil($container.find('>*').length / by_item) - 1)
                        $next.addClass('-dis');
                }

                function init_event() {
                    $dots.find('li').on('click', function(){
                        count = $dots.find('li').index($(this));
                        move();
                    })

                    $prev.on('click', function(){
                        if (count > 0) {
                            count--;
                            move();
                        }
                    })

                    $next.on('click', function(){
                        if (Math.ceil($container.find('>*').length / by_item) > count + 1) {
                            count++;
                            move();
                        }
                    })

                    $(window)
                        .off('resize.product-row-slider-' + t_i)
                        .on('resize.product-row-slider-' + t_i, function(){

                            var device = getDevice();

                            if (device !== last_device) {

                                if (device === 'mob')
                                    divide_item();
                                else if (last_device === 'mob')
                                    join_item();

                                if (device === 'mob-lg') {
                                    by_item = 2;
                                } else if (device.match('mob')) {
                                    by_item = 4;
                                } else if (device === 'tab')
                                    by_item = 3;
                                else if (device.match('laptop'))
                                    by_item = 4;

                                if (device.match('mob'))
                                    nth_offset = NTH_OFFSET_BY2;
                                else
                                    nth_offset = NTH_OFFSET_LG_BY2;

                                count = 0;
                                last_device = device;
                            }

                            move();
                        })

                    $(window).triggerHandler('resize.product-row-slider-' + t_i);
                }

                function init_swipe() {
                    var initialPoint;
                    var finalPoint;

                    t.on('touchstart', function(e) {
                        initialPoint = event.changedTouches[0];
                    });

                    t.on('touchend', function(e) {
                        finalPoint = event.changedTouches[0];
                        var xAbs = Math.abs(initialPoint.pageX - finalPoint.pageX);
                        var yAbs = Math.abs(initialPoint.pageY - finalPoint.pageY);

                        if (xAbs > 20 || yAbs > 20) {
                            if (xAbs > yAbs) {
                                if (finalPoint.pageX < initialPoint.pageX){
                                    $next.trigger('click');
                                }
                                else{
                                    $prev.trigger('click');
                                }
                            }
                            else {
                                if (finalPoint.pageY < initialPoint.pageY){
                                    /*СВАЙП ВВЕРХ*/}
                                else{
                                    /*СВАЙП ВНИЗ*/}
                            }
                        }
                    });
                }

                init();
                init_event();
                init_swipe();
            }
        });
    })

</script>
<?php $this->endContent() ?>
