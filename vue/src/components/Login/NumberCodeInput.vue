<template>
  <div class="number-code-input">
    <input
      class="number-code-input__field _number-code"
      v-for="index of 4"
      @input="(e) => onInput(index, e)"
      :key="index"
      :id="index"
    />
  </div>
</template>
<script setup>
import { ref, defineEmits, onMounted } from "vue";

const emit = defineEmits(["inputCode"]);

const code = ref("");

function onInput(index, e) {
  setEventValueInInput(e);
  if (e.data === null) {
    focusOnPrevInput(index);
  } else {
    focusOnNextInput(index);
  }
  setCode();
}

function focusOnPrevInput(index) {
  console.log("prev", index);
  const inputs = document.querySelectorAll("._number-code");

  if (inputs[index - 2]) {
    console.log("focus on", inputs[index - 2]);
    // for (let i = inputs.length - 1; i > (index - 2); i--) {
    //   inputs[i].disabled = true
    // }
    inputs[index - 2]?.focus();
  } else {
    console.log("blur on", inputs[index - 1]);
    inputs[index - 1].disabled = false
    inputs[index - 1].blur();
  }
}

function focusOnNextInput(index) {
  console.log("next");
  const inputs = document.querySelectorAll("._number-code");

  if (inputs[index]) {
    console.log("focus on", inputs[index]);
    
    // inputs[index].disabled = false
    inputs[index]?.focus();
  } else {
    console.log("blur on", inputs[index - 1]);
    // inputs[index - 1].disabled = false
    inputs[index - 1].blur();
  }
}

function setEventValueInInput(e) {
  e.target.value = e.data;
}

function setCode() {
  const inputs = Array.from(document.querySelectorAll("._number-code"));
  code.value = inputs.reduce((acc, item) => acc + item.value, "");

  if (code.value.length === 4) {
    console.log("code:", code.value);
    emit("inputCode", code.value);
  }
}

onMounted(() => {
  const inputs = document.querySelectorAll("._number-code");
  console.log('inputs:', inputs);
  // for (let i = 1; i < inputs.length; i++) {
  //   inputs[i].disabled = true
  // }
})
</script>
<style lang="scss">
.number-code-input {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  &__field {
    border: none;
    background: $lightGray;
    height: 60px;
    width: 60px;
    font-size: 28px;
    text-align: center;
    &:focus {
      outline: 1px solid gray;
    }
  }
}
</style>
