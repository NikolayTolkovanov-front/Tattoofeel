<template>
  <div class="code-block">
    <img src="/img/promo-1.png" alt="" />
    <div class="code-block__information">
      <p class="code-block__title">Скидки на все товары бренда по промокоду</p>
      <div class="code-block__code">DRAGON20</div>
    </div>
    <div class="code-block__tag">20%</div>
  </div>
</template>
<script setup></script>
<style lang="scss">
.code-block {
  box-shadow: 0px 5px 13px 0px #8898aa26;
  width: 292px;
  height: 108px;
  display: flex;
  gap: 28px;
  position: relative;
  img {
    width: 106px;
    display: inline-block;
  }
  &__title {
    font-size: 13px;
    font-weight: 400;
    line-height: 16px;
  }

  &__code {
    height: 33px;
    width: 130px;
    background: $yellow;
    padding: 10px 27px;
    box-sizing: border-box;
    font-size: 13px;
    font-weight: 600;
    line-height: 16px;
  }
  &__tag {
    position: absolute;
    top: 3px;
    background: $red;
    color: white;
    padding: 0 12px;
    height: 20px;
    vertical-align: middle;
    font-family: Museo Sans Cyrl;
    font-size: 16px;
    font-weight: 600;
    line-height: 20px;
  }
  &__information {
    width: 144px;
    flex: 0 0 auto;
  }
}
</style>
