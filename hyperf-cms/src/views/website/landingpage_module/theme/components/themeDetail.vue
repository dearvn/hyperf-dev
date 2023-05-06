<template>
  <el-dialog
    :title="themeDetailDialogData.themeDetailTitle"
    :visible.sync="themeDetailDialogData.themeDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="theme" :rules="rules" ref="themeForm" label-width="150px">
      <el-form-item label="Theme Name" prop="name">
        <el-input v-model="theme.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="theme.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('themeForm')">Submit</el-button>
      <el-button @click="resetForm('themeForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createTheme,
  updateTheme,
  editTheme
} from '@/api/website/landingpage_module/theme'
const defaultTheme = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'themeDetail',
  props: {
    themeDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      theme: Object.assign({}, defaultTheme),
      rules: {
        name: [
          { required: true, message: 'Please input theme name', trigger: 'blur' },
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
    getThemeInfo() {
      //Judging whether it is a modification
      if (this.themeDetailDialogData.isEdit == true) {
        debugger;
        editTheme(this.themeDetailDialogData.themeId).then((response) => {
          if (response.code == 200) {
            let themeData = response.data.list
            this.theme = Object.assign({}, themeData)
          }
        })
        delete this.rules.name
        delete this.theme.name
        delete this.theme.description
      } else {
        this.theme = Object.assign({}, defaultTheme)
      }
    },
    onSubmit(themeForm) {
      this.$refs[themeForm].validate((valid) => {
        if (valid) {
          if (this.themeDetailDialogData.isEdit) {
            updateTheme(this.theme.id, this.theme).then((response) => {
              if (response.code == 200) {
                this.$refs[themeForm].resetFields()
                this.$parent.getList()
                this.themeDetailDialogData.themeDetailDialogVisible = false
              }
            })
          } else {
            createTheme(this.theme).then((response) => {
              if (response.code == 200) {
                this.$refs[themeForm].resetFields()
                this.theme = Object.assign({}, defaultTheme)
                this.$parent.getList()
                this.themeDetailDialogData.themeDetailDialogVisible = false
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
    resetForm(themeForm) {
      this.$refs[themeForm].resetFields()
      this.brand = Object.assign({}, defaultTheme)
    },
  },
}
</script>

<style>
</style>
