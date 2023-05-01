<template>
  <el-dialog
    :title="photoDetailDialogData.photoDetailTitle"
    :visible.sync="photoDetailDialogData.photoDetailDialogVisible"
    width="700px"
    :close-on-click-modal="false"
  >
    <el-form :model="photo" :rules="rules" ref="photoForm" label-width="180px">
      <el-form-item label="Album type：" prop="photo_album">
        <el-select
          v-model="photo.photo_album"
          clearable
          class="input-width"
          size="medium"
          placeholder="Album type"
        >
          <el-option
            v-for="album in photoDetailDialogData.photoAlbumOption"
            :key="album.id"
            :label="album.album_name"
            :value="album.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Upload picture：" prop="photo_type">
        <multi-upload v-model="photo.photo_url" :savePath="photo"></multi-upload>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('photoForm')">Submit</el-button>
      <el-button v-if="!photoDetailDialogData.isEdit" @click="resetForm('photoForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import { createPhoto } from '@/api/blog/picture_module/photo'
import MultiUpload from '@/components/Upload/multiUpload'
const defaultPhoto = {
  photo_album: '',
  photo_url: [],
}
export default {
  name: 'PhotoDetail',
  props: {
    photoDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  components: {
    MultiUpload,
  },
  data() {
    return {
      photo: Object.assign({}, defaultPhoto),
      rules: {
        photo_album: [
          { required: true, message: 'Please select album type', trigger: 'blur' },
        ],
      },
    }
  },
  created() {},
  methods: {
    getPhotoInfo() {
      const photo_album = this.$route.params && this.$route.params.photo_album
      this.photo = Object.assign({}, defaultPhoto)
      this.photo.photo_album = photo_album
    },
    onSubmit(photoForm) {
      this.$refs[photoForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'Sure',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            createPhoto(this.photo).then((response) => {
              if (response.code == 200) {
                this.$refs[photoForm].resetFields()
                this.photo = Object.assign({}, defaultPhoto)
                this.$parent.getList()
                this.photoDetailDialogData.photoDetailDialogVisible = false
              }
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
    resetForm(photoForm) {
      this.$refs[photoForm].resetFields()
      this.brand = Object.assign({}, defaultPhoto)
    },
  },
}
</script>

<style>
</style>
