<template>
  <div class="number-counter">
    <button class="number-counter__button" @click="emit('minusProduct')">
      -
    </button>
    <input class="number-counter__count" type="text" v-model="count" readonly />
    <button class="number-counter__button" @click="emit('plusProduct')">
      +
    </button>
  </div>
</template>
<script setup>
import { computed, defineProps, defineEmits } from "vue";
const count = computed({
  get: () => props.modelValue,
  // set: (newVal) => {
  //   if (newVal >= 1) emit("update:modelValue", newVal);
  // },
});
const props = defineProps(["modelValue"]);
const emit = defineEmits(["update:modelValue", "minusProduct", "plusProduct"]);
</script>
<style lang="scss">
.number-counter {
  white-space: nowrap;
  &__button {
    display: inline-block;
    border-radius: 2px;
    text-align: center;
    vertical-align: middle;
    margin: 0;
    font-size: 18px;
    width: 30px;
    height: 30px;
    line-height: 30px;
    background: #fbfbfb;
    border: none;
    padding: 0;
    cursor: pointer;
    &:hover {
      background: #dfdfdf;
    }
    &:active {
      background: #cccccc;
    }
  }

  &__count {
    border: none;
    border-collapse: collapse;
    border-spacing: 0;
    user-select: none;
    outline: none;
    text-align: center;
    display: inline-block;
    vertical-align: middle;
    font-family: monospace;
    font-weight: bold;
    width: 32px;
    font-size: 15px;
    padding: 0 10px;
  }
}
</style>
