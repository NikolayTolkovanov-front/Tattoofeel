<template>
  <div class="small-product-item">
    <img
      class="small-product-item__img"
      src="/img/catalog/product_1.png"
      alt=""
    />
    <div class="small-product-item__information">
      <div href="#" class="small-product-item__name">
        Картриджи V-Select Round ...
      </div>
      <div class="small-product-item__price small-product-item__price--old">
        1950 ₽
      </div>
      <div class="small-product-item__price small-product-item__price--new">
        1 850 ₽
      </div>
    </div>
    <button @click="addProduct(4590, 1)" class="small-product-item__basket-button">
      <basketIcon></basketIcon>
    </button>
  </div>
</template>
<script setup>
import basketIcon from "@/components/Icons/basketIcon.vue";

import { useCartStore } from "@/store/cart";
const { addProduct } = useCartStore();
</script>
<style lang="scss">
.small-product-item {
  max-width: 127px;
  box-shadow: 0px 5px 13px 0px #8898aa26;
  position: relative;
  &__img {
    width: 100%;
  }

  &__name {
    display: inline-block;
    font-size: 13px;
    line-height: 16px;
    color: $main-font-color;
    text-decoration: none;
  }
  &__price--old {
    margin-top: 6px;
    font-size: 13px;
    font-weight: 300;
    line-height: 16px;
  }

  &__price--new {
    font-size: 16px;
    font-weight: 600;
    line-height: 19px;
  }
  &__information {
    padding: 8px;
  }
  &__basket-button {
    display: none;
    height: 50px;
    width: 67px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 0;
    border: none;
    background: $yellow;
    cursor: pointer;
    &:hover {
      background: $yellow-hover;
    }
    &:active {
      background: $yellow-active;
    }
    svg {
      width: 35px;
      transform: translateX(-2px) translateY(4px);
    }
  }
  &:hover {
    .small-product-item__basket-button {
      display: block;
    }
  }
}
</style>
