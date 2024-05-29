<template>
  <div class="mobile-aside">
    <div class="mobile-aside__progress">
      <ProgressBar
        is-arrow-button
        @arrow-click="modalStore.showProgressModal"
      ></ProgressBar>
    </div>
    <vButton
      class="mobile-aside__button"
      type="primary"
      @click="modalStore.showPriceSectionModal"
      >Оформить заказ на 2 323 ₽</vButton
    >
  </div>
</template>
<script setup>
import ProgressBar from "@/components/Cart/ProgressBar.vue";
import vButton from "@/components/vButton.vue";
import { useModalStore } from "@/store/modal";
const modalStore = useModalStore();
</script>
<style lang="scss">
body {
  padding-bottom: 122px;
}
.mobile-aside {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  margin: 0;
  border: 0;
  box-sizing: border-box;
  box-shadow: 0 -4px 12px #0000001a;
  border-radius: 12px 12px 0 0;
  padding: 12px;
  z-index: 100000;
  &__progress {
    position: relative;
    margin-bottom: 14px;
    .progress-bar__title {
      margin-bottom: 12px;
    }
  }

  &__arrow-right {
    position: absolute;
    right: 0px;
  }

  &__button {
    width: 100%;
  }
}
@media (min-width: $laptop) {
  body {
    padding-bottom: unset;
  }
  .mobile-aside {
    display: none;
  }
}
</style>
