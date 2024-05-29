<template>
  <div class="delivery-point" :class="{ 'delivery-point--active': isActive }">
    <p class="delivery-point__name">{{ data.label }}</p>
    <div class="delivery-point__bottom">
      <div class="delivery-point__rating" v-if="data.rating">
        <starIcon></starIcon>
        {{ data.rating }}
      </div>
      <span class="delivery-point__type" v-if="data.type">
        {{ data.type }}
      </span>
    </div>
    <ContextMenuButton class="delivery-point__button"> </ContextMenuButton>
  </div>
</template>
<script setup>
import starIcon from "@/components/Icons/starIcon.vue";
import ContextMenuButton from "@/components/Cart/ContextMenuButton.vue";
import { defineProps } from "vue";
defineProps(["isActive", "data"]);
</script>
<style lang="scss">
.delivery-point {
  position: relative;
  font-size: 16px;
  line-height: 22px;
  border: 1px solid $lightGrayD;
  border-radius: 12px;
  padding: 12px 44px 12px 16px;
  cursor: pointer;
  &:hover {
    border: 1px solid $yellow-hover;
  }
  &--active {
    border: 1px solid $yellow;
  }
  &__name {
    margin: 0;
  }

  &__bottom {
    display: flex;
    font-size: 14px;
  }

  &__rating {
    margin-right: 8px;
    path,
    svg {
      width: 12px;
    }
  }
  &__type {
    color: #868695;
  }
  &__button {
    position: absolute;
    right: 8px;
    top: 8px;
  }
}
</style>
