import { createApp } from "vue";
import { createPinia } from "pinia";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
// import { library } from "@fortawesome/fontawesome-svg-core";
import router from "./router";
import vClickOutside from "click-outside-vue3";
import App from "./App.vue";

import "swiper/css";

// import "./assets/css/normalize.scss";
// import "./assets/css/default.scss";

// import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
// library.add(faUserSecret)

const pinia = createPinia();
const app = createApp(App);
app
.use(pinia)
.use(router)
.use(vClickOutside)
.component("font-awesome-icon", FontAwesomeIcon)
.mount("#app");

createApp(App)


