<template>
  <div>
    <button
      class="context-menu-button"
      @click.stop="isShow = true"
      v-click-outside="() => (isShow = false)"
    >
      <menuIcon></menuIcon>
    </button>
    <div class="context-menu-button__menu" v-if="isShow">
      <button>показать на карте</button>
      <button>удалить</button>
    </div>
  </div>
</template>
<script setup>
import menuIcon from "@/components/Icons/menuIcon.vue";
import { ref } from "vue";
const isShow = ref(false);
import vClickOutsideDirective from "click-outside-vue3";
const vClickOutside = vClickOutsideDirective.directive;
</script>
<style lang="scss">
.context-menu-button {
  position: relative;
  height: 28px;
  width: 28px;
  svg {
    position: absolute;
    z-index: 2;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    fill: #a7a7a7;
    transition: all 0.3s ease 0s;
  }
  &::before {
    content: "";
    display: block;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 50%;
    z-index: 1;
    width: 0;
    height: 0;
    background-color: #e8e8f0;
    opacity: 0;
    transition: all 0.3s ease 0s;
  }
  &:hover {
    svg {
      fill: black;
    }
    &::before {
      height: 100%;
      width: 100%;
      opacity: 1;
    }
  }
  &__menu {
    border-radius: 8px;
    background: white;
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    z-index: 10;
    padding: 12px;
    width: 163px;
    button {
      font-weight: 300;
      width: 100%;
      padding: 8px;
      text-align: start;
      border-radius: 8px;
      &:hover {
        background: #f6f6f9;
      }
    }
  }
}
</style>
