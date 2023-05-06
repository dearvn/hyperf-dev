<template>
  <el-dialog
    :title="domainDetailDialogData.domainDetailTitle"
    :visible.sync="domainDetailDialogData.domainDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="domain" :rules="rules" ref="domainForm" label-width="150px">
      <el-form-item label="Domain Name" prop="name">
        <el-input v-model="domain.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="domain.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('domainForm')">Submit</el-button>
      <el-button @click="resetForm('domainForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createDomain,
  updateDomain,
  editDomain
} from '@/api/website/domain_module/domain'
const defaultDomain = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'domainDetail',
  props: {
    domainDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      domain: Object.assign({}, defaultDomain),
      rules: {
        name: [
          { required: true, message: 'Please input domain name', trigger: 'blur' },
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
    getDomainInfo() {
      //Judging whether it is a modification
      if (this.domainDetailDialogData.isEdit == true) {
        debugger;
        editDomain(this.domainDetailDialogData.domainId).then((response) => {
          if (response.code == 200) {
            let domainData = response.data.list
            this.domain = Object.assign({}, domainData)
          }
        })
        delete this.rules.name
        delete this.domain.name
        delete this.domain.description
      } else {
        this.domain = Object.assign({}, defaultDomain)
      }
    },
    onSubmit(domainForm) {
      this.$refs[domainForm].validate((valid) => {
        if (valid) {
          if (this.domainDetailDialogData.isEdit) {
            updateDomain(this.domain.id, this.domain).then((response) => {
              if (response.code == 200) {
                this.$refs[domainForm].resetFields()
                this.$parent.getList()
                this.domainDetailDialogData.domainDetailDialogVisible = false
              }
            })
          } else {
            createDomain(this.domain).then((response) => {
              if (response.code == 200) {
                this.$refs[domainForm].resetFields()
                this.domain = Object.assign({}, defaultDomain)
                this.$parent.getList()
                this.domainDetailDialogData.domainDetailDialogVisible = false
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
    resetForm(domainForm) {
      this.$refs[domainForm].resetFields()
      this.brand = Object.assign({}, defaultDomain)
    },
  },
}
</script>

<style>
</style>
