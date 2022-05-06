import Vue  from "vue";
import Vuetify from 'vuetify'

Vue.use(Vuetify)
import  ru from 'vuetify/lib/locale/ru'

const opts = {
    icons: {
        iconfont: 'mdi', // default - only for display purposes
    },
    lang: {
        locales: { ru: ru },
        current: 'ru',
    },
}
export default new Vuetify(opts)
