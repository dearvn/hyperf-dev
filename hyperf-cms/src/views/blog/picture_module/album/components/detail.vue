<template>
  <el-dialog
    :title="albumDetailDialogData.albumDetailTitle"
    :visible.sync="albumDetailDialogData.albumDetailDialogVisible"
    width="800px"
    :close-on-click-modal="false"
  >
    <el-form :model="album" :rules="rules" ref="albumForm" label-width="150px">
      <el-form-item label="Album name：" prop="album_name">
        <el-input
          v-model="album.album_name"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the title"
          style="width:500px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Album description：" prop="album_desc">
        <el-input
          v-model="album.album_desc"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the album description"
          style="width:500px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Album author：" prop="album_author">
        <el-input
          v-model="album.album_author"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the album author"
          style="width:500px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Album type:" prop="album_type">
        <el-select
          v-model="album.album_type"
          clearable
          class="input-width"
          size="medium"
          placeholder="Album type"
        >
          <el-option
            v-for="dict in albumDetailDialogData.typeOptions"
            :key="dict.dict_value"
            :label="dict.dict_label"
            :value="dict.dict_value"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Album problem：" prop="album_question" v-if="album.album_type == 2">
        <el-input
          v-model="album.album_question"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the album problem"
          style="width:500px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Album password" prop="album_answer" v-if="album.album_type == 2">
        <el-input
          v-model="album.album_answer"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the album password"
          style="width:500px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Album sorting:" prop="album_sort">
        <el-input-number
          v-model="album.album_sort"
          auto-complete="off"
          size="medium"
          placeholder="Please enter album sorting"
          :max="99"
          :min="1"
        ></el-input-number>
      </el-form-item>
      <el-form-item label="Album cover:" prop="album_cover">
        <single-upload v-model="album.album_cover" :savePath="album/album_cover"></single-upload>
      </el-form-item>
      <el-form-item label="Album status:" prop="album_status">
        <el-radio
          v-model="album.album_status"
          v-for="dict in albumDetailDialogData.statusOptions"
          :key="dict.dict_value"
          :label="dict.dict_value"
        >{{ dict.dict_label}}</el-radio>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('albumForm')">Submit</el-button>
      <el-button v-if="!albumDetailDialogData.isEdit" @click="resetForm('albumForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createAlbum,
  updateAlbum,
  editAlbum,
} from '@/api/blog/picture_module/album'
import SingleUpload from '@/components/Upload/singleUpload'
const defaultAlbum = {
  album_name: '',
  album_desc: '',
  album_cover: '',
  album_type: 1,
  album_author: '',
  album_status: 1,
  album_question: '',
  album_answer: '',
  album_sort: '',
}
export default {
  name: 'AlbumDetail',
  props: {
    albumDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  components: {
    SingleUpload,
  },
  data() {
    return {
      album: Object.assign({}, defaultAlbum),
      rules: {
        album_name: [
          { required: true, message: 'Please fill in the album name', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
        album_status: [
          { required: true, message: 'Please select status', trigger: 'blur' },
        ],
        album_type: [
          { required: true, message: 'Please choose the type', trigger: 'blur' },
        ],
      },
    }
  },
  created() {},
  methods: {
    getAlbumInfo() {
      this.album = Object.assign({}, defaultAlbum)
      //Judging whether it is a modification
      if (this.albumDetailDialogData.isEdit == true) {
        editAlbum(this.albumDetailDialogData.id).then((response) => {
          if (response.code == 200) {
            let albumData = response.data.list
            this.album = Object.assign({}, albumData)
          }
        })
      }
    },
    onSubmit(albumForm) {
      this.$refs[albumForm].validate((valid) => {
        if (valid) {
          this.$confirm('Do you want to sumit data?', 'Alert', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.albumDetailDialogData.isEdit) {
              updateAlbum(this.album.id, this.album).then((response) => {
                if (response.code == 200) {
                  this.$refs[albumForm].resetFields()
                  this.$parent.getList()
                  this.albumDetailDialogData.albumDetailDialogVisible = false
                }
              })
            } else {
              createAlbum(this.album).then((response) => {
                if (response.code == 200) {
                  this.$refs[albumForm].resetFields()
                  this.album = Object.assign({}, defaultAlbum)
                  this.$parent.getList()
                  this.albumDetailDialogData.albumDetailDialogVisible = false
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
    resetForm(albumForm) {
      this.$refs[albumForm].resetFields()
      this.brand = Object.assign({}, defaultAlbum)
    },
  },
}
</script>

<style>
</style>
