<template>
  <vModal
    :show="isPaymentShow"
    @close="hidePaymentModal"
    class="payment-modal"
    isCross
  >
    <h3 class="payment-modal__title">Способ оплаты</h3>
    <ul class="payment-modal__list">
      <li
        class="payment-modal__item"
        v-for="paymentType in getPaymentTypes"
        :key="paymentType.id"
      >
        <PaymentCard
          @click="activeType = paymentType"
          :isActive="activeType === paymentType"
        >
          <div class="payment-modal__label">
            <component
              class="payment-modal__icon"
              :is="paymentType.icon"
            ></component>
            <span>
              {{ paymentType.title }}
            </span>
          </div>
        </PaymentCard>
      </li>
    </ul>
    <vButton
      class="payment-modal__button"
      type="primary"
      @click="() => selectSet(activeType.title)"
      >Выбрать</vButton
    >
  </vModal>
</template>
<script setup>
import vModal from "@/components/modals/vModal.vue";
import PaymentCard from "@/components/Cart/PaymentCard.vue";
import vButton from "@/components/vButton.vue";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useModalStore } from "@/store/modal";
import { useOrderRegisterStore } from "@/store/order-register";

const { getPaymentTypes } = storeToRefs(useOrderRegisterStore());
setTimeout(() => {
  console.log("getPaymentTypes:", getPaymentTypes.value);
}, 3000);
const { isPaymentShow } = storeToRefs(useModalStore());

const { setPaymentType } = useOrderRegisterStore();
const { hidePaymentModal } = useModalStore();
const activeType = ref("");

function selectSet(type) {
  // const entriedDeliveryServices = Object.entries(getPaymentTypes.value);
  // const filteredKey = entriedDeliveryServices.filter((service) => {
  //   if (service[1] === type) {
  //     return service;
  //   }
  // })[0][0];

  setPaymentType(type);
  hidePaymentModal();
}
</script>
<style lang="scss">
.payment-modal {
  max-width: 420px;
  width: 100%;
  padding-top: 26px;
  padding: 40px;
  border-radius: 20px;
  .modal__cross {
    right: 8px;
    top: 8px;
  }
  &__title {
    margin-bottom: 24px;
    font-weight: 700;
    margin-bottom: 20px;
  }
  &__item {
    margin-bottom: 12px;
  }
  &__button {
    width: 100%;
  }
  &__icon {
    width: 18px;
  }
  &__label {
    display: flex;
    align-items: center;
    gap: 8px;
  }
}
@media (max-width: $tablet) {
  .payment-modal {
    margin: 0 auto;
    width: calc(100vw - 80px);
    max-width: unset;
    min-height: 100vh;
    border-radius: 0;
  }
}
</style>
