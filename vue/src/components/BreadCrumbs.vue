<template>
  <div class="bread-crumbs">
    <ul class="bread-crumbs__list">
      <li><a href="/">Главная</a></li>
      <li><arrowIcon></arrowIcon></li>
      <li><a href="/lk">Личный кабинет</a></li>
      <li><arrowIcon></arrowIcon></li>
      <li>Корзина</li>
    </ul>
  </div>
</template>
<script setup>
import arrowIcon from "@/components/Icons/arrowIcon.vue";
</script>
<style lang="scss">
.bread-crumbs {
  &__list {
    padding: 0;
    gap: 14px;
    list-style: none;
    display: flex;
    color: #8b8888;
  }
  a {
    color: #8b8888;
    text-decoration: none;
    &:hover {
      color: #616161;
    }
    &:active {
      color: #1c1c1c;
    }
  }
}
</style>
