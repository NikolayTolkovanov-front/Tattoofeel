<?php

use frontend\widgets\common\Icon;
use yii\helpers\Url;

?>
<div class="product-list">

    <div class="product-list__item _has-config">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>
        <!--<span class="product-list__item__price-old">39 474 руб</span>-->
        <span class="product-list__item__price">32 500 руб</span>
        <a class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'arw','width'=>'12px','height'=>'8px',
                'options' => ['stroke'=>"#363636", 'class' => 'icon pArw'],
            ]) ?>
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options' => ['fill'=>"#363636", 'class' => 'icon pCart'],
            ]) ?>
        </a>
    </div>

    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/default.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>

    <div class="product-list__config _c2">

        <div class="product-list__config-inner">

            <div class="product-list__config-inner-inner">

                <div class="product-list__config__list">
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-list__config__button">
                    <a href="#" class="product-list-cart btn">В корзину
                        <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                </div>

            </div>

        </div>

    </div>

    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>

    <div class="product-list__config _c3">
        <div class="product-list__config-inner">
            <div class="product-list__config-inner-inner">

                <div class="product-list__config__list">
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-list__config__button">
                    <a href="#" class="product-list-cart btn">В корзину
                        <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>

    <div class="product-list__config _c4">
        <div class="product-list__config-inner">
            <div class="product-list__config-inner-inner">

                <div class="product-list__config__list">
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="product-list__config__table">
                            <div class="product-list__config__table__head">
                                <div>Объем</div>
                                <div>Цена</div>
                                <div>Кол-во</div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>1/2 унции - 15 мл</div>
                                <div>391 руб</div>
                                <div>
                                    <div class="number">
                                        <span class="number__minus">-</span>
                                        <span class="number__value">0</span><input type="hidden" value="0" />
                                        <span class="number__plus">+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-list__config__button">
                    <a href="#" class="product-list-cart btn">В корзину
                        <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636"]
                        ]) ?>
                    </a>
                </div>

            </div>
        </div>
    </div>



    <div class="product-list__item">
        <span class="product-list__item__sale">-12%</span>
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>
        <!--<span class="product-list__item__price-old">39 474 руб</span>-->
        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>
        <!--<span class="product-list__item__price-old">39 474 руб</span>-->
        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <!-- <span class="product-list__item__sale">-12%</span> -->
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>
    <div class="product-list__item">
        <span class="product-list__item__sale">-12%</span>
        <a href="<?= Url::to(['product']) ?>">
            <span class="product-list__item__img" style="background-image:url('../img/_tmp/product.png')"></span>
            <h3 class="product-list__item__head">Название товара</h3>
        </a>
        <span class="product-list__item__art">Артикул: 0001</span>

        <span class="product-list__item__price">32 500 руб</span>
        <a href="#" class="product-list-cart btn">В корзину
            <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                'options'=>['fill'=>"#363636"]
            ]) ?>
        </a>
    </div>

</div>

<div class="btn-box center">
    <a href="#" class="btn _wide">
        Показать еще
        <?= Icon::widget(['name' => 'more-dots','width'=>'18px','height'=>'18px',
            'options'=>['fill'=>"#363636"]
        ]) ?>
    </a>
</div>
