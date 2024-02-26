import Vue from 'vue';
import App from './App.vue';
import "./bootstrap.js";
import BootstrapVue from 'bootstrap-vue';
import Vuelidate from 'vuelidate';
import VueSweetalert2 from 'vue-sweetalert2';
import vco from "v-click-outside";
import router from './router';
import store from './state/store';
import iziToast from './assets/plugin/iziToastPlugin';
import _ from 'lodash';
import FormValidation from '@/components/form-validation.vue';

const layoutColor = localStorage.getItem('layoutColor');
if (layoutColor == 'dark') {
    require('@/assets/scss/app-dark-layout.scss');
} else {
    require('@/assets/scss/app.scss');
}

Vue.prototype.axios = window.axios;
Vue.prototype._ = _;
Vue.config.productionTip = false;
Vue.use(iziToast);
Vue.use(BootstrapVue);
Vue.use(vco);
Vue.use(Vuelidate);
Vue.use(VueSweetalert2);
Vue.component('FormValidation', FormValidation);

new Vue({
    router: router,
    store: store,
    render: h => h(App)
}).$mount('#app')