<template>
  <div
    class="vue-modal"
    :class="{ 'modal--mobile': isOnlyMobile }"
    v-if="show"
    @keyup.esc="close"
    tabindex="0"
    ref="modal"
  >
    <div class="vue-modal__background" @click="close"></div>
    <div :class="props.class + ' vue-modal__body'">
      <button class="vue-modal__cross" v-if="isCross" @click="close">
        <crossIcon></crossIcon>
      </button>
      <slot></slot>
    </div>
  </div>
</template>
<script setup>
import { defineProps, defineEmits, watch, ref } from "vue";
import crossIcon from "@/components/Icons/crossIcon.vue";
const props = defineProps({
  show: Boolean,
  class: String,
  isCross: Boolean,
  isOnlyMobile: Boolean,
});
const emit = defineEmits(["close"]);
const modal = ref(null);
function close() {
  emit("close");
}
watch(
  () => props.show,
  () => {
    toggleBodyOverflow();
    setTimeout(() => {
      modal.value?.focus();
    }, 20);
  }
);
function toggleBodyOverflow() {
  const body = document.querySelector("body");
  if (!body.style.overflow) body.style.overflow = "hidden";
  else body.style.overflow = "";
}
</script>
<style lang="scss">
.vue-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 100000;
  overflow: auto;
  &__body {
    display: block;
    width: fit-content;
    margin: 40px auto;
    position: relative;
    background: white;
    padding: 70px 55px;
  }

  &__background {
    position: fixed;
    width: 100vw;
    height: 100vh;
    background: $main-font-color;
    opacity: 0.3;
  }
  &__cross {
    position: absolute;
    right: 23px;
    top: 26px;
    &:hover {
      svg {
        stroke: $gray;
      }
    }
    &:active {
      svg {
        stroke: $main-font-color;
      }
    }
    svg {
      height: 24px;
      width: 24px;
      stroke: $main-font-color;
    }
  }
}
@media (min-width: $laptop) {
  .modal--mobile {
    display: none;
  }
}
</style>
