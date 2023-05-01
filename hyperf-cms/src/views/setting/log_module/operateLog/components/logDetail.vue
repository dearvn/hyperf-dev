<template>
  <!--Detailed operation log-->
  <el-dialog
    :title="logDetailDialogData.logDetailTitle"
    :visible.sync="logDetailDialogData.logDetailDialogVisible"
    width="800px"
    append-to-body
  >
    <el-form
      ref="logDetailForm"
      :model="logDetailDialogData.logDetailData"
      label-width="100px"
      size="mini"
    >
      <el-row>
        <el-col :span="12">
          <el-form-item label="Operating behavior：">{{ logDetailDialogData.logDetailData.action }}</el-form-item>
          <el-form-item
            label="login information："
          >{{ logDetailDialogData.logDetailData.uid }} / {{ logDetailDialogData.logDetailData.username }} / {{ logDetailDialogData.logDetailData.operator }}</el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Request address：">{{ logDetailDialogData.logDetailData.target_url }}</el-form-item>
          <el-form-item
            label="Request information："
          >{{ logDetailDialogData.logDetailData.request_method}} / {{ logDetailDialogData.logDetailData.request_ip }}</el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Target controller">{{ logDetailDialogData.logDetailData.target_class }}</el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Target：">{{ logDetailDialogData.logDetailData.target_method }}</el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="Request parameters：" v-if="logDetailDialogData.logDetailData.data != undefined">
            <json-viewer
              :value="JSON.parse(logDetailDialogData.logDetailData.data)"
              :expand-depth="3"
              copyable
              boxed
            ></json-viewer>
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item
            label="Response result："
          >{{ logDetailDialogData.logDetailData.response_code}} / {{ logDetailDialogData.logDetailData.response_result}}</el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="Operation time：">{{ logDetailDialogData.logDetailData.created_at}}</el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item
            label="Abnormal information："
            v-if="logDetailDialogData.logDetailData.status != 200"
          >{{ logDetailDialogData.logDetailData.response_result }}</el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="closeDialog()">Close</el-button>
    </div>
  </el-dialog>
</template>
<script>
import JsonViewer from 'vue-json-viewer'
import 'vue-json-viewer/style.css'
export default {
  name: 'logDetail',
  components: { JsonViewer },
  props: {
    logDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  methods: {
    closeDialog() {
      this.logDetailDialogData.logDetailDialogVisible = false
    },
  },
}
</script>

<style>
</style>
