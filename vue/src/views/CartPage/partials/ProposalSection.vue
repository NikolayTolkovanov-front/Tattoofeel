<template>
  <div class="proposal-section">
    <div class="proposal-section__slider">
      <carousel
        ref="slider"
        class="proposal-section__slider"
        v-bind="settings"
        :breakpoints="breakpoints"
        v-model="currentSlide"
      >
        <slide v-for="slide in 12" :key="slide">
          <ProductItem></ProductItem>
        </slide>
      </carousel>
      <SliderButtons
        class="proposal-section__slider-button"
        @next="slider.next()"
        @prev="slider.prev()"
        :currentSlide="currentSlide"
        :isNextDisalbed="isNextDisabled"
      />
    </div>
  </div>
</template>
<script setup>
import ProductItem from "@/components/Cart/ProductItem.vue";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide } from "vue3-carousel";
import SliderButtons from "@/components/Cart/SliderButtons.vue";
import { ref, computed } from "vue";


const currentSlide = ref(0);
const slider = ref(null);
const settings = {
  itemsToShow: 1,
  snapAlign: "start",
};
const breakpoints = {
  560: {
    itemsToShow: 2,
    snapAlign: "start",
    itemsToScroll: 2,
  },
  800: {
    itemsToShow: 3,
    itemsToScroll: 3,
    snapAlign: "start",
  },
  1070: {
    itemsToShow: 4,
    itemsToScroll: 4,
    snapAlign: "start",
  },
};
const isNextDisabled = computed(() => {
  return currentSlide.value >= 12 - slider?.value?.data.config.itemsToShow;
});
</script>
<style lang="scss">
.proposal-section {
  &__slider {
    position: relative;
  }

  &__slider-button {
    width: calc(100% + 20px);
    left: -10px;
  }
}
.carousel__track {
    margin: 14px 0;
}
</style>
