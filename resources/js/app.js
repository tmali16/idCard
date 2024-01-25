// import Vue from "vue";

require('./bootstrap');

window.Vue = require('vue').default;
import Vuetify from "./Plugins/Vuetify";
import { Lang } from 'laravel-vue-lang/dist';
window.L = require('leaflet/dist/leaflet');
require('./Plugins/Semicircle')
require('leaflet-draw/dist/leaflet.draw')

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
// Vue.component('pro-map-component', require('./components/ProMapComponent.vue').default);
// Vue.component('location-component', require('./components/LocationComponent.vue').default);

// console.log($lang("form.lac"))
const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
});
window.azimuthToText = function (azimuth) {
    let res = '';
    if(azimuth <= 10 && azimuth >= 350){
        res = ' (в северном направлении)';
    }else if(azimuth > 10 && azimuth < 80){
        res = ' (в северо-восточном направлении)';
    }else if(azimuth >= 80 && azimuth <= 100){
        res = ' (в восточном направлении)';
    }else if(azimuth > 100 && azimuth < 170){
        res = ' (в юго-восточном направлении)';
    }else if(azimuth >= 170 && azimuth <=190){
        res = ' (в южном направлении)';
    }else if(azimuth > 190 && azimuth < 260){
        res = ' (в юго-западном направлении)';
    }else if(azimuth > 280 && azimuth < 350){
        res = ' (в северо-западном направлении)';
    }else if(azimuth <= 280 && azimuth >=170 ){
        res = ' (в западном направлении)';
    }else{
        res = '';
    }
    return res;
}
