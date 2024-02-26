import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'
import layout from './modules/layout'

Vue.use(Vuex)

const store = new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',

    plugins: [
        createPersistedState()
    ],
    modules: {
        auth,
        layout,
    }
})
export default store
