<template>
  <label class="checkbox">
    <input
      type="checkbox"
      class="checkbox__input"
      @change="emit('update:modelValue', !modelValue)"
    />
    <div
      class="checkbox__icon"
      :class="{ 'checkbox__icon--active': modelValue }"
    >
      <radioIcon></radioIcon>
    </div>
    <span class="checkbox__label"><slot></slot></span>
  </label>
</template>
<script setup>
import radioIcon from "@/components/Icons/radioIcon.vue";
import { defineProps, defineEmits } from "vue";
defineProps(["modelValue"]);
const emit = defineEmits(["update:modelValue"]);
</script>
<style lang="scss">
.checkbox {
  display: inline-flex;
  gap: 8px;
  align-items: center;
  cursor: pointer;
  &__input {
    display: none;
  }
  &__icon {
    align-self: center;
    width: 21px;
    flex: 0 0 auto;
    svg {
      overflow: visible;
    }
    rect {
      stroke: $lightGrayE;
    }

    &--active {
      svg {
        overflow: hidden;
      }
      rect {
        fill: $yellow;
        stroke: none;
      }
      path {
        stroke: $main-font-color;
      }
    }
  }

  &__label {
  }
}
</style>
