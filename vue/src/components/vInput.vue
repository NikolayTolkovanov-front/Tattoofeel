<template>
  <label class="input">
    <input
      class="input__field"
      type="text"
      v-maska
      :data-maska="maska"
      @focus="isFocus = true"
      @blur="isFocus = false"
      @input="emit('update:modelValue', modelValue)"
      v-model="modelValue"
    />
    <span
      class="input__placeholder"
      :class="{ 'input__placeholder--active': isFocus || modelValue }"
      >{{ placeholder }}</span
    >
  </label>
</template>
<script setup>


import { vMaska } from "maska"
import { defineProps, defineEmits, ref, toRefs } from "vue";

const props = defineProps(["placeholder", "maska"]);
const emit = defineEmits(["update:modelValue"])
const { maska } = toRefs(props)
const isFocus = ref(false);
const modelValue = ref("");


</script>
<style lang="scss">
.input {
  position: relative;
  &__field {
    width: 100%;
    box-sizing: border-box;
    height: 46px;
    background: #f8f8f8;
    border: 0;
    border-bottom: 1px solid #bbb;
    padding: 9px 18px 0;
    &:focus {
      outline: none;
    }
  }
  &__placeholder {
    pointer-events: none;
    left: 18px;
    top: 18px;
    position: absolute;
    font-size: 14px;
    line-height: 14px;
    transition: all 0.3s ease;
    &--active {
      top: 4px;
      font-size: 12px;
    }
  }
}
</style>
