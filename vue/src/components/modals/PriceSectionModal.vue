<template>
  <vModal
    :show="modalStore.isPriceSectionModalShow"
    class="price-section-modal"
    is-cross
    @close="modalStore.hidePriceSectionModal"
    is-only-mobile
  >
    <PriceSection></PriceSection>
  </vModal>
</template>
<script setup>
import vModal from "@/components/modals/vModal.vue";
import PriceSection from "@/views/CartPage/partials/PriceSection.vue";
import { useModalStore } from "@/store/modal";
const modalStore = useModalStore();
</script>
<style lang="scss">
.price-section-modal {
  padding: 0;
  margin: 0;
  width: 100vw;
  .modal__cross {
    top: 8px;
    right: 8px;
  }
  .price-section {
    padding-top: 60px;
  }
}
</style>
