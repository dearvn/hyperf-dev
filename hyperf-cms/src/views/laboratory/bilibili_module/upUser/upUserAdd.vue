<template>
  <el-card class="form-container" shadow="never" style="width: 900px">
    <el-form :model="upUser" :rules="rules" ref="upUserForm" label-width="150px">
      <el-form-item label="UP main link information:">
        <div class="dataItem" v-for="(item, index) in upUser.up_user_info" :key="index">
          <el-input v-model="item.up_user_url" placeholder="Please fill in the link information"></el-input>
          <el-select v-model="item.timed_status" placeholder="Please select whether to turn on regular statistics">
            <el-option
              v-for="item in timedStatusOptions"
              :key="item.dict_value"
              :label="item.dict_label"
              :value="item.dict_value"
            ></el-option>
          </el-select>
          <el-button type="text" @click="handleDeleteDataItem(index)">delete</el-button>
        </div>
        <el-button @click="handleAddDataItem()" size="small">Add to</el-button>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit('upUserForm')">Submit</el-button>
        <el-button @click="resetForm('upUserForm')">Reset</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>
<script>
import { upUserAdd } from '@/api/laboratory/bilibili_module/upUser'
const defaultUpUser = {
  up_user_info: [
    {
      up_user_url: '',
      timed_status: 1,
    },
  ],
}
export default {
  name: 'FileMaterialDetail',
  data() {
    return {
      upUser: Object.assign({}, defaultUpUser),
      timedStatusOptions: [],
    }
  },
  created() {
    this.getDicts('lab_up_user_time_status').then((response) => {
      if (response.code == 200) {
        this.timedStatusOptions = response.data.list
      }
    })
  },
  methods: {
    onSubmit(upUserForm) {
      this.$refs[upUserForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'Sure',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            upUserAdd(this.upUser).then((response) => {
              this.resetForm(upUserForm)
            })
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
    resetForm(upUserForm) {
      this.upUser.up_user_info = [
        {
          up_user_url: '',
          timed_status: 1,
        },
      ]
    },
    handleDeleteDataItem(index) {
      this.upUser.up_user_info.splice(index, 1)
    },
    handleAddDataItem() {
      if (this.upUser.up_user_info.length >= 50) {
        this.msgError('Add up to 50 links at one time')
      }
      this.upUser.up_user_info.push({
        up_user_url: '',
        timed_status: 1,
      })
    },
  },
}
</script>
<style>
.dataItem {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.dataItem >>> .el-input {
  width: 45%;
}
</style>
