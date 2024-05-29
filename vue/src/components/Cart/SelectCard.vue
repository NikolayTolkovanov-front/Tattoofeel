<template>
  <label class="select-card" :class="{ 'select-card--active': isActive }">
    <div class="select-card__icon">
      <slot name="icon"></slot>
    </div>
    <div class="select-card__information">
      <div class="select-card__name">{{ title }}</div>
      <div class="select-card__label">
        {{ subtitle }}
      </div>
      <div class="select-card__delivery" v-if="delivery">
        {{ delivery }}
      </div>
    </div>
    <input
      type="radio"
      class="select-card__input"
      @change="emit('select')"
      :name="name"
    />
    <div class="select-card__criclie"></div>
  </label>
</template>
<script setup>
import { defineProps, defineEmits } from "vue";
defineProps(["title", "subtitle", "delivery", "name", "isActive"]);
const emit = defineEmits(["select"]);
</script>
<style lang="scss">
.select-card {
  display: flex;
  border: 2px solid #cbcbcb;
  padding: 15px;
  width: calc(49% - 32px);
  margin-bottom: 15px;
  gap: 30px;
  width: 100%;
  box-sizing: border-box;
  justify-content: space-between;
  cursor: pointer;

  &__name {
    vertical-align: baseline;
    font-size: 18px;
    line-height: 22px;
    margin-bottom: 4px;
  }

  &__label {
    font-weight: 300;
    font-size: 16px;
    line-height: 19px;
    margin-bottom: 5px;
  }
  &__delivery {
    color: $gray;
  }

  &__input {
    display: none;
  }

  &__criclie {
    width: 1.5em;
    height: 1.5em;
    border-radius: 50%;
    display: block;
    border: 0.3em solid #c4c4c4;
    flex: 0 0 auto;
  }
  &__information {
    width: 100%;
  }
  &:hover {
    border: 2px solid $gray9;
    .select-card__criclie {
      border: 0.3em solid $gray9;
    }
  }
  &--active {
    border: 2px solid $yellow;
    .select-card__criclie {
      border: 0.3em solid $yellow;
    }
    &:hover {
      border: 2px solid $yellow-hover;
      .select-card__criclie {
        border: 0.3em solid $yellow-hover;
      }
    }
  }
}
</style>
