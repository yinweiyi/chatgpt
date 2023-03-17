<template>
    <div id="content">
        <a-spin :spinning="spinning">
            <div class="sch-box">
                <a-input type="text" class="f-text" @keyup.enter="chat" name="question" v-model:value="question"
                         placeholder="iphone15 pro 配置参数"/>
                <a-input type="submit" class="btn-search" @click="chat" value="提交"/>
                <a-input type="button" class="btn-clear" @click="clearQuestions" value="清空对话"/>
            </div>
            <div class="list" v-for="item in list">
                <strong class="question">Q：{{ item.question }} </strong>
                <p class="answer">
                    {{ item.answer }}
                </p>
            </div>
        </a-spin>
    </div>
</template>

<script>
import {ref, computed, onMounted} from "vue";
import axios from "axios";
import {message} from "ant-design-vue";
import {useStore} from "vuex";

const chatEffect = (store) => {

    const list = computed(() => store.getters['questionList']);
    const question = ref('')
    const spinning = ref(false)
    const chat = () => {
        if (!question.value) {
            message.error('内容不能为空');
            return
        }
        spinning.value = true
        axios.post('/api/chatgpt/chat', {
            question: formatPrompt(question.value)
        }).then(response => {
            if (response.data.answer.text) {
                store.commit('appendQuestion', {
                    question: response.data.question,
                    answer: response.data.answer.text
                })
            } else {
                message.error('服务器错误，请稍后重试')
            }
            spinning.value = false;
        })
    }

    const formatPrompt = (question) => {
        let questions = [];
        for (let i in list.value) {
            let item = list.value[i];
            questions.push('Human:' + item.question + '\nAI:' + item.answer);
        }
        questions.push('Human:' + question)
        return questions.join('\n');
    }
    const clearQuestions = () => {
        store.commit('clearQuestions')
    }

    return {chat, list, question, spinning, clearQuestions}
}

export default {
    name: "Index",
    setup() {
        const store = useStore();
        onMounted(() => {
            store.commit('initQuestions')
        })
        const {list, question, spinning, chat, clearQuestions} = chatEffect(store)
        return {list, question, spinning, chat, clearQuestions}
    }
}
</script>

<style scoped lang="scss">
.sch-box {
    width: 900px;
    margin: 10px auto 0 auto;

    .f-text {
        border: 3px solid #6abb79;
        padding: 10px;
        height: 40px;
        width: 600px;
        display: inline-block;
    }

    .btn-search {
        background-color: #6abb78;
        font-size: 20px;
        color: #fff;
        border: 0;
        vertical-align: top;
        height: 40px;
        width: 120px;
    }

    .btn-clear {
        background-color: gray;
        font-size: 20px;
        color: #fff;
        border: 0;
        vertical-align: top;
        height: 40px;
        width: 100px;
    }
}

#content {
    width: 900px;
    padding-right: 20px;
    padding-top: 5px;
    margin: 0 auto;
    font-size: 14px;
    font-family: Arial, Helvetica, sans-serif;

    .list {
        font-size: 14px;
        line-height: 24px;
        padding: 10px 0 5px;

        .answer {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-top: 5px;
            border-radius: 2px;
            padding: 10px;
            background-color: #eee;
        }
    }
}
</style>
