<template>
    <div class="image_window">
        <a-form :model="formState" :label-col="labelCol">
            <a-form-item label="描述">
                <a-input v-model:value="formState.prompt"/>
            </a-form-item>

            <a-form-item label="生成数量(个)">
                <a-select v-model:value="formState.n">
                    <a-select-option v-for="i in nums" :value="i">{{ i }}</a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item label="尺寸">
                <a-radio-group v-model:value="formState.size">
                    <a-radio value="256x256">256x256</a-radio>
                    <a-radio value="512x512">512x512</a-radio>
                    <a-radio value="1024x1024">1024x1024</a-radio>
                </a-radio-group>
            </a-form-item>
            <a-form-item label="图片">

                <a-spin :spinning="spinning">
                    <div class="images">
                        <a-image-preview-group>
                            <a-image :width="200" src="https://aliyuncdn.antdv.com/vue.png" />
                            <a-image :width="200" src="https://aliyuncdn.antdv.com/logo.png" />

                            <a-image class="image" v-for="image in images" :width="200" :src="image.url"/>
                        </a-image-preview-group>
                    </div>
                </a-spin>
            </a-form-item>

            <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                <a-button type="primary" @click="onSubmit">生成</a-button>
            </a-form-item>
        </a-form>
    </div>
</template>

<script>
import {defineComponent, reactive, ref} from 'vue';
import axios from "axios";
import {message} from "ant-design-vue";

export default defineComponent({
    name: "createImage",
    setup() {
        const formState = reactive({
            prompt: '',
            n: 1,
            size: '256x256',
        });

        const nums = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

        const images = ref([])
        const spinning = ref(false)

        const onSubmit = () => {
            if (!formState.prompt) {
                message.error('描述不能为空')
                return
            }
            spinning.value = true
            axios.post('/api/chatgpt/chat', {
                type: 'image_create',
                params: formState
            }).then(response => {
                if (response.data.data !== null) {
                    images.value = response.data.data;
                } else {
                    message.error('错误：服务器错误。。')
                }
            }).finally(() => {
                spinning.value = false
            })
        };
        return {
            labelCol: {
                style: {
                    width: '150px',
                },
            },
            formState, nums, images, spinning,
            onSubmit,
        };
    },
});
</script>

<style lang="scss">
.image_window {
    width: calc(100% - 20px);
    max-width: 800px;
    min-height: 600px;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    padding: 50px 10px 0 0;
    overflow: hidden;
    background-color: #fff;

    .images {
        height: 440px;
        overflow-x: hidden;
        overflow-y: auto;
        background-color: #f3f2f2;

        .ant-image {
            margin: 5px;
        }
    }
}


</style>
