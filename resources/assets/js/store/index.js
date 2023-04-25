import cart from './modules/cart'
import {config} from './modules/config'
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        cart,
        config
    }
});
