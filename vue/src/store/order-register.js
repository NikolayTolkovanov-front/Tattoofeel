import { defineStore } from "pinia";
import OrderRegisterService from "@/services/order-register.service";
import visaCardIcon from "@/components/Icons/visaCardIcon.vue";
import SBPIcon from "@/components/Icons/SBPIcon.vue";
import QRIcon from "@/components/Icons/QRIcon.vue";
import tinkoffIcon from "@/components/Icons/tinkoffIcon.vue";

export const useOrderRegisterStore = defineStore("orderRegister", {
  state: () => ({
    authKey: localStorage.getItem("auth_key") || "",
    orderData: {},
    citiesData: {},
    filteredCities: [],
    currentCity: "",
    cityNotFoundError: "выберите город",
    isCitySelected: false,
    pvzList: [],
    paymentType: "",
    deliveryPoint: "",
    deliveryPoints: [{ id: 3, label: "г Красногорск", type: "Адрес доставки" }],
    marketPlaces: [
      {
        id: 1,
        label: "г Красногорск, Посёлок Архангельское 18",
        rating: "4.98",
        type: "Пункт выдачи",
      },
      {
        id: 2,
        label: "г Красногорск, Посёлок Архангельское 21",
        rating: "4.80",
        type: "Пункт выдачи",
      },
    ],
  }),
  actions: {
    async checkoutInit() {
      try {
        console.log('cookies', document.cookie);
        if (this.authKey === "") {
          console.log("authKey is empty");
          return;
        }
        const { success, data } = await OrderRegisterService.checkoutInit(this.authKey);
        if (!success || !data) {
          console.log('checkoutInit failed');
          return;
        }
        console.group('checkoutInit')
        console.log('success', success)
        console.log('data', data);
        this.orderData = data;
        console.log("orderData in checkoutInit:", this.orderData);
        console.groupEnd()
      } catch (error) {
        console.log("error in checkoutInit:", error);
      }
    },
    async getCities(cityFragment) {
      try {
        this.cityNotFoundError = "выберите город"

        if (cityFragment === "") {
          this.filteredCities = []
          console.log("city must not be empty")
          return;
        }
        if (this.authKey === "") {
          console.log("authKey is empty");
          return;
        }
        const { success, data } =  await OrderRegisterService.getCities(this.authKey, cityFragment);

        if (!success || !data) {
          this.cityNotFoundError = "ошибка, нет success и data"
          console.log('getCities failed');
          return;
        }
        this.citiesData = data;

        const cities = this.citiesData['cities']
        const deliveryList = this.citiesData['delivery_list']
        
        console.group('getCities')
        console.log('success', success)
        console.log('data', data);
        console.log("citiesData in getCities:", this.citiesData);
        console.log('deliveryList:', deliveryList);
        console.log('cities:', cities);
        console.groupEnd()

        
        // if (!this.isCitySelected) {
        //   console.log('isCitySelected', !this.isCitySelected);
        //   this.cityNotFoundError = "выберите город"
        //   console.log('cityNotFoundError', this.cityNotFoundError);
        //   console.log('cityNotFoundError', this.cityNotFoundError.length);
        // }

        if (cities?.status === true) {
          // this.cityNotFoundError = ''
          this.filteredCities = cities?.data
          console.log('filteredCities', this.filteredCities);
        } else {
          this.cityNotFoundError = cities?.msg
          this.filteredCities = []
        }
      } catch (error) {
        console.log("error in getCities:", error);
      }
    },
    async getCityCodes(cityId, cityName) {
      try {
        if (this.authKey === "") {
          console.log("authKey is empty");
          return;
        }
        const res = await OrderRegisterService.getCityCodes(this.authKey, cityId, cityName);
        // if (!success || !data) {
        //   console.log('getPvzList failed');
        //   return;
        // }

        console.group('getCityCodes')
        // console.log('success', success)
        // console.log('data', data);
        console.log("res in getCityCodes:", res);
        console.groupEnd()
      } catch (error) {
        console.log("error in getCityCodes:", error);
      }
    },
    async getPvzList(deliveryService, cityName) {
      try {
        // if (this.authKey === "") {
        //   console.log("authKey is empty");
        //   return;
        // }
        const res = await OrderRegisterService.getPvzList(this.authKey, deliveryService, cityName);
        // if (!success || !data) {
        //   console.log('getPvzList failed');
        //   return;
        // }

        console.group('getPvzList')
        // console.log('success', success)
        // console.log('data', data);
        console.log("res in getPvzList:", res);
        console.groupEnd()
      } catch (error) {
        console.log("error in getPvzList:", error);
      }
    },
    async getCdekPvzInfo(cityCode) {
      try {
        if (this.authKey === "") {
          console.log("authKey is empty");
          return;
        }
        const res = await OrderRegisterService.getCdekPvzInfo(this.authKey, cityCode);
        // if (!success || !data) {
        //   console.log('getPvzList failed');
        //   return;
        // }

        console.group('getCdekPvzInfo')
        // console.log('success', success)
        // console.log('data', data);
        console.log("res in getCdekPvzInfo:", res);
        console.groupEnd()
      } catch (error) {
        console.log("error in getCdekPvzInfo:", error);
      }
    },
    setDeliveryPoint(newVal) {
      this.deliveryPoint = newVal;
    },
    setPaymentType(newVal) {
      console.log('paymentType:', newVal);
      this.paymentType = newVal;
    },
  },
  getters: {
    getDeliveryPointLabel(state) {
      return state.deliveryPoint.label;
    },
    getPaymentTypeLabel(state) {
      return state.paymentType.label;
    },
    getDeliveryServices(state) {
      return state.orderData['delivery_services']
    },
    getPaymentTypes(state) {
      return state.orderData['payment_types']
    },
    getFilteredCities(state) {
      console.log('comp cities', state.filteredCities);
      return state.filteredCities || []
    }
  },
});
