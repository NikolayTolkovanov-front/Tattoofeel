import { defineStore } from 'pinia'
export const useModalStore = defineStore('modals', {
   state: () => ({ 
    isDeliveryShow: false,
    isPaymentShow: false,
    isLoginShow: false,
    isProgressModalShow: false,
    isPriceSectionModalShow: false,
  }),
  actions: {
    showDeliveryModal(){
      this.isDeliveryShow = true
    },
    hideDeliveryModal(){
      this.isDeliveryShow = false
    },
    showPaymentModal(){
      this.isPaymentShow = true
    },
    hidePaymentModal(){
      this.isPaymentShow = false
    },
    showLoginModal(){
      this.isLoginShow = true;
    },
    hideLoginModal(){
      this.isLoginShow = false;
    },
    showProgressModal(){
      this.isProgressModalShow = true;
    },
    hideProgressModal(){
      this.isProgressModalShow = false;
    },
    showPriceSectionModal(){
      this.isPriceSectionModalShow = true;
    },
    hidePriceSectionModal(){
      this.isPriceSectionModalShow = false;
    }
  },
})