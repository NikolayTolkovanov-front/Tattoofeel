<template>
  <div class="payment-card" :class="{ 'payment-card--active': isActive }">
    <slot></slot>
  </div>
</template>
<script setup>
import { defineProps } from "vue";
defineProps(["isActive"]);
</script>
<style lang="scss">
.payment-card {
  width: 100%;
  padding: 11px 16px 13px;
  line-height: 22px;
  box-sizing: border-box;
  border-radius: 12px;
  border: 1px solid #d3d4dd;
  cursor: pointer;
  &--active {
    border: 1px solid $yellow;
  }
  &:hover {
    border: 1px solid $yellow-hover;
  }
}
</style>
