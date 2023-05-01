<template>
    <span>
        <el-tooltip
          :effect="effect"
          :placement="placement"
        >
            <div v-html="toBreak(content)" slot="content"></div>
            <slot name="tipBtn">
                <i class="el-icon-question" :style="{color: btnColor}"></i>
            </slot>
        </el-tooltip>
    </span>
</template>

<script>
export default {
    name: 'Tip',
    props: {
        //style theme
        effect: {
            type: String,
            default: 'dark'
        },
        //Prompt content
        content: {
            type: String,
            default: ''
        },
        //Prompt position
        placement: {
            type: String,
            defualt: 'top'
        },
        //Hint button color
        btnColor: {
            type: String,
            default: '#1989fa'
        }
    },
    methods: {
        toBreak (val) {
          return val.replace(/\n/g, '<br />');
        },
    }
}
</script>
