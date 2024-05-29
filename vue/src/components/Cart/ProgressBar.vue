<template>
  <label class="progress-bar">
    <div
      class="progress-bar__title"
      :class="{ 'progress-bar__title--arrow': isArrowButton }"
    >
      <span> Ещё <b> 2500 ₽</b> до бесплатной доставки </span>
      <button
        v-if="isArrowButton"
        class="progress-bar__arrow-icon"
        @click="emit('arrowClick')"
      >
        <arrowIcon></arrowIcon>
      </button>
    </div>
    <div class="progress-bar__progress">
      <div ref="progressBar"></div>
    </div>
  </label>
</template>
<script setup>
import arrowIcon from "@/components/Icons/arrowIcon.vue";
import { defineProps, defineEmits, onMounted, ref } from "vue";
const emit = defineEmits(["arrowClick"]);
defineProps({ isArrowButton: Boolean });
const progress = {
  val: 20,
  max: 100,
};
const progressBar = ref(null);
onMounted(() => {
  progressBar.value.style.width = 100 / (progress.max / progress.val) + "%";
});
</script>
<style lang="scss">
.progress-bar {
  &__progress {
    width: 100%;
    border-radius: 0;
    background: #ebebeb;
    height: 8px;
    position: relative;
    div {
      position: absolute;
      height: 100%;
      background: #8ae553;
    }
  }
  &__title--arrow {
    display: flex;
    justify-content: space-between;
  }
  &__arrow-icon {
    cursor: pointer;
    border-radius: 48px;
    background: #eaedf6;
    height: 24px;
    width: 24px;
    font-size: 0;
    path {
      stroke: $yellow;
      stroke-width: 2px;
    }
  }
}
</style>
