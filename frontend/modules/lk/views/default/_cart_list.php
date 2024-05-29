<?php if(!empty($cart->linkProducts)) { ?>
    <tr class="lk-table__head">
        <th class="lk-table-fd-name">Наименование товара</th>
        <th class="lk-table-fd-amount">Кол-во</th>
        <th class="lk-table-fd-sum">Сумма</th>
        <th class="lk-table-fd-def"></th>
        <th class="lk-table-fd-del"></th>
    </tr>
    <?php foreach($cart->linkProducts as $p) { ?>
        <?= $this->render('_cart_item', ['lp' => $p]) ?>
    <?php } ?>
<?php } else { ?>
    <tr class="lk-table__row">
        <td class="lk-table-fd-name _empty">
            Корзина пуста
        </td>
    </tr>
<?php } ?>
