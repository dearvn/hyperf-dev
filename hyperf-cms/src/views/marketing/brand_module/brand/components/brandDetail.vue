<template>
  <el-dialog
    :title="brandDetailDialogData.brandDetailTitle"
    :visible.sync="brandDetailDialogData.brandDetailDialogVisible"
    width="80%"
    :close-on-click-modal="false"
  >
    <el-form :model="brand" :rules="rules" ref="brandForm" label-width="150px">
      <el-row :gutter="20">
        <el-col :xs="12" :sm="24" :lg="12">
          <el-card class="box-card" style="margin-bottom:40px">
            <div slot="header" class="clearfix">
              <span>Brand Info</span>
            </div>
            <el-form-item label="Brand Name" prop="brand_name">
              <el-input v-model="brand.brand_name" placeholder="Brand name" auto-complete="off" size="medium"></el-input>
            </el-form-item>
            <el-form-item label="From email" prop="from_email">
              <el-input v-model="brand.from_email" placeholder="name@domain.com" auto-complete="off" size="medium" type="email"></el-input>
            </el-form-item>
            <el-form-item label="Reply to email" prop="reply_to_email">
              <el-input v-model="brand.reply_to_email" placeholder="name@domain.com" auto-complete="off" size="medium" type="email"></el-input>
            </el-form-item>
            <el-form-item label="Brand logo:">
              <single-upload v-model="brand.brand_logo" savePath="admin_face"></single-upload>
            </el-form-item>
            <el-collapse v-model="activeName" accordion>
              <el-collapse-item v-for="item in items" :key="item.id" :title="item.title" :name="item.id">
                <div>
                  {{ item.title }}
                </div>
              </el-collapse-item>
            </el-collapse>
          </el-card>
        </el-col>
        <el-col :xs="12" :sm="24" :lg="12">
          <el-card class="box-card" style="margin-bottom:30px">
            <div slot="header" class="clearfix">
              <span>Brand settings</span>
            </div>
            <div>
              <el-row :gutter="20" class="panel-group">
                <el-form-item label="Login email" prop="login_email">
                  <el-input v-model="brand.login_email" placeholder="name@domain.com" auto-complete="off" size="medium" type="email"></el-input>
                </el-form-item>
              </el-row>
              <el-row :gutter="20" class="panel-group" style="margin-bottom:-20px">
                
              </el-row>
            </div>
          </el-card>
        </el-col>
      </el-row>


      <div class="left-board">
        
      </div>
      <div class="center-board">
        
      </div>
      
      
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
import SingleUpload from '@/components/Upload/singleUpload'
import { validatEmail } from '@/utils/validate'

const defaultBrand = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'brandDetail',
  components: { SingleUpload },
  props: {
    brandDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    var validateEmail = (rule, value, callback) => {
      if (!validatEmail(value)) {
        callback(new Error('The email format is incorrect'))
      } else {
        callback()
      }
    }
    return {
      activeName: '',
      brand: Object.assign({}, defaultBrand),
      rules: {
        brand_name: [
          { required: true, message: 'Please input brand name', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
        from_email: [{ required: true, validator: validateEmail, trigger: 'blur' }],
        reply_to_email: [{ required: true, validator: validateEmail, trigger: 'blur' }],
      },
      items: [
        {id: 'smtp_settings', title: 'SMTP settings'},
        {id: 'custom_domain', title: 'Custom domain'},
        {id: 'google_captcha', title: 'Google reCAPTCHA v2'},
        {id: 'gdpr_features', title: 'GDPR features'},
        {id: 'privacy', title: 'Privacy'},
        {id: 'miscellaneous', title: 'Miscellaneous'},
      ]

      
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
