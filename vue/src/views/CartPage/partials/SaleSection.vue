<template>
  <WhiteBlock class="sale-section">
    <h3 class="sale-section__title">Акции и скидки</h3>
    <ul class="sale-section__list">
      <li><CodeBlock></CodeBlock></li>
    </ul>
  </WhiteBlock>
</template>
<script setup>
import CodeBlock from "@/components/Cart/CodeBlock.vue";
import WhiteBlock from "@/components/WhiteBlock.vue";
</script>
<style lang="scss">
.sale-section {
  &__title {
    margin-bottom: 16px;
  }
  &__list {
    display: flex;
    margin: 0;
    gap: 12px 12px;
    flex-wrap: wrap;
  }
}
</style>
