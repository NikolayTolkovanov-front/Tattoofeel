<template>
  <WhiteBlock class="sale-section">
    <h3 class="sale-section__title">Корзина</h3>
    <CartTable></CartTable>
  </WhiteBlock>
</template>
<script setup>
import CartTable from "@/components/Cart/CartTable.vue";
import WhiteBlock from "@/components/WhiteBlock.vue";
</script>
<style lang="scss">
.sale-section__title {
  margin-bottom: 8px;
}
</style>
