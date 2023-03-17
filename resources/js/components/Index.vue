<template>
    <div class="chat_window">
        <div class="top_menu">
            <div class="buttons">
                <div class="button close"></div>
                <div class="button minimize"></div>
                <div class="button maximize"></div>
            </div>
            <div class="title">ChatGPT</div>
            <button class="btn_clear" @click="clearMessages">清空聊天</button>
        </div>
        <ul class="messages" ref="messageBox">
            <li class="message_qa" v-for="item in list">
                <div class="message right" v-if="item.role === 'user'">
                    <div class="avatar">Q</div>
                    <span class="text_wrapper">
                        {{ item.content }}
                    </span>
                </div>
                <div class="message left" v-else>
                    <div class="avatar">A</div>
                    <span class="text_wrapper">
                        {{ item.content }}
                    </span>
                </div>
            </li>

            <li class="message_qa" v-if="spinning">
                <div class="message left">
                    <div class="avatar">A</div>
                    <span class="text_wrapper">
                        <a-spin :spinning="spinning"></a-spin>
                    </span>
                </div>
            </li>



        </ul>
        <div class="bottom_wrapper clearfix">
            <textarea class="message_input_wrapper" placeholder="点击这里输入问题" v-model="question"></textarea>
            <button class="send_message" @click="chat">提交</button>
        </div>
    </div>
</template>

<script>
import {ref, computed, onMounted, nextTick} from "vue";
import axios from "axios";
import {message} from "ant-design-vue";
import {useStore} from "vuex";


const chatEffect = (store) => {
    const list = computed(() => store.getters['questionList']);
    const question = ref('')
    const messageBox = ref(null)
    const spinning = ref(false)


    const chat = () => {
        const data = question.value
        if (!data) {
            message.error('内容不能为空');
            return
        }
        store.commit('appendMessage', {role: 'user', content: data})
        question.value = ''
        spinning.value = true
        axios.post('/api/chatgpt/chat', {
            type: chat,
            messages: list.value.map(item => item.data)
        }).then(response => {
            if (response.data.data) {
                store.commit('appendMessage', response.data.data)
            } else {
                message.error('错误：服务器错误。。')
            }
        }).finally(() => {
            spinning.value = false
        })
        nextTick(() => {
            messageBox.value.scrollTop = messageBox.value.offsetHeight + messageBox.value.offsetTop
        })
    }
    const clearMessages = () => {
        store.commit('clearMessages')
    }

    return {chat, list, question, spinning, messageBox, clearMessages}
}

export default {
    name: "Index",
    setup() {
        const store = useStore();
        const {list, question, spinning, messageBox, chat, clearMessages} = chatEffect(store)

        const type = ref('chat')

        onMounted(() => {
            store.commit('initMessages')
            nextTick(() => {
                messageBox.value.scrollTop = messageBox.value.offsetHeight + messageBox.value.offsetTop
            })
        })

        return {list, question, spinning, type, messageBox, chat, clearMessages}
    }
}
</script>

<style scoped lang="scss">
.chat_window {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateX(-50%) translateY(-50%);
    width: calc(100% - 20px);
    max-width: 800px;
    height: 600px;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    background-color: #f8f8f8;
}


.top_menu {
    background-color: #fff;
    width: 100%;
    padding: 20px 0 15px;
    box-shadow: 0 1px 30px rgba(0, 0, 0, 0.1);

    .buttons {
        margin: 3px 0 0 20px;
        position: absolute;

        .button {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            position: relative;
        }

        .button.close {
            background-color: #f5886e;
        }

        .button.minimize {
            background-color: #fdbf68;
        }

        .button.maximize {
            background-color: #a3d063;
        }
    }

    .title {
        text-align: center;
        color: #bcbdc0;
        font-size: 20px;
    }

    .btn_clear {
        top: 20px;
        right: 20px;
        position: absolute;
        width: 80px;
        height: 30px;
        display: inline-block;
        border-radius: 30px;
        background-color: gray;
        border: 2px solid gray;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s linear;
        text-align: center;

        &:hover {
            color: gray;
            background-color: #fff;
        }
    }
}

.messages {
    position: relative;
    list-style: none;
    padding: 20px 10px 0 10px;
    margin: 0;
    height: 447px;
    overflow-x: hidden;
    overflow-y: auto;

    .message {
        clear: both;
        overflow: hidden;
        margin-bottom: 20px;
        transition: all 0.5s linear;

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 60px;
        }

        .text_wrapper {
            display: inline-block;
            padding: 20px;
            border-radius: 6px;
            max-width: calc(100% - 160px);
            min-width: 100px;
            position: relative;

            .text {
                font-size: 18px;
                font-weight: 300;
            }

            &:after, &:before {
                top: 18px;
                border: solid transparent;
                content: " ";
                height: 0;
                width: 0;
                position: absolute;
                pointer-events: none;
            }

            &:after {
                border-width: 13px;
                margin-top: 0;
            }

            &:before {
                border-width: 15px;
                margin-top: -2px;
            }
        }
    }

    .message.left {
        .avatar {
            background-color: #f5886e;
            float: left;
        }

        .text_wrapper {
            background-color: #ffe6cb;
            margin-left: 20px;
            white-space: pre-wrap;
            word-wrap: break-word;

            &:after, &:before {
                right: 100%;
                border-right-color: #ffe6cb;
            }
        }

        .text {
            color: #c48843;
        }
    }

    .message.right {
        .avatar {
            background-color: #fdbf68;
            float: right;
        }

        .text_wrapper {
            background-color: #c7eafc;
            margin-right: 20px;
            float: right;

            &:after, &:before {
                left: 100%;
                border-left-color: #c7eafc;
            }
        }

        .text {
            color: #45829b;
        }
    }
}


.bottom_wrapper {
    width: 100%;
    background-color: #fff;
    padding: 20px 20px;
    position: absolute;
    bottom: 0;

    .message_input_wrapper {
        display: inline-block;
        height: 50px;
        line-height: 50px;
        border-radius: 25px;
        border: 1px solid #bcbdc0;
        width: calc(100% - 160px);
        position: relative;
        padding: 0 20px;

        .message_input {
            border: none;
            height: 100%;
            box-sizing: border-box;
            width: calc(100% - 40px);
            position: absolute;
            outline-width: 0;
            color: gray;
        }
    }

    .send_message {
        width: 140px;
        height: 50px;
        display: inline-block;
        border-radius: 50px;
        background-color: #a3d063;
        border: 2px solid #a3d063;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s linear;
        text-align: center;
        float: right;

        &:hover {
            color: #a3d063;
            background-color: #fff;
        }

        .text {
            font-size: 18px;
            font-weight: 300;
            display: inline-block;
            line-height: 48px;
        }
    }
}
</style>
