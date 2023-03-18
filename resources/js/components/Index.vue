<template>
    <a-layout class="layout">
        <a-layout-header class="menu_panel">
            <a-menu
                v-model:selectedKeys="selectedKeys"
                theme="light"
                mode="horizontal"
                :style="{ lineHeight: '64px' }"
            >
                <a-menu-item key="chat">chat</a-menu-item>
                <a-menu-item key="create_image">图片生成</a-menu-item>
            </a-menu>

        </a-layout-header>
        <a-layout-content>
            <chat v-if="selectedKeys[0] === 'chat'" style="margin: 20px auto;"></chat>
            <create-image v-if="selectedKeys[0] === 'create_image'" style="margin: 20px auto;"></create-image>
        </a-layout-content>
        <a-layout-footer class="layout_footer">
            本站对接的第三方api,仅提供学习AI使用
        </a-layout-footer>
    </a-layout>
</template>

<script>
import Chat from "@/components/chat.vue";
import CreateImage from "@/components/createImage.vue";
import {ref} from "vue";

export default {
    name: "Index",
    components: {Chat, CreateImage},
    setup() {
        return {
            selectedKeys: ref(['chat']),
        };
    }
}
</script>

<style scoped lang="scss">
.menu_panel {
    background-color: #fff;
    box-sizing: border-box;
    border-bottom: 1px solid #f0f0f0;
}

.layout {
    .layout_footer {
        margin-top: 40px;
        text-align: center;
    }
}


</style>
