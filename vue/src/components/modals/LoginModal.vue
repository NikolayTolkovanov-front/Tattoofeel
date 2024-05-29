<template>
  <vModal
    :show="modalStore.isLoginShow"
    class="login-modal"
    isCross
    @close="hideModal"
  >
    <h3 class="login-modal__title">Авторизация</h3>
    <vInput
      maska="+7 (###) ###-##-##"
      placeholder="Номер телефона"
      class="login-modal__input login-modal__input--number"
      @update:modelValue="newValue => phone = newValue"
      v-if="!isCodeSend"
    ></vInput>
    <NumberCodeInput v-else @inputCode="newValue => code = newValue" class="login-modal__input"></NumberCodeInput>
    <p v-if="!isCorrectPhone">Некорректный номер</p>
    <template v-if="!isCodeSend">
      <vCheckbox v-model="isAccept" class="login-modal__checkbox"
        >Согласен с условиями Публичной оферты</vCheckbox
      >
      <RecaptchaField
        class="login-modal__captcha"
        v-model="isCapthaSuccess"
      ></RecaptchaField>
    </template>
    <vButton
      type="primary"
      class="login-modal__button"
      @click="
        async () => {
          if (isCodeSend) {
            isCodeSend = false;
            hideModal();
            await sendMessageCode(code)
          } else {
            isCodeSend = true
            await sendMessagePhone(phone)
          };
        }
      "
      :disabled="!isCorrectFields"
      >Войти</vButton
    >
    <vButton type="inline" v-if="isCodeSend" @click="async () => await sendMessagePhone(phone)" class="login-modal__inline-button"
      >Повторно отправить смс</vButton
    >
  </vModal>
</template>
<script setup>
import vModal from "@/components/modals/vModal.vue";
import vButton from "@/components/vButton.vue";
import vInput from "@/components/vInput.vue";
import vCheckbox from "@/components/vCheckbox.vue";
import NumberCodeInput from "@/components/Login/NumberCodeInput.vue";
import RecaptchaField from "@/components/Login/RecaptchaField.vue";
import { ref, computed } from "vue";
import { useModalStore } from "@/store/modal";
import { useAuthStore } from "@/store/auth";

const modalStore = useModalStore();
const { sendPhone, sendCode } = useAuthStore();
const { hideLoginModal } = modalStore;
const isAccept = ref(false);
const isCodeSend = ref(false);
const isCapthaSuccess = ref(false);

let code = ref('')
let phone = ref('')

const isCorrectPhone = computed(() => {
  return phone.value.length === 18
})

const isCorrectFields = computed(() => {
  return isCorrectPhone.value && isCapthaSuccess.value && isAccept.value
})

const isCorrectCode = computed(() => {
  return code.value.length === 4
})

function hideModal() {
  isAccept.value = false
  isCodeSend.value = false
  isCapthaSuccess.value = false
  hideLoginModal()
}

async function sendMessagePhone() {
 if (isCorrectFields.value) {
  await sendPhone(phone.value)
 }
}

async function sendMessageCode() {
 if (isCorrectCode.value) {
  await sendCode(code.value)
 }
}

</script>
<style lang="scss">
.login-modal {
  padding: 60px;
  max-width: 520px;
  width: calc(100vw - 120px);
  &__title {
    margin-bottom: 16px;
  }

  &__input {
    margin-bottom: 16px;
    &--number {
      display: block;
    }
  }

  &__checkbox {
    margin-bottom: 20px;
  }

  &__button {
    width: 100%;
    margin-bottom: 16px;
  }

  &__inline-button {
    text-decoration: underline;
    &:hover {
      text-decoration: none;
    }
  }
  &__captcha {
    margin-bottom: 12px;
  }
}
@media (max-width: $tablet) {
  .login-modal {
    max-width: none;
    margin: 0;
    padding: 32px;
    &__captcha {
      display: block;
      background: transparent;
    }
    width: calc(100vw - 64px);
    height: calc(100vh - 64px);
  }
}
</style>
