import { createRouter, createWebHistory  } from "vue-router";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/lk/cart",
      name: "cart",
      component: () => import('@/views/CartPage/vCart.vue'),
    },
  ],
});

export default router
