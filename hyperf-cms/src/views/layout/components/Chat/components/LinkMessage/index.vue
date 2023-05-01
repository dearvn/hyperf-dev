<template>
  <el-dialog
    title="Link insertion"
    :visible.sync="linkMessageDialogData.visible"
    width="600px"
    :close-on-click-modal="true"
    :append-to-body="true"
    :close-on-press-escape="false"
    class="field-dialog"
    @close="closeDialog"
  >
    <el-form :model="form" ref="linkForm">
      <el-form-item label="Link：" label-width="80px">
        <el-input v-model="linkMessageDialogData.content" placeholder="Please fill in the sending link" autofocus></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="linkMessageDialogData.visible = false">Cancel</el-button>
      <el-button type="primary" @click="submit">Send</el-button>
    </div>
  </el-dialog>
</template>
<script>
export default {
  name: 'linkMessage',
  props: {
    linkMessageDialogData: {
      type: Object,
      default: {},
    },
  },
  components: {},
  data() {
    return {
      form: {},
    }
  },
  mounted() {},

  created() {},
  methods: {
    closeDialog() {
      this.$refs.linkForm.resetFields()
      this.linkMessageDialogData.content = ''
    },
    submit() {
      this.$parent.$parent.sendLinkMessage(this.linkMessageDialogData.content)
    },
  },
}
</script>