<template>
  <div class="v-tabs">
    <ul class="v-tabs__tab-list">
      <li
        v-for="tab of tabList"
        :key="`tab-${tab.id}`"
        class="v-tabs__tab-item"
      >
        <button
          type=""
          class="v-tabs__button"
          :class="{ 'v-tabs__button--active': tab.id === acitvePage }"
          @click="acitvePage = tab.id"
        >
          {{ tab.title }}
        </button>
      </li>
    </ul>
    <slot name="pages" :acitvePage="acitvePage"></slot>
  </div>
</template>
<script setup>
import { defineProps, onMounted, ref } from "vue";
const props = defineProps({
  tabList: Array,
});
const acitvePage = ref("");
onMounted(() => {
  acitvePage.value = props.tabList[0].id;
});
</script>
<style lang="scss">
.v-tabs {
  &__tab-list {
    display: flex;
    background: #f1f1f5;
    border-radius: 8px;
    padding: 2px;
    margin-bottom: 24px;
  }

  &__tab-item {
    flex-basis: 50%;
  }

  &__button {
    height: 44px;
    border-radius: 8px;
    width: 100%;
    font-size: 16px;
    font-weight: 600;
    &:hover {
      background: #e8e8f0;
    }
    &--active {
      background: white;
      &:hover {
        background: white;
      }
    }
  }
}
</style>
