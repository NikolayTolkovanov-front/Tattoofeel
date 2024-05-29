import { defineStore } from "pinia";
import CartService from "@/services/cart.service";

export const useCartStore = defineStore("cart", {
  state: () => ({
    cart: [],
    authKey: localStorage.getItem("auth_key") || "",

  }),
  actions: {
    async updateCart() {
      try {
        this.cart = await CartService.getCart();

        console.group('UpdateCart')
        console.log("cart in getCart:", this.cart);
        console.groupEnd()
      } catch (error) {
        console.log("error in getCart:", error);
      }
    },
    async addProduct(id, count) {
      const productToChange = this.getProductById(id);

      if (productToChange) {
        try {
          const res = await CartService.changeProduct(
            id,
            productToChange.count + count,
            ""
          );

          console.group('addProduct')
          console.log("productToChange", productToChange);
          console.log("res in changeProduct:", res);
          console.groupEnd()

          await this.updateCart();
        } catch (error) {
          alert("больше товаров нет");
          console.log("error in changeProduct:", error);
        }
      } else {
        try {
          const res = await CartService.addProduct(id, count);

          console.group('addProduct')
          console.log("productToChange", productToChange);
          console.log("res in addProduct:", res);
          console.groupEnd()

          await this.updateCart();
        } catch (error) {
          alert("больше товаров нет");
          console.log("error in addProduct:", error);
        }
      }
    },
    async plusProduct(id, count, coupon) {
      try {
        const res = await CartService.changeProduct(id, count + 1, coupon);
        
        console.group('plusProduct')
        console.log("res in changeProduct:", res);
        console.groupEnd()

        await this.updateCart();
      } catch (error) {
        alert("больше товаров нет");
        console.log("error in plusProduct:", error);
      }
    },
    async minusProduct(id, count, coupon) {
      try {
        if (count > 1) {
          const res = await CartService.changeProduct(id, count - 1, coupon);

          console.group('minusProduct')
          console.log("res in changeProduct:", res);
          console.groupEnd()

          await this.updateCart();
        }
      } catch (error) {
        console.log("error in minusProduct:", error);
      }
    },
    async removeProduct(id, coupon) {
      try {
        const res = await CartService.removeProduct(id, coupon);

        console.group('minusProduct')
        console.log("res in removeProduct:", res);
        console.groupEnd()

        await this.updateCart();
      } catch (error) {
        console.log("error in removeProduct:", error);
      }
    },
  },
  getters: {
    getCartProducts(state) {
      return state.cart["products"] || [];
    },

    getTotalPrice(state) {
      return state.cart["total_price"] || 0;
    },
    getTotalCount(state) {
      return state.cart["total_count"] || 0;
    },
    getProductById() {
      return (id) => this.getCartProducts.find((product) => product.id === id);
    },
  },
});
