<template>
  <button
    class="favorite-button"
    :class="{ 'favorite-button__active': isActive }"
    @click="isActive = !isActive"
  >
    <svg class="icon" width="17px" height="17px" fill="none">
      <use xlink:href="/img/svg/icons.svg#like"></use>
    </svg>
  </button>
</template>
<script setup>
import { ref } from "vue";

const isActive = ref(false);
</script>
<style lang="scss">
.favorite-button {
  stroke: black;
  &:hover {
    stroke: rgb(90, 90, 90);
  }
  &:active {
    stroke: rgb(115, 115, 115);
  }
  &__active {
    svg {
      fill: black;
    }
    &:hover svg {
      fill: rgb(90, 90, 90);
    }
    &:active svg {
      fill: rgb(115, 115, 115);
    }
  }
}
</style>
