<template>
  <div class="product-item">
    <img
      class="product-item__img"
      src="https://tattoofeel.ru/storage/source/1/E_8_Vm1XGlSQMnFOJZt4EUhcZWf6Egw3.webp"
      alt=""
    />
    <h3 class="product-item__name">Ambition Soldier</h3>
    <p class="product-item__subtitle">Беспроводная машина “Pen”</p>
    <span class="product-item__price">15 300 ₽</span>
    <vButton @click="addProduct(4590, 1)" type="primary" class="product-item__button">
      <span class="product-item__label"> В корзину </span>
      <basketIcon class="product-item__icon"></basketIcon>
    </vButton>
  </div>
</template>
<script setup>
import vButton from "@/components/vButton.vue";
import basketIcon from "@/components/Icons/basketIcon.vue";

import { useCartStore } from "@/store/cart";
const { addProduct } = useCartStore();

</script>
<style lang="scss">
.product-item {
  width: fit-content;
  outline: none;
  background: #fff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
  box-sizing: border-box;
  padding: 20px;
  text-align: center;
  position: relative;
  z-index: 3;
  overflow: hidden;

  &__img {
    width: 200px;
  }

  &__name {
    height: 78px;
    vertical-align: middle;
    font-size: 18px;
    line-height: 78px;
    margin-bottom: 12px;
    font-weight: 900;
    overflow: hidden;
    text-align: center;
    width: 100%;
  }

  &__subtitle {
    display: block;
    color: #888;
    overflow: hidden;
    margin: 0;
    margin-bottom: 12px;
    margin: 16px 15%;
  }

  &__price {
    display: inline-block;
    font-size: 18px;
    font-weight: 700;
    width: 100%;
    line-height: 16px;
    margin-bottom: 12px;
  }

  &__icon {
    height: 30px;
    width: 30px;
  }
  &__button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 0;
    fill: $main-font-color;
  }
}
</style>
