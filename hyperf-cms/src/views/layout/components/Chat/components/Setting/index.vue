<template>
  <el-dialog
    title="Set up"
    :visible.sync="settingDialogData.visible"
    width="650px"
    :close-on-click-modal="true"
    :append-to-body="true"
    class="field-dialog"
  >
    <el-tabs tab-position="left" style="height:350px">
      <el-tab-pane label="Universal settings">
        <div style="margin-bottom:20px">
          <span style="margin-left:20px; font-size:14px">send Message：</span>
          <el-radio-group v-model="settingDialogData.sendText" size="medium">
            <el-radio-button label="Enter">Enter</el-radio-button>
            <el-radio-button label="Enter+Ctrl">Enter + Ctrl</el-radio-button>
          </el-radio-group>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:20px; font-size:14px">Theme choice：</span>
          <el-radio-group v-model="settingDialogData.theme" size="medium">
            <el-radio-button label="default"></el-radio-button>
            <el-radio-button label="blue"></el-radio-button>
          </el-radio-group>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.avatarCricle" active-text="Open the chat round avatar (need to refresh)"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.hideMessageName" active-text="Whether to hide the name of the contact person in the chat window"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.hideMessageTime" active-text="Whether to hide the message delivery time in the chat window"></el-switch>
        </div>
      </el-tab-pane>
      <el-tab-pane label="message notification">
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.friendOnlineNotice" active-text="Open the user online notice"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.friendOnlineNoticeTone" active-text="Turn on the user's online notification prompt sound"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.messagePagePrompt" active-text="Turn on the new message page prompt"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:30px; font-size:14px"></span>
          <el-switch v-model="settingDialogData.messageTone" active-text="Open the new message prompt sound"></el-switch>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:20px; font-size:14px">Choice：</span>
          <el-select
            v-model="settingDialogData.messageToneType"
            placeholder="Please select message prompt sound"
            @change="playAudio(settingDialogData.messageToneType)"
          >
            <el-option
              v-for="item in messageToneTypeOption"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </div>
      </el-tab-pane>
      <el-tab-pane label="About IM">
        <div style="margin-bottom:20px">
          <span style="margin-left:20px; margin-right:20px;font-size:14px">Version Information：</span>
          <span>1.0.0</span>
        </div>
        <div style="margin-bottom:20px">
          <span style="margin-left:20px; margin-right:20px;font-size:14px">Manual：</span>
          <el-button size="medium" @click="clickHelp()">View</el-button>
        </div>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
</template>
<script>
export default {
  name: 'Setting',
  props: {
    settingDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      messageToneTypeOption: [
        { label: '提示音1', value: 'messageTone1.mp3' },
        { label: '提示音2', value: 'messageTone2.mp3' },
        { label: '提示音3', value: 'messageTone3.mp3' },
        { label: '提示音4', value: 'messageTone4.mp3' },
        { label: '提示音5', value: 'messageTone5.mp3' },
        { label: '提示音6', value: 'messageTone6.mp3' },
        { label: '提示音7', value: 'messageTone7.mp3' },
        { label: '提示音8', value: 'messageTone8.mp3' },
        { label: '提示音9', value: 'messageTone9.mp3' },
        { label: '提示音10', value: 'messageTone10.mp3' },
      ],
    }
  },
  mounted() {},
  watch: {
    'settingDialogData.sendText'(val) {
      this.$store.commit('SET_SEND_TEXT', val)
    },
    'settingDialogData.theme'(val) {
      this.$store.commit('SET_LEMON_THEME', val)
    },
    'settingDialogData.avatarCricle'(val) {
      this.$store.commit('SET_AVATAR_CRICLE', val)
    },
    'settingDialogData.hideMessageName'(val) {
      this.$store.commit('SET_HIDE_MESSAGE_NAME', val)
    },
    'settingDialogData.hideMessageTime'(val) {
      this.$store.commit('SET_HIDE_MESSAGE_TIME', val)
    },
    'settingDialogData.friendOnlineNotice'(val) {
      this.$store.commit('SET_FRIEND_ONLINE_NOTICE', val)
    },
    'settingDialogData.friendOnlineNoticeTone'(val) {
      this.$store.commit('SET_FRIEND_ONLINE_NOTICE_TONE', val)
    },
    'settingDialogData.messagePagePrompt'(val) {
      this.$store.commit('SET_MESSAGE_PAGE_PROMPT', val)
    },
    'settingDialogData.messageTone'(val) {
      this.$store.commit('SET_MESSAGE_TONE', val)
    },
    'settingDialogData.messageToneType'(val) {
      this.$store.commit('SET_MESSAGE_TONE_TYPE', val)
    },
  },
  created() {},
  methods: {
    init() {},
    clickHelp() {
      location.href = 'http://june000.gitee.io/lemon-im/'
    },
  },
}
</script>


<style lang="scss" scoped>
</style>
