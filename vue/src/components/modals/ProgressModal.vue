<template>
  <vModal
    :show="modalStore.isProgressModalShow"
    class="progress-modal"
    is-cross
    @close="modalStore.hideProgressModal"
  >
    <h4 class="progress-modal__title">Ваш прогресс</h4>
    <ul class="progress-modal__list">
      <li
        class="progress-modal__item"
        :class="{
          'progress-modal__item--active': summ < 1500,
          'progress-modal__item--gray': summ < 1500,
        }"
      >
        <span class="progress-modal__label">
          <successIcon class="progress-modal__icon"></successIcon>
          Минимальная сумма заказа
        </span>
        <span class="progress-modal__summ"> от 1 500 ₽ </span>
      </li>
      <li
        class="progress-modal__item"
        :class="{
          'progress-modal__item--active': summ < 2000,
          'progress-modal__item--gray': summ >= 1500 && summ < 2000,
        }"
      >
        <span class="progress-modal__label">
          <deliveryIcon class="progress-modal__icon"></deliveryIcon>
          До бесплатной доставки
        </span>
        <span class="progress-modal__summ"> 279 ₽ </span>
      </li>
      <li
        class="progress-modal__item"
        :class="{
          'progress-modal__item--active': summ < 3000,
          'progress-modal__item--gray': summ >= 2000 && summ < 3000,
        }"
      >
        <span class="progress-modal__label">
          <watchIcon class="progress-modal__icon"></watchIcon>
          До бесплатной доставки в любое время
        </span>
        <span class="progress-modal__summ"> 1 200 ₽ </span>
      </li>
    </ul>
    <vButton
      class="progress-modal__button"
      type="primary"
      @click="modalStore.hideProgressModal"
      >Хорошо</vButton
    >
  </vModal>
</template>
<script setup>
import vModal from "@/components/modals/vModal.vue";
import vButton from "@/components/vButton.vue";
import successIcon from "@/components/Icons/successIcon.vue";
import deliveryIcon from "@/components/Icons/deliveryIcon.vue";
import watchIcon from "@/components/Icons/watchIcon.vue";
import { useModalStore } from "@/store/modal";
const modalStore = useModalStore();
const summ = 2000;
</script>
<style lang="scss">
.progress-modal {
  margin: 0;
  width: 100%;
  box-sizing: border-box;
  position: absolute;
  bottom: 0;
  padding: 36px 20px 42px 20px;
  border-radius: 12px 12px 0 0;
  .modal__cross {
    right: 8px;
    top: 8px;
  }
  &__title {
    text-align: center;
    margin-bottom: 24px;
  }
  &__summ {
    white-space: nowrap;
  }
  &__item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-radius: 8px;
    color: $gray9;
    font-size: 16px;
    svg {
      height: 24px;
      width: 24px;
      margin-right: 12px;
    }
    path {
      fill: $gray9;
    }
    span {
      vertical-align: middle;
    }
    &--active {
      color: $main-font-color;
      path {
        fill: $yellow;
      }
    }
    &--gray {
      background: #eaedf6;
    }
  }
  &__label {
    display: flex;
    align-items: center;
  }

  &__button {
    width: 100%;
  }
  &__list {
    margin: 0;
    margin-bottom: 32px;
  }
}
</style>
