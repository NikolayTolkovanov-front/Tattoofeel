<template>
  <vModal
    :show="isDeliveryShow"
    @close="hideDeliveryModal"
    class="delivery-modal"
    isCross
  >
    <h3 class="delivery-modal__title">Способ доставки</h3>
    <vTabs
      :tabList="[
        { id: 0, title: 'Пункт выдачи' },
        { id: 1, title: 'Курьером' },
      ]"
    >
      <template #pages="{ acitvePage }">
        <div v-if="acitvePage === 0">
          <ul class="delivery-modal__list">
            <li
              class="delivery-modal__item"
              v-for="point of marketPlaces"
              :key="point.id"
            >
              <DeliveryPoint
                @click="() => setActivePoint(point)"
                :data="point"
                :isActive="activePoint.id === point.id"
              ></DeliveryPoint>
            </li>
          </ul>
          <vButton
            class="delivery-modal__button"
            type="primary"
            @click="() => selectSet(activePoint)"
            :disabled="isButtonDisabled(marketPlaces)"
            >Выбрать</vButton
          >
          <vButton class="delivery-modal__button" type="inline"
            >Добавить новый адрес</vButton
          >
        </div>
        <div v-if="acitvePage === 1">
          <ul class="delivery-modal__list">
            <li
              class="delivery-modal__item"
              v-for="point of deliveryPoints"
              :key="point.id"
            >
              <DeliveryPoint
                @click="() => setActivePoint(point)"
                :data="point"
                :isActive="activePoint.id === point.id"
              ></DeliveryPoint>
            </li>
          </ul>
          <vButton
            class="delivery-modal__button"
            type="primary"
            @click="() => selectSet(activePoint)"
            :disabled="isButtonDisabled(deliveryPoints)"
            >Выбрать</vButton
          >
          <vButton class="delivery-modal__button" type="inline"
            >Добавить новый адрес</vButton
          >
        </div>
      </template>
    </vTabs>
  </vModal>
</template>
<script setup>
import vModal from "@/components/modals/vModal.vue";
import vTabs from "@/components/vTabs.vue";
import vButton from "@/components/vButton.vue";
import DeliveryPoint from "@/components/Cart/DeliveryPoint.vue";

import { useModalStore } from "@/store/modal";
import { useOrderRegisterStore } from "@/store/order-register";
import { storeToRefs } from "pinia";
import { computed, onMounted, ref } from "vue";

const { marketPlaces, deliveryPoints } = storeToRefs(useOrderRegisterStore())
const { isDeliveryShow } = storeToRefs(useModalStore());
const { hideDeliveryModal } = useModalStore();
const { setDeliveryPoint, getCdekPvzInfo } = useOrderRegisterStore();

function selectSet(poin) {
  setDeliveryPoint(poin);
  hideDeliveryModal();
}
const activePoint = ref("");
function setActivePoint(newVal) {
  activePoint.value = newVal;
}
const isButtonDisabled = computed(
  () => (list) => !list.find((item) => item.id === activePoint.value.id)
);

onMounted(async () => {
  // await getCdekPvzInfo()
})
</script>
<style lang="scss">
.delivery-modal {
  min-width: 320px;
  width: calc(100vw - 80px);
  max-width: 780px;
  box-sizing: border-box;
  background: #fff;
  padding: 28px 40px 40px;
  border-radius: 20px;
  .modal__cross {
    right: 8px;
    top: 8px;
  }
  &__title {
    margin-bottom: 28px;
  }
  &__item {
    margin-bottom: 8px;
  }
  &__button:first-of-type {
    margin-right: 24px;
  }
}
@media (max-width: $tablet) {
  .delivery-modal {
    margin: 0 auto;
    border-radius: 0;
    min-height: 100vh;
    width: 100vw;
    &__button {
      width: 100%;
      margin-bottom: 16px;
      margin-right: 0;
    }
  }
}
</style>
