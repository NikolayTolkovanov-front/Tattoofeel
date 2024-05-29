<template>
  <table class="basket-table" v-if="getCartProducts?.length">
    <tr class="basket-table__row basket-table__row--header">
      <th class="basket-table__cell basket-table__cell--name-header">
        Наименование товара
      </th>
      <th class="basket-table__cell">Кол-во</th>
      <th class="basket-table__cell">Сумма</th>
      <th class="basket-table__cell"></th>
      <th class="basket-table__cell"></th>
    </tr>
    <tr
      class="basket-table__row"
      v-for="product in getCartProducts"
      :key="product.id"
    >
      <td class="basket-table__cell basket-table__cell--name">
        <!-- <img class="basket-table__image" :src="row.img" /> -->
        <div class="basket-table__information">
          <span class="basket-table__name">{{ product.title }}</span>
          <span class="basket-table__index">Арт. {{ product.id }}</span>
          <div class="basket-table__information-mobile">
            <RemoveButton></RemoveButton>
            <div class="basket-table__summ">
              {{ product.price * product.count }} ₽
            </div>
            <NumberCounted v-model="product.count"></NumberCounted>
            <!-- <FavoriteButton v-model="product.isFavorite"></FavoriteButton> -->
          </div>
        </div>
      </td>
      <td class="basket-table__cell basket-table__cell--count">
        <NumberCounted
          v-model="product.count"
          @minusProduct="minusProduct(product.id, product.count, '')"
          @plusProduct="plusProduct(product.id, product.count, '')"
        ></NumberCounted>
      </td>
      <td class="basket-table__cell">
        <div class="basket-table__summ">
          {{ (product.price * product.count).toLocaleString("ru-RU") }} ₽
        </div>
      </td>
      <td class="basket-table__cell">
        <!-- <FavoriteButton v-model="row.isFavorite"></FavoriteButton> -->
      </td>
      <td class="basket-table__cell basket-table__cell--remove">
        <RemoveButton @removeProduct="removeProduct(product.id, '')"></RemoveButton>
      </td>
    </tr>
  </table>
  <p v-else>В корзине нет товаров</p>
</template>
<script setup>
import NumberCounted from "./NumberCounted.vue";
// import FavoriteButton from "./FavoriteButton.vue";
import RemoveButton from "./RemoveButton.vue";
import { onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useCartStore } from "@/store/cart";
const { updateCart, plusProduct, minusProduct, removeProduct } = useCartStore();
const { getCartProducts } = storeToRefs(useCartStore());

onMounted(async () => {
  await updateCart().catch((error) => console.log("errror in CartTable:", error));
});
</script>
<style lang="scss">
.basket-table {
  width: 100%;
  border-spacing: 0px;
  &__row {
    display: flex;
    flex-direction: column;
    position: relative;
    margin-top: 16px;
    &--header {
      display: none;
    }
  }
  &__image {
    height: 108px;
    width: 108px;
    border: 1px solid $lightGrayD;
    object-fit: contain;
  }

  &__cell {
    display: none;
    padding: 11px 6px;
    &--name {
      display: flex;
      gap: 16px;
    }
  }

  &__information-mobile {
    display: flex;
    flex-direction: column;
    .favorite-button {
      position: absolute;
      right: 0;
      top: 0;
    }
    .remove-button {
      position: absolute;
      right: 0;
      bottom: 0;
    }
    .basket-table__summ {
      margin-bottom: 8px;
    }
  }
  &__summ {
    white-space: nowrap;
  }
  &__name {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 4px;
    max-width: 138px;
  }
  &__index {
    display: inline-block;
    font-weight: 400;
    margin-bottom: 4px;
  }
}
@media (min-width: $tablet) {
  .basket-table {
    &__row {
      flex-direction: row;
      gap: 20px;
    }

    &__cell {
      &--count {
        display: block;
      }
    }
    &__information-mobile {
      .number-counter {
        display: none;
      }
    }
  }
}
@media (min-width: $laptop) {
  .basket-table {
    &__row {
      display: table-row;
      margin-top: 0;
    }
    &__row--header {
      font-weight: 700;
      height: 34px;
      th {
        background: #fbfbfb;
      }
    }
    &__cell {
      display: table-cell;
      text-align: center;
      &--name {
        display: flex;
        align-items: center;
        justify-content: center;
      }
      &--name-header {
        width: 410px;
      }
    }
    &__image {
      height: 56px;
      width: 70px;
      border: 1px solid $lightGrayD;
      object-fit: contain;
    }

    &__information {
      text-align: start;
    }
    &__information-mobile {
      display: none;
    }

    &__name {
      max-width: unset;
    }
    &__index {
      text-align: start;
    }
  }
}
</style>
