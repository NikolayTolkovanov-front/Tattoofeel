<template>
  <div class="recaptcha-field">
    <vue-recaptcha
      :sitekey="siteKey"
      hl="ru"
      :loading-timeout="30000"
      @verify="recaptchaVerified"
      @expire="recaptchaExpired"
      @fail="recaptchaFailed"
      @error="recaptchaError"
      ref="recaptchaRef"
    >
    </vue-recaptcha>
  </div>
</template>
<script setup>
import { ref, defineEmits } from "vue";
import vueRecaptcha from "vue3-recaptcha2";
const emit = defineEmits(["update:modelValue"]);
const recaptchaRef = ref(null);
const siteKey = "6LcmPT0pAAAAANrCdn04At6FzcfKch_tYek7gjlC";
// const siteKey = "6LdovNQoAAAAAAATZ01cXJqwXSsDfWfkUn--nSR6"; // localhost
function recaptchaVerified() {
  emit("update:modelValue", true);
}

function recaptchaExpired() {
  recaptchaRef.value.reset();
}

function recaptchaFailed() {}

function recaptchaError(reason) {
  console.log("reason", reason);
}
</script>
<style lang="scss">
.recaptcha-field {
  display: inline-block;
  height: 78px;
  background: #f9f9f9;
  position: relative;
}
</style>
