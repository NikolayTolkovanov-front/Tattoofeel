<template>
  <div>
    <PriceSectionModal></PriceSectionModal>
    <DeliveryModal></DeliveryModal>
    <PaymentModal></PaymentModal>
    <LoginModal></LoginModal>
    <ProgressModal></ProgressModal>
  </div>
</template>
<script setup>
import DeliveryModal from "./DeliveryModal.vue";
import PaymentModal from "./PaymentModal.vue";
import LoginModal from "./LoginModal.vue";
import ProgressModal from "./ProgressModal.vue";
import PriceSectionModal from "./PriceSectionModal.vue";
</script>
<style lang="scss"></style>
