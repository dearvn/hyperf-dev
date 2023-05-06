<template>
  <el-dialog
    :title="templateDetailDialogData.templateDetailTitle"
    :visible.sync="templateDetailDialogData.templateDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="template" :rules="rules" ref="templateForm" label-width="150px">
      <el-form-item label="Template Name" prop="name">
        <el-input v-model="template.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="template.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('templateForm')">Submit</el-button>
      <el-button @click="resetForm('templateForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createTemplate,
  updateTemplate,
  editTemplate
} from '@/api/marketing/email_module/template'
const defaultTemplate = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'templateDetail',
  props: {
    templateDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      template: Object.assign({}, defaultTemplate),
      rules: {
        name: [
          { required: true, message: 'Please input template name', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  created() {
    
  },
  methods: {
    getTemplateInfo() {
      //Judging whether it is a modification
      if (this.templateDetailDialogData.isEdit == true) {
        debugger;
        editTemplate(this.templateDetailDialogData.templateId).then((response) => {
          if (response.code == 200) {
            let templateData = response.data.list
            this.template = Object.assign({}, templateData)
          }
        })
        delete this.rules.name
        delete this.template.name
        delete this.template.description
      } else {
        this.template = Object.assign({}, defaultTemplate)
      }
    },
    onSubmit(templateForm) {
      this.$refs[templateForm].validate((valid) => {
        if (valid) {
          if (this.templateDetailDialogData.isEdit) {
            updateTemplate(this.template.id, this.template).then((response) => {
              if (response.code == 200) {
                this.$refs[templateForm].resetFields()
                this.$parent.getList()
                this.templateDetailDialogData.templateDetailDialogVisible = false
              }
            })
          } else {
            createTemplate(this.template).then((response) => {
              if (response.code == 200) {
                this.$refs[templateForm].resetFields()
                this.template = Object.assign({}, defaultTemplate)
                this.$parent.getList()
                this.templateDetailDialogData.templateDetailDialogVisible = false
              }
            })
          }
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
    resetForm(templateForm) {
      this.$refs[templateForm].resetFields()
      this.brand = Object.assign({}, defaultTemplate)
    },
  },
}
</script>

<style>
</style>
