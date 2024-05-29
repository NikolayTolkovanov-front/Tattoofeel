<template>
  <div class="slider-buttons">
    <button
      class="slider-buttons__button slider-buttons__button--prev"
      @click="emit('prev')"
      :disabled="currentSlide <= 0"
    >
      <arrowfillIcon></arrowfillIcon>
    </button>
    <button
      class="slider-buttons__button slider-buttons__button--next"
      @click="emit('next')"
      :disabled="isNextDisalbed"
    >
      <arrowfillIcon></arrowfillIcon>
    </button>
  </div>
</template>
<script setup>
import arrowfillIcon from "@/components/Icons/arrowfillIcon.vue";
import { defineEmits, defineProps } from "vue";
defineProps(["currentSlide", "isNextDisalbed"]);
const emit = defineEmits(["next", "prev"]);
</script>
<style lang="scss">
.slider-buttons {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  justify-content: space-between;
  width: 100%;
  pointer-events: none;
  &__button {
    height: 40px;
    background: white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 50px;
    pointer-events: all;
    svg {
      fill: $yellow;
      width: 8px;
      vertical-align: middle;
    }
    &:hover svg {
      fill: $yellow-hover;
    }
    &:active svg {
      fill: $yellow-active;
    }
    &:disabled svg {
      fill: $lightGrayD;
    }
  }
  &__button--prev {
    padding-left: 24px;
    padding-right: 28px;
    svg {
      transform: rotate(180deg);
    }
  }

  &__button--next {
    padding-left: 28px;
    padding-right: 24px;
  }
}
</style>
