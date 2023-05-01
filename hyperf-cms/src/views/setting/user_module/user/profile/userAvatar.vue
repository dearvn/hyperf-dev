<template>
  <div>
    <div class="user-info-head" @click="editCropper()">
      <img v-bind:src="options.img" title="Click to upload the avatar" class="img-circle img-lg" />
    </div>
    <el-dialog
      :title="title"
      :visible.sync="open"
      width="800px"
      append-to-body
      @opened="modalOpened"
    >
      <el-row>
        <el-col :xs="24" :md="12" :style="{height: '350px'}">
          <vue-cropper
            ref="cropper"
            :img="options.img"
            :info="true"
            :autoCrop="options.autoCrop"
            :autoCropWidth="options.autoCropWidth"
            :autoCropHeight="options.autoCropHeight"
            :fixedBox="options.fixedBox"
            @realTime="realTime"
            v-if="visible"
          />
        </el-col>
        <el-col :xs="24" :md="12" :style="{height: '350px'}">
          <div class="avatar-upload-preview">
            <img :src="previews.url" :style="previews.img" />
          </div>
        </el-col>
      </el-row>
      <br />
      <el-row>
        <el-col :lg="2" :md="2">
          <el-upload
            action="#"
            :http-request="requestUpload"
            :show-file-list="false"
            :before-upload="beforeUpload"
          >
            <el-button size="small">
              Choose
              <i class="el-icon-upload el-icon--right"></i>
            </el-button>
          </el-upload>
        </el-col>
        <el-col :lg="{span: 1, offset: 2}" :md="2">
          <el-button icon="el-icon-plus" size="small" @click="changeScale(1)"></el-button>
        </el-col>
        <el-col :lg="{span: 1, offset: 1}" :md="2">
          <el-button icon="el-icon-minus" size="small" @click="changeScale(-1)"></el-button>
        </el-col>
        <el-col :lg="{span: 1, offset: 1}" :md="2">
          <el-button icon="el-icon-refresh-left" size="small" @click="rotateLeft()"></el-button>
        </el-col>
        <el-col :lg="{span: 1, offset: 1}" :md="2">
          <el-button icon="el-icon-refresh-right" size="small" @click="rotateRight()"></el-button>
        </el-col>
        <el-col :lg="{span: 2, offset: 6}" :md="2">
          <el-button type="primary" size="small" @click="uploadImg()">Submit</el-button>
        </el-col>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
import store from '@/store'
import { VueCropper } from 'vue-cropper'
import { uploadAvatar } from '@/api/setting/user_module/user'

export default {
  components: { VueCropper },
  props: {
    user: {
      type: Object,
    },
  },
  data() {
    return {
      //Whether to display the popup layer
      open: false,
      //Whether to display cropper
      visible: false,
      //popup title
      title: 'Modify avatar',
      options: {
        img: store.getters.avatar, //The address of the cropped image
        autoCrop: true, //Whether to generate a screenshot box by default
        autoCropWidth: 200, //Generate screenshot frame width by default
        autoCropHeight: 200, //Generate screenshot frame height by default
        fixedBox: true, //Fixed screenshot frame size not allowed to change
      },
      previews: {},
    }
  },
  methods: {
    //edit avatar
    editCropper() {
      this.open = true
      let _this = this
      //Set avatar base64
      //Where this.avatar is the current avatar
      this.setAvatarBase64(store.getters.avatar, (base64) => {
        _this.options.img = base64
      })
    },
    //Set avatar base64
    setAvatarBase64(src, callback) {
      let _this = this
      let image = new Image()
      //handle cache
      image.src = src + '?v=' + Math.random()
      //Support cross domain images
      image.crossOrigin = '*'
      image.onload = function () {
        let base64 = _this.transBase64FromImage(image)
        callback && callback(base64)
      }
    },
    //Convert network images to base64 format
    transBase64FromImage(image) {
      let canvas = document.createElement('canvas')
      canvas.width = image.width
      canvas.height = image.height
      let ctx = canvas.getContext('2d')
      ctx.drawImage(image, 0, 0, image.width, image.height)
      //Optional other values ​​image/jpeg
      return canvas.toDataURL('image/png')
    },
    //Callback when opening the popup layer ends
    modalOpened() {
      this.visible = true
    },
    //Override default upload behavior
    requestUpload() {},
    //rotate to the left
    rotateLeft() {
      this.$refs.cropper.rotateLeft()
    },
    //rotate right
    rotateRight() {
      this.$refs.cropper.rotateRight()
    },
    //image zoom
    changeScale(num) {
      num = num || 1
      this.$refs.cropper.changeScale(num)
    },
    //upload preprocessing
    beforeUpload(file) {
      if (file.type.indexOf('image/') == -1) {
        this.msgError('The file format is wrong, please upload the picture type, such as: JPG, PNG suffix files.')
      } else {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = () => {
          this.options.img = reader.result
        }
      }
    },
    //upload image
    uploadImg() {
      this.$refs.cropper.getCropBlob((data) => {
        let formData = new FormData()
        formData.append('file', data)
        formData.append('save_path', 'admin_face')
        formData.append('id', this.user.id)
        uploadAvatar(formData).then((response) => {
          if (response.code == 200) {
            this.open = false
            this.options.img = response.data.url
            store.commit('SET_AVATAR', this.options.img)
            this.msgSuccess('Successfully modified')
            this.visible = false
          }
        })
      })
    },
    //live preview
    realTime(data) {
      this.previews = data
    },
  },
}
</script>
<style scoped lang="scss">
.user-info-head {
  position: relative;
  display: inline-block;
  height: 120px;
}

.user-info-head:hover:after {
  content: '+';
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  color: #eee;
  background: rgba(0, 0, 0, 0.5);
  font-size: 24px;
  font-style: normal;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  cursor: pointer;
  line-height: 110px;
  border-radius: 50%;
}
</style>