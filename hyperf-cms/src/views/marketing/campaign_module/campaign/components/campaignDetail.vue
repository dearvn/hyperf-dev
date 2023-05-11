<template>
  <el-dialog
    :title="campaignDetailDialogData.campaignDetailTitle"
    :visible.sync="campaignDetailDialogData.campaignDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="campaign" :rules="rules" ref="campaignForm" label-width="150px">
      <el-form-item label="Campaign Name" prop="name">
        <el-input v-model="campaign.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="campaign.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('campaignForm')">Submit</el-button>
      <el-button @click="resetForm('campaignForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createCampaign,
  updateCampaign,
  editCampaign
} from '@/api/marketing/campaign_module/campaign'
const defaultCampaign = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'campaignDetail',
  props: {
    campaignDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      campaign: Object.assign({}, defaultCampaign),
      rules: {
        name: [
          { required: true, message: 'Please input campaign name', trigger: 'blur' },
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
    getCampaignInfo() {
      //Judging whether it is a modification
      if (this.campaignDetailDialogData.isEdit == true) {
        debugger;
        editCampaign(this.campaignDetailDialogData.campaignId).then((response) => {
          if (response.code == 200) {
            let campaignData = response.data.list
            this.campaign = Object.assign({}, campaignData)
          }
        })
        delete this.rules.name
        delete this.campaign.name
        delete this.campaign.description
      } else {
        this.campaign = Object.assign({}, defaultCampaign)
      }
    },
    onSubmit(campaignForm) {
      this.$refs[campaignForm].validate((valid) => {
        if (valid) {
          if (this.campaignDetailDialogData.isEdit) {
            updateCampaign(this.campaign.id, this.campaign).then((response) => {
              if (response.code == 200) {
                this.$refs[campaignForm].resetFields()
                this.$parent.getList()
                this.campaignDetailDialogData.campaignDetailDialogVisible = false
              }
            })
          } else {
            createCampaign(this.campaign).then((response) => {
              if (response.code == 200) {
                this.$refs[campaignForm].resetFields()
                this.campaign = Object.assign({}, defaultCampaign)
                this.$parent.getList()
                this.campaignDetailDialogData.campaignDetailDialogVisible = false
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
    resetForm(campaignForm) {
      this.$refs[campaignForm].resetFields()
      this.brand = Object.assign({}, defaultCampaign)
    },
  },
}
</script>

<style>
</style>
