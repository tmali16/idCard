// import Vue from "vue";

require('./bootstrap');

window.Vue = require('vue').default;
import Vuetify from "./Plugins/Vuetify";
import { Lang } from 'laravel-vue-lang/dist';
window.L = require('leaflet/dist/leaflet');

Vue.use(Lang, {
    locale: 'ru',
    fallback: 'ru',
    ignore: {
        // fr: ['validation'],
    },
});

// Vue.component('search-component', require('./components/SearchComponent.vue').default);
// Vue.component('control-component', require('./components/ControlComponent.vue').default);
// Vue.component('view-component', require('./components/ViewComponent.vue').default);
Vue.component('map-component', require('./components/MapComponent.vue').default);

// console.log($lang("form.lac"))
const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
});
