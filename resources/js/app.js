require('./bootstrap');

window.Vue = require('vue').default;
import Vuetify from "./Plugins/Vuetify";
import { Lang } from 'laravel-vue-lang';

Vue.use(Lang, {
    locale: 'ru',
    fallback: 'ru',
    ignore: {
        // fr: ['validation'],
    },
});

Vue.component('search-component', require('./components/SearchComponent.vue').default);
Vue.component('control-component', require('./components/ControlComponent.vue').default);
Vue.component('view-component', require('./components/ViewComponent.vue').default);


const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
    data:{
    },
    methods:{
    }
});
