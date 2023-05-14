<template>
  <el-dialog
    :title="brandDetailDialogData.brandDetailTitle"
    :visible.sync="brandDetailDialogData.brandDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="brand" :rules="rules" ref="brandForm" label-width="150px">
      <el-form-item label="Brand Name" prop="name">
        <el-input v-model="brand.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="brand.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('brandForm')">Submit</el-button>
      <el-button @click="resetForm('brandForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createBrand,
  updateBrand,
  editBrand
} from '@/api/marketing/brand_module/brand'
const defaultBrand = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'brandDetail',
  props: {
    brandDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      brand: Object.assign({}, defaultBrand),
      rules: {
        name: [
          { required: true, message: 'Please input brand name', trigger: 'blur' },
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
    getBrandInfo() {
      //Judging whether it is a modification
      if (this.brandDetailDialogData.isEdit == true) {
        debugger;
        editBrand(this.brandDetailDialogData.brandId).then((response) => {
          if (response.code == 200) {
            let brandData = response.data.list
            this.brand = Object.assign({}, brandData)
          }
        })
        delete this.rules.name
        delete this.brand.name
        delete this.brand.description
      } else {
        this.brand = Object.assign({}, defaultBrand)
      }
    },
    onSubmit(brandForm) {
      this.$refs[brandForm].validate((valid) => {
        if (valid) {
          if (this.brandDetailDialogData.isEdit) {
            updateBrand(this.brand.id, this.brand).then((response) => {
              if (response.code == 200) {
                this.$refs[brandForm].resetFields()
                this.$parent.getList()
                this.brandDetailDialogData.brandDetailDialogVisible = false
              }
            })
          } else {
            createBrand(this.brand).then((response) => {
              if (response.code == 200) {
                this.$refs[brandForm].resetFields()
                this.brand = Object.assign({}, defaultBrand)
                this.$parent.getList()
                this.brandDetailDialogData.brandDetailDialogVisible = false
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
    resetForm(brandForm) {
      this.$refs[brandForm].resetFields()
      this.brand = Object.assign({}, defaultBrand)
    },
  },
}
</script>

<style>
</style>
