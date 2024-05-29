<template>
   <div class="basket-page container">
      <BreadCrumbs class="basket-page__bread-crumbs"></BreadCrumbs>
      <main class="basket-page__main">
         <div class="basket-page__main-content">
            <CartSection class="basket-page__section basket-page__section--mobile"></CartSection>
            <AlsoSection class="basket-page__section"></AlsoSection>
            <SaleSection class="basket-page__section"></SaleSection>
            <CartSection class="basket-page__section basket-page__section--desktop"></CartSection>
         </div>
         <aside class="basket-page__aside">
            <PriceSection class="basket-page__section"></PriceSection>
         </aside>
      </main>
      <h2>Рекомендуем</h2>
      <ProposalSection></ProposalSection>
      <MobileAside></MobileAside>
   </div>
</template>
<script setup>
import BreadCrumbs from "@/components/BreadCrumbs.vue";
import AlsoSection from "@/views/CartPage/partials/AlsoSection.vue";
import SaleSection from "@/views/CartPage/partials/SaleSection.vue";
import CartSection from "@/views/CartPage/partials/CartSection.vue";
import PriceSection from "@/views/CartPage/partials/PriceSection.vue";
import ProposalSection from "@/views/CartPage/partials/ProposalSection.vue";
import MobileAside from "@/views/CartPage/partials/MobileAside.vue";

import { useCartStore } from "@/store/cart";
import { storeToRefs } from "pinia";
const { getCartProducts } = storeToRefs(useCartStore());
</script>
<style lang="scss" scoped>
.basket-page {
   padding-top: 55.5px;
   padding-bottom: 28px;

   &__bread-crumbs {
      margin-bottom: 24px;
   }

   &__main {
      margin-bottom: 28px;
   }

   &__main-content {
      margin-bottom: 12px;
   }

   &__section {
      margin-bottom: 12px;

      &:last-child {
         margin: 0;
      }

      &--mobile {
         display: block;
      }

      &--desktop {
         display: none;
      }
   }

   &__aside {
      display: none;
   }
}

@media (min-width: $laptop) {
   .basket-page {
      padding-top: 0;
      padding-bottom: 64px;

      &__bread-crumbs {
         margin-bottom: 48px;
      }

      &__main {
         display: flex;
         gap: 14px;
         margin-bottom: 32px;
      }

      &__main-content {
         width: calc(100% - 446px);
      }

      &__aside {
         display: block;
         flex: 0 0 auto;
         width: 430px;
      }

      &__section {
         margin-bottom: 14px;

         &--mobile {
            display: none;
         }

         &--desktop {
            display: block;
         }
      }
   }
}</style>
