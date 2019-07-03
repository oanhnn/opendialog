/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/**
 * Now we import the Vue router.
 */
import router from '@/router/index';

import App from '@/views/App';
import BootstrapVue from 'bootstrap-vue';

require('@/bootstrap');
window.Vue = require('vue');

window.Vue.use(BootstrapVue);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const { app } = new window.Vue({
  el: '#app',
  components: { App },
  router,
});
