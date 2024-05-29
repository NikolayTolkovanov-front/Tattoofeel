<template>
  <a class="remove-button" @click="emit('removeProduct')"> </a>
</template>
<script setup>
import { defineEmits } from "vue";
const emit = defineEmits(["removeProduct"]);
</script>
<style lang="scss">
.remove-button {
  display: inline-block;
  background: $yellow;
  height: 22px;
  width: 22px;
  position: relative;
  cursor: pointer;
  &:hover {
    background: $yellow-hover;
  }
  &:active {
    background: $yellow-active;
  }
  &::after,
  &::before {
    position: absolute;
    content: " ";
    top: 10px;
    left: 3px;
    width: 16px;
    height: 1px;
    background: #363636;
    transform: rotate(45deg);
  }
  &::after {
    transform: rotate(-45deg);
  }
}
</style>
