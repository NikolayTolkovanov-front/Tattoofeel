<template>
  <WhiteBlock class="also-section">
    <h3>Возможно, вам понадобится</h3>
    <div class="also-section__slider">
      <carousel
        ref="slider"
        v-bind="settings"
        :breakpoints="breakpoints"
        v-model="currentSlide"
      >
        <slide v-for="slide in 12" :key="slide">
          <SmallProductItem></SmallProductItem>
        </slide>
      </carousel>
      <SliderButtons
        class="also-section__buttons"
        @next="slider.next()"
        @prev="slider.prev()"
        :currentSlide="currentSlide"
        :isNextDisalbed="isNextDisabled"
      ></SliderButtons>
    </div>
  </WhiteBlock>
</template>
<script setup>
import WhiteBlock from "@/components/WhiteBlock.vue";
import SmallProductItem from "@/components/Cart/SmallProductItem.vue";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide } from "vue3-carousel";
import SliderButtons from "@/components/Cart/SliderButtons.vue";
import { ref, computed } from "vue";
const currentSlide = ref(0);
const slider = ref(null);
const isNextDisabled = computed(() => {
  return currentSlide.value >= 12 - slider?.value?.data.config.itemsToShow;
});
const settings = {
  itemsToShow: 2,
  snapAlign: "start",
};
const breakpoints = {
  // 700px and up
  480: {
    itemsToShow: 3,
    snapAlign: "start",
  },
  600: {
    itemsToShow: 4,
    snapAlign: "start",
  },
  740: {
    itemsToShow: 5,
    snapAlign: "start",
  },
  1024: {
    itemsToShow: 3,
    snapAlign: "start",
  },
  1100: {
    itemsToShow: 4,
    snapAlign: "start",
  },
  // 1024 and up
  1200: {
    itemsToShow: 5,
    snapAlign: "start",
  },
  1400: {
    itemsToShow: 6,
    snapAlign: "start",
  },
};
</script>
<style lang="scss">
.also-section {
  &__slider {
    position: relative;
  }
  &__buttons {
    width: calc(100% + 28px);
    left: -14px;
  }
}
</style>
