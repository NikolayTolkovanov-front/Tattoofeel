<template>
  <button :class="`button button--${props.type}`" :disabled="isDisabled">
    <slot></slot>
  </button>
</template>
<script setup>
import { defineProps } from "vue";
const props = defineProps({
  type: { type: String, default: "inline" },
  isDisabled: { type: Boolean, default: false }
});

setTimeout(() => {
  console.log('disabled', props.isDisabled)
}, 3000);
</script>
<style lang="scss">
.button {
  &--inline {
    color: $gray;
    path {
      fill: $gray;
    }

    &:hover {
      color: $darkGray;
      path {
        fill: $darkGray;
      }
    }
    &:active {
      color: $gray9;
      path {
        fill: $gray9;
      }
    }
  }
  &--primary {
    height: 40px;
    padding-left: 32px;
    padding-right: 32px;
    color: $main-font-color;
    background: $yellow;
    &:hover {
      background: $yellow-hover;
    }
    &:active {
      background: $yellow-active;
    }
  }
  &:disabled {
    cursor: default;
    background: $lightGrayD;
    color: gray;
  }
}
</style>
