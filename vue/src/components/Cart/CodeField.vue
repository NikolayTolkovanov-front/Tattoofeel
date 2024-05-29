<template>
  <div class="code-field">
    <input
      type="text"
      class="code-field__input"
      placeholder="Введите промокод"
    />
    <vButton type="primary">Применить</vButton>
  </div>
</template>
<script setup>
import vButton from "@/components/vButton.vue";
</script>
<style lang="scss">
.code-field {
  display: flex;
  white-space: nowrap;
  &__input {
    flex-basis: 100%;
    height: 38px;
    box-sizing: border-box;
    border-radius: 0;
    outline: none;
    border: 1px solid #e4e4e4;
    border-right: 0;
    background: #fbfbfb;
    padding-left: 15px;
  }
}
</style>
