<template>
  <el-dialog
    :title="adviceDetailDialogData.adviceDetailTitle"
    :visible.sync="adviceDetailDialogData.adviceDetailDialogVisible"
    width="900px"
    :close-on-click-modal="false"
    @close="closeDialog()"
  >
    <el-form :model="advice" :rules="rules" ref="adviceForm" label-width="90px">
      <el-form-item label="Title" prop="title">
        <el-input v-model="advice.title" auto-complete="off" size="medium" placeholder="Please enter the title"></el-input>
      </el-form-item>
      <el-form-item label="Type" prop="type">
        <el-select
          v-model="advice.type"
          clearable
          class="input-width"
          size="medium"
          placeholder="Type selection"
        >
          <el-option
            v-for="dict in adviceDetailDialogData.typeOptions"
            :key="dict.dict_value"
            :label="dict.dict_label"
            :value="dict.dict_value"
          ></el-option>
        </el-select>
        <span>(Priority processing bug)</span>
      </el-form-item>
      <el-form-item label="Content" prop="content">
        <tinymce :height="300" v-model="advice.content" id="tinymce" ref="contentEditor"></tinymce>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('adviceForm')">Submit</el-button>
      <el-button v-if="!adviceDetailDialogData.isEdit" @click="resetForm('adviceForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createAdvice,
  updateAdvice,
  editAdvice,
} from '@/api/setting/system_module/advice'
import Tinymce from '@/components/Tinymce'
const defaultAdvice = {
  title: '',
  type: '',
  content: '',
}
export default {
  name: 'AdviceDetail',
  components: { Tinymce },
  props: {
    adviceDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      advice: Object.assign({}, defaultAdvice),
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
        type: [{ required: true, message: 'Please choose the type', trigger: 'blur' }],
      },
    }
  },
  created() {},
  methods: {
    getAdviceInfo() {
      //Judging whether it is a modification
      if (this.adviceDetailDialogData.isEdit == true) {
        editAdvice(this.adviceDetailDialogData.id).then((response) => {
          if (response.code == 200) {
            let adviceData = response.data.list
            this.advice = Object.assign({}, adviceData)
            this.$refs.contentEditor.setContent(adviceData.content)
          }
        })
      } else {
        this.advice = Object.assign({}, defaultAdvice)
      }
    },
    onSubmit(adviceForm) {
      this.$refs[adviceForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'Sure',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.adviceDetailDialogData.isEdit) {
              updateAdvice(this.advice.id, this.advice).then((response) => {
                if (response.code == 200) {
                  this.$refs[adviceForm].resetFields()
                  this.$parent.getList()
                  this.adviceDetailDialogData.adviceDetailDialogVisible = false
                }
              })
            } else {
              createAdvice(this.advice).then((response) => {
                if (response.code == 200) {
                  this.$refs[adviceForm].resetFields()
                  this.advice = Object.assign({}, defaultAdvice)
                  this.$parent.getList()
                  this.adviceDetailDialogData.adviceDetailDialogVisible = false
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
    resetForm(adviceForm) {
      this.$refs[adviceForm].resetFields()
      this.brand = Object.assign({}, defaultAdvice)
    },
    closeDialog() {
      this.$refs.contentEditor.setContent('')
    },
  },
}
</script>

<style>
</style>
