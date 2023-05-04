<template>
  <el-dialog
    :title="noticeDetailDialogData.noticeDetailTitle"
    :visible.sync="noticeDetailDialogData.noticeDetailDialogVisible"
    width="1000px"
    :close-on-click-modal="false"
    @close="closeDialog()"
  >
    <el-form :model="notice" :rules="rules" ref="noticeForm" label-width="90px">
      <el-form-item label="title" prop="title">
        <el-input v-model="notice.title" auto-complete="off" size="medium" placeholder="Please enter the title"></el-input>
      </el-form-item>
      <el-form-item label="release time" prop="public_time">
        <el-date-picker
          v-model="notice.public_time"
          type="datetime"
          placeholder="Selection date time"
          align="right"
          size="medium"
          ref="datePoint"
        ></el-date-picker>
      </el-form-item>
      <el-form-item label="Status" prop="status">
        <el-select
          v-model="notice.status"
          clearable
          class="input-width"
          size="medium"
          placeholder="State selection"
        >
          <el-option
            v-for="dict in noticeDetailDialogData.statusOptions"
            :key="dict.dict_value"
            :label="dict.dict_label"
            :value="dict.dict_value"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Content" prop="content">
        <tinymce :height="300" v-model="notice.content" id="tinymce" ref="contentEditor"></tinymce>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('noticeForm')">Submit</el-button>
      <el-button v-if="!noticeDetailDialogData.isEdit" @click="resetForm('noticeForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createNotice,
  updateNotice,
  editNotice,
} from '@/api/setting/system_module/notice'
import Tinymce from '@/components/Tinymce'
const defaultNotice = {
  title: '',
  status: '',
  content: '',
  public_time: '',
}
export default {
  name: 'NoticeDetail',
  components: { Tinymce },
  props: {
    noticeDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      notice: Object.assign({}, defaultNotice),
      rules: {
        title: [
          { required: true, message: 'Please fill in the title', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
        status: [{ required: true, message: 'Please select status', trigger: 'blur' }],
        public_time: [
          { required: true, message: 'Please select the release time', trigger: 'blur' },
        ],
      },
    }
  },
  created() {},
  methods: {
    getNoticeInfo() {
      //Judging whether it is a modification
      if (this.noticeDetailDialogData.isEdit == true) {
        editNotice(this.noticeDetailDialogData.id).then((response) => {
          if (response.code == 200) {
            let noticeData = response.data.list
            this.notice = Object.assign({}, noticeData)
            this.$refs.contentEditor.setContent(noticeData.content)
          }
        })
      } else {
        this.notice = Object.assign({}, defaultNotice)
      }
    },
    onSubmit(noticeForm) {
      this.$refs[noticeForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.noticeDetailDialogData.isEdit) {
              updateNotice(this.notice.id, this.notice).then((response) => {
                if (response.code == 200) {
                  this.$refs[noticeForm].resetFields()
                  this.$parent.getList()
                  this.noticeDetailDialogData.noticeDetailDialogVisible = false
                }
              })
            } else {
              createNotice(this.notice).then((response) => {
                if (response.code == 200) {
                  this.$refs[noticeForm].resetFields()
                  this.notice = Object.assign({}, defaultNotice)
                  this.$parent.getList()
                  this.noticeDetailDialogData.noticeDetailDialogVisible = false
                }
              })
            }
          })
        } else {
          this.$message({
            message: 'Verification failed',
            type: 'error',
            duration: 1000,
          })
          return false
        }
      })
    },
    resetForm(noticeForm) {
      this.$refs[noticeForm].resetFields()
      this.brand = Object.assign({}, defaultNotice)
    },
    closeDialog() {
      this.$refs.contentEditor.setContent('')
    },
  },
}
</script>

<style>
</style>
