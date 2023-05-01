<template>
  <el-dialog
    :title="this.messageType[this.messageView.messageType]"
    :visible.sync="messageDialogVisible"
    width="40%"
    :close-on-click-modal="false"
    :show-close="false"
    class="message-dialog"
  >
    <el-card class="box-card" shadow="never" v-if="this.messageView.messageType == 'notice'">
      <h3 v-html="messageView.title"></h3>
      <p style="color: #999;font-style: italic" v-html="messageView.created_at"></p>
      <div v-html="messageView.content"></div>
    </el-card>
    <el-card class="box-card" shadow="never" v-else-if="this.messageView.messageType == 'advice'">
      <div class="text item">
        <h3 v-html="messageView.title"></h3>
        <div class="content" v-html="messageView.content"></div>
        <div class="devide-line"></div>
        <p style="font-size: 14px;font-weight: bold;">反馈：</p>
        <div class="reply" v-html="messageView.reply"></div>
      </div>
    </el-card>
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" @click="showPrevMessage" v-if="messageInfo.prevBtn">Last</el-button>
      <el-button type="primary" @click="showNextMessage" v-if="messageInfo.nextBtn">Next</el-button>
      <el-button
        type="primary"
        @click="messageDialogVisible=false"
        v-if="this.messageInfo.closeBtn"
      >OK</el-button>
    </span>
  </el-dialog>
</template>

<script>
export default {
  name: 'MessageDialog',
  props: {
    messageList: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      messageDialogVisible: false,
      messageView: [],
      messageInfo: {
        nextList: [],
        prevList: [],
        nextBtn: false,
        prevBtn: false,
        closeBtn: true,
      },
      messageType: {
        notice: 'Latest notification',
        advice: 'Feedback',
      },
    }
  },
  watch: {
    messageList(val) {
      //Show popup if there is a message in the list
      if (this.messageList.length > 0) {
        this.showSystemMessage()
      }
    },
  },
  methods: {
    showSystemMessage() {
      //Show message popup
      let len = this.messageList.length
      for (let i = 0; i < len; i++) {
        this.messageInfo.nextList.push(this.messageList[i])
      }

      this.messageDialogVisible = true
      this.messageView = this.messageInfo.nextList.shift()
      if (this.messageInfo.nextList.length > 0) {
        this.messageInfo.nextBtn = true
        this.messageInfo.closeBtn = false
      }
    },
    showNextMessage() {
      //show next message
      this.messageInfo.prevList.push(this.messageView) //Put the current message in the queue of read messages
      this.messageInfo.prevBtn = true
      this.messageView = this.messageInfo.nextList.shift() //get next message
      if (this.messageInfo.nextList.length == 0) {
        this.messageInfo.nextBtn = false
        this.messageInfo.closeBtn = true
      }
    },
    showPrevMessage() {
      //show previous message
      this.messageInfo.nextList.unshift(this.messageView) //Put the current message back on the next message queue
      this.messageInfo.nextBtn = true
      this.messageView = this.messageInfo.prevList.pop() //get last message
      if (this.messageInfo.prevList.length == 0) {
        this.messageInfo.prevBtn = false
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.devide-line {
  width: 100%;
  height: 2px;
  background-color: #ddd;
}

.message-dialog ::v-deep .content img,
.message-dialog ::v-deep .reply img {
  max-width: 100%;
  height: auto;
}
</style>