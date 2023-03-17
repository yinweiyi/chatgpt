import {createStore} from 'vuex'

import localStorage from "../utils/localStorage";

export default createStore({
    state: {
        messages: [],
    },
    getters: {
        questionList: (state) => {
            return state.messages
        }
    },
    mutations: {
        appendMessage: (state, message) => {
            state.messages.push(message)
            localStorage.set('messages', state.messages)
        },
        initMessages: (state) => {
            state.messages = localStorage.get('messages') || []
        },
        clearMessages:(state) => {
            state.messages = [];
            localStorage.remove('messages')
        }
    },
    action: {},
})
