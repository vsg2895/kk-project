const namespaced = true;

const state = {
    lessonsCount: 15,
    phones: {
        customers: {
            text : '010 58 58 590',
            regular : '0105858590'
        }
    }
};

const actions = {
    setLessonsCount({commit}, count = null) {
        commit('setLessonsCount', count);
    }
};

const mutations = {
    setLessonsCount(state, count = null) {
        if (!this._vm['$localStorage'].get('lessonsCount') || count) {
            this._vm['$localStorage'].set('lessonsCount', count || 15);
        }

        state.lessonsCount = parseInt(this._vm['$localStorage'].get('lessonsCount'));
    }
};

const getters = {
    getLessonsCount(state) {
        return state.lessonsCount;
    },
    getLessonsCountPlural(state) {
        return state.lessonsCount > 1 ? 'körlektioner' : 'körlektioner';
    },
    getPhones(state) {
        return state.phones;
    }
};

export const config = {
    namespaced,
    state,
    actions,
    mutations,
    getters
};
