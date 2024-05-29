<template>
  <div class="select">
    <label class="select__field">
      <input
        class="select__input"
        placeholder="Выберите город"
        @focus="isOpen = true"
        @blur="isOpen = false"
        @input="getCitiesByFragment"
        v-model="currentCity"
      />
      <arrowIcon class="select__arrow-icon"></arrowIcon>
    </label>
    <Transition name="select__options--transition">
      <div class="select__options" v-if="getFilteredCities.length && isOpen">
        <ul class="select__options-list">
          <li
            class="select__option"
            v-for="city of getFilteredCities"
            :key="city.id"
            @click="() => selectItem(city.id)"
          >
            <label class="select__label">
              <span class="select__city">{{ city.name }}</span>
              <span class="select__region">{{ city.region }}</span>
            </label>
            <div
              class="select__icon"
              :class="{ 'select__icon--active': modelValue === city.id }"
            >
              <radioIcon></radioIcon>
            </div>
          </li>
        </ul>
      </div>
    </Transition>
    <p v-if="cityNotFoundError.length">{{ cityNotFoundError }}</p>
  </div>
</template>
<script setup>
import radioIcon from "@/components/Icons/radioIcon.vue";
import arrowIcon from "@/components/Icons/arrowIcon.vue";
import { ref, computed } from "vue";

import { useOrderRegisterStore } from "@/store/order-register";
import { storeToRefs } from 'pinia'
const { getCities, getCityCodes } = useOrderRegisterStore()
const { currentCity, isCitySelected, cityNotFoundError, getFilteredCities } = storeToRefs(useOrderRegisterStore())

const isOpen = ref(false);
const modelValue = ref(0);

async function selectItem(newVal) {
  const filteredCity = getFilteredCities.value.filter((city) => city.id === newVal)[0]
  cityNotFoundError.value = ""
  currentCity.value = filteredCity.name
  isCitySelected.value = true
  modelValue.value = newVal;

  await getCityCodes()
}
async function getCitiesByFragment(e) {
  modelValue.value = 0
  isCitySelected.value = false
  await getCities(e.target.value)
  console.log('change', e.target.value);
}
</script>
<style lang="scss">
.select {
  position: relative;
  &__input {
    height: 38px;
    border-radius: 0;
    border: 1px solid #e4e4e4;
    background: transparent;
    padding-left: 28px;
    width: 100%;
    box-sizing: border-box;
    &::placeholder {
      font-size: 16px;
      line-height: 19px;
    }
    &:focus {
      outline: none;
    }
  }
  &__arrow-icon {
    position: absolute;
    right: 17px;
    top: 50%;
    transform: translateY(-50%) rotate(90deg);
  }
  &__field {
    display: inline-block;
    position: relative;
    height: 100%;
    width: 100%;
  }
  &__options {
    position: absolute;
    background: white;
    padding: 16px 32px;
    padding-left: 28px;
    box-shadow: 0px 0px 15px 0px #00000033;
    top: calc(100% + 12px);
    right: 0;
    left: 0;
    z-index: 1;
  }

  &__options-list {
  }

  &__option {
    display: flex;
    justify-content: space-between;
    cursor: pointer;
    padding: 16px 8px;
    &:hover {
      background: $lightGray;
    }
  }

  &__label {
    cursor: pointer;
  }

  &__city {
    display: block;
    font-size: 16px;
    line-height: 24px;
  }
  &__region {
    font-size: 13px;
    line-height: 19px;
    color: $gray9;
  }
  &__icon {
    align-self: center;
    width: 21px;
    flex: 0 0 auto;
    svg {
      overflow: visible;
    }
    rect {
      stroke: $lightGrayE;
    }

    &--active {
      svg {
        overflow: hidden;
      }
      rect {
        fill: $yellow;
        stroke: none;
      }
      path {
        stroke: $main-font-color;
      }
    }
  }
  &__options--transition-enter-active,
  &__options--transition-leave-active {
    transition: opacity 0.2s ease;
  }

  &__options--transition-enter-from,
  &__options--transition-leave-to {
    opacity: 0;
  }
}
</style>
