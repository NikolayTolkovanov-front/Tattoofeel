import { defineStore } from "pinia";
import { useOrderRegisterStore } from "./order-register";
import { useCartStore } from "./cart";

import AuthService from "@/services/auth.service";
export const useAuthStore = defineStore("auth", {
  state: () => ({
    authKey: localStorage.getItem("auth_key") || "",
  }),
  actions: {
    async sendPhone(phone) {
      const { updateCart } = useCartStore();
      let formattedPhone = phone.replaceAll(/[\s()-]/g, "");

      try {
        const { success } = await AuthService.sendPhone(formattedPhone);
        if (!success) {
          console.log('sendPhone failed');
          return;
        }

        console.group('sendPhone')
        console.log('success', success)
        console.groupEnd()

        await updateCart();
      } catch (error) {
        console.log("error in sendPhone:", error);
      }
    },
    async sendCode(code) {
      const { checkoutInit } = useOrderRegisterStore();
      const { updateCart } = useCartStore();

      try {
        const { success, data } = await AuthService.sendCode(code);

        if (!success || !data["auth_key"]) {
          console.log('sendCode failed');
          return;
        }
        localStorage.setItem("auth_key", data["auth_key"]);
        
        console.group('sendCode')
        console.log('success', success)
        console.log('data', data);
        console.log("key:", localStorage.getItem("auth_key"));
        console.groupEnd()

        await checkoutInit();
        await updateCart();
      } catch (error) {
        console.log("error in sendCode:", error);
      }
    },
  },
  getters: {},
});
