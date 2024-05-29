<template>
  <WhiteBlock class="price-section">
    <div class="price-section__price">
      <div class="price-section__label">
        <tooltipIcon></tooltipIcon>
        <span> Итого к оплате </span>
      </div>
      <div class="price-section__line"></div>
      <span>{{ getTotalPrice?.toLocaleString("ru-RU") }} ₽</span>
    </div>
    <citySelect class="price-section__city-select"></citySelect>
    <div class="price-section__delivery">
      <h4>Способ доставки</h4>
      <vButton type="inline" @click="showModalDelivery" :isDisabled="!isCitySelected">
        <span class="price-section__button-label">
          <template v-if="!getDeliveryPointLabel">не выбрано</template>
          <template v-else> {{ getDeliveryPointLabel }} </template>
        </span>
        <editIcon class="price-section__button-icon"></editIcon>
      </vButton>
    </div>
    <div class="price-section__payment">
      <h4>Способ оплаты</h4>
      <vButton type="inline" @click="showPaymentModal">
        <span class="price-section__button-label">
          <template v-if="!getPaymentTypeLabel">не выбрано</template>
          <template v-else> {{ getPaymentTypeLabel }} </template>
        </span>
        <editIcon class="price-section__button-icon"></editIcon>
      </vButton>
    </div>
    <div class="price-section__information">
      <p class="price-section__info-title">
        Сумма доставки и комиссии могут отличаться от данных сайта. Точную сумму
        назовет оператор.
      </p>
      <div class="price-section__info-block">
        <img src="/img/shield.png" alt="" />
        <div>
          <h5>Страховка за наш счёт</h5>
          <span
            >Если транспортная компания потеряет или повредит Ваш заказ, мы
            оперативно вернём деньги, либо вышлем новый товар.</span
          >
        </div>
      </div>
    </div>
    <div class="price-section__counter">
      <span> Количество товара </span>
      <span>{{ getTotalCount }} шт</span>
    </div>
    <CodeField class="price-section__code"></CodeField>
    <ProgressBar class="price-section__progress"></ProgressBar>
    <vButton class="price-section__button" type="primary"
      >Оформить заказ на <b>{{ getTotalPrice }}</b>
    </vButton>
  </WhiteBlock>
</template>
<script setup>
import tooltipIcon from "@/components/Icons/tooltipIcon.vue";
import editIcon from "@/components/Icons/editIcon.vue";
import WhiteBlock from "@/components/WhiteBlock.vue";
import citySelect from "@/components/Cart/citySelect.vue";
import CodeField from "@/components/Cart/CodeField.vue";
import ProgressBar from "@/components/Cart/ProgressBar.vue";
import vButton from "@/components/vButton.vue";

import { computed } from "vue";
import { storeToRefs } from "pinia";
import { useModalStore } from "@/store/modal";
import { useCartStore } from "@/store/cart";
import { useOrderRegisterStore } from "@/store/order-register";

const { showDeliveryModal, showPaymentModal } = useModalStore();
const { getCdekPvzInfo } = useOrderRegisterStore();
const {
  currentCity,
  isCitySelected,
  cityNotFoundError,
  getDeliveryPointLabel,
  getPaymentTypeLabel,
} = storeToRefs(useOrderRegisterStore());

const {
  getTotalPrice,
  getTotalCount,
} = storeToRefs(useCartStore());

async function showModalDelivery() {
  if (!isCitySelected) {
    console.log('sity was not selected');
    return
  }
  showDeliveryModal()
  await getCdekPvzInfo()
}
</script>
<style lang="scss">
.price-section {
  &__price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 4px;
    margin-bottom: 32px;
    & > span {
      font-size: 18px;
      font-weight: 800;
      flex: 0 0 auto;
    }
  }
  &__line {
    height: 20px;
    flex-basis: 100%;
    border-bottom: 1px dotted $main-font-color;
  }
  &__label {
    font-size: 0;
    height: 20px;
    flex: 0 0 auto;
    span {
      vertical-align: top;
      line-height: 20px;
      font-size: 14px;
      margin-left: 4px;
    }
  }
  &__city-select {
    margin-bottom: 42px;
  }
  h4 {
    margin-bottom: 8px;
  }
  &__delivery {
    margin-bottom: 36px;
  }
  &__button-label {
    text-decoration: underline;
    margin-right: 8px;
    &:hover {
      text-decoration: none;
    }
  }
  &__payment {
    margin-bottom: 42px;
  }
  &__info-title {
    font-size: 10px;
    color: $gray7;
    margin: 0;
    margin-bottom: 20px;
  }

  &__info-block {
    margin-bottom: 28px;
    display: flex;
    gap: 8px;
    color: $gray7;
    img {
      flex: 0 0 auto;
      height: 48px;
    }
    h5 {
      font-weight: 400;
      margin: 0;
      --swiper-theme-color: #007aff;
      font-size: 14px;
      margin-bottom: 5px;
    }
    span {
      outline: none;
      font: inherit;
      vertical-align: baseline;
      font-size: 12px;
    }
  }

  &__counter {
    display: flex;
    justify-content: space-between;
    color: $gray;
    margin-bottom: 18px;
  }

  &__button {
    margin-top: 12px;
    width: 100%;
  }
  &__code {
    margin-bottom: 38px;
    width: 100%;
  }
}
@media (min-width: $laptop) {
  .price-section {
    &__price {
      & > span {
        font-size: 21px;
      }
    }

    &__button-label {
      font-size: 16px;
    }
  }
}
</style>
