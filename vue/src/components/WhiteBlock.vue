<template>
  <section class="white-block">
    <slot></slot>
  </section>
</template>
<style lang="scss">
.white-block {
  background: white;
  box-shadow: 0px 0px 15px 0px #00000033;
  padding: 18px 18px;
}
@media (min-width: $laptop) {
  .white-block {
    padding: 48px 30px;
  }
}
</style>
