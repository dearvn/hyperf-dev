<template>
  <el-card class="form-container" shadow="never" style="width: 1000px">
    <el-form :model="video" :rules="rules" ref="videoForm" label-width="120px">
      <el-form-item label="Video link information：">
        <div class="dataItem" v-for="(item, index) in video.video_info" :key="index">
          <el-input v-model="item.video_url" placeholder="Please fill in the video link：" style="width:70%"></el-input>
          <el-select v-model="item.timed_status" placeholder="Please select whether to turn on regular statistics" style="width:30%">
            <el-option
              v-for="item in timedStatusOptions"
              :key="item.dict_value"
              :label="item.dict_label"
              :value="item.dict_value"
            ></el-option>
          </el-select>
          <el-button type="text" @click="handleDeleteDataItem(index)">Delete</el-button>
        </div>
        <el-button @click="handleAddDataItem()" size="small">Add to</el-button>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit('videoForm')">Submit</el-button>
        <el-button @click="resetForm('videoForm')">Reset</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>
<script>
import { videoAdd } from '@/api/laboratory/bilibili_module/video'
const defaultvideo = {
  video_info: [
    {
      video_url: '',
      timed_status: '',
    },
  ],
}
export default {
  name: 'FileMaterialDetail',
  data() {
    return {
      video: Object.assign({}, defaultvideo),
      timedStatusOptions: [],
    }
  },
  created() {
    this.getDicts('lab_video_time_status').then((response) => {
      if (response.code == 200) {
        this.timedStatusOptions = response.data.list
      }
    })
  },
  methods: {
    onSubmit(videoForm) {
      this.$refs[videoForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            videoAdd(this.video).then((response) => {
              this.resetForm(videoForm)
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
    resetForm(videoForm) {
      this.video.video_info = [
        {
          video_url: '',
          timed_status: 1,
        },
      ]
    },
    handleDeleteDataItem(index) {
      this.video.video_info.splice(index, 1)
    },
    handleAddDataItem() {
      if (this.video.video_info.length >= 50) {
        this.msgError('Add up to 50 video links at one time')
      }
      this.video.video_info.push({
        video_url: '',
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
