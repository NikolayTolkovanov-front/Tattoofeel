import { defineStore } from 'pinia'
export const useMenuStore = defineStore('menu', {
  state: () => ({ 
    isMenuOpened: false,
    isMobileOpened: false,
  }),
  actions: {
    showMobile() {
      this.isMobileOpened = true
      console.log('show');
    },
    hideMobile() {
      this.isMobileOpened = false
      console.log('hide');
    }
  },
})