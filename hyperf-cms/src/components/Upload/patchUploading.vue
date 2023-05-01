<!--Multipart upload component-->
<template>
  <div class="upload">
  </div>
</template>
<script>
export default {
  name: 'PatchUploading',
  props: {
    accept: {
      type: Object,
      default: null,
    },
    //upload address
    url: {
      type: String,
      default: null,
    },
    //The maximum number of uploads is 100 by default
    fileNumLimit: {
      type: Number,
      default: 100,
    },
    //Size limit default 20M
    fileSingleSizeLimit: {
      type: Number,
      default: 20480000,
    },
    //Parameters passed to the backend when uploading, generally token, key, etc.
    formData: {
      type: Object,
      default: null
    },
    //Generate the key of the file in the form data, the following is just an example, the specific form is negotiated with the backend
    keyGenerator: {
      type: Function,
      default (file) {
        const currentTime = new Date().getTime();
        const key = `${currentTime}.${file.name}`;
        return key;
      },
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    //upload button id
    uploadButton: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      uploader: null
    };
  },
  mounted() {
    this.initWebUpload();
  },
  watch: {
    url(newValue, oldValue) {
      this.uploader.options.server = newValue
    },
    formData: {
      handler(newValue, oldValue) {
        this.uploader.options.formData = newValue
      },
      deep: true, //Attribute monitoring inside the object, also called deep monitoring
      immediate: false //Immediate indicates whether to execute the handler when the watch is bound for the first time. If the value is true, it means that when the watch is declared, the handler method will be executed immediately. If the value is false, it will be the same as the general use of the watch. Only execute the handler
    }
  },
  methods: {
    initWebUpload() {
      this.uploader = WebUploader.create({
        auto: true, //Whether to automatically upload the file after selecting the file
        swf: 'https://cdn.bootcss.com/webuploader/0.1.1/Uploader.swf', //Swf file path
        server: this.url, //file receiving server
        pick: {
          id: this.uploadButton, //button to select a file
          multiple: this.multiple, //Whether to upload multiple files, the default is false
          label: '',
        },
        accept: this.getAccept(this.accept), //Allows selection of the file format.
        threads: 3,
        fileNumLimit: this.fileNumLimit, //Limit the number of uploads
        //fileSingleSizeLimit: this.fileSingleSizeLimit, //Limit the size of a single uploaded image
        formData: this.formData, //Upload required parameters
        chunked: true, //Multipart upload
        chunkSize: 2048000, //Fragment size
        duplicate: true, //Duplicate upload
      });

      //When a file is added to the queue, add it to the page preview
      this.uploader.on('fileQueued', (file) => {
        this.$emit('fileChange', file);
      });

      this.uploader.on('uploadStart', (file) => {
        if (this.formData.keyword == null) {
          this.$message({
            message: 'Please select the game',
            type: 'error',
            duration: 2000
          })
        }
        //Here you can prepare the data of formData
        //this.uploader.options.formData.key = this.keyGenerator(file);
      });

      //The progress bar is displayed in real time during the file upload process.
      this.uploader.on('uploadProgress', (file, percentage) => {
        this.$emit('progress', file, percentage);
      });

      this.uploader.on('uploadSuccess', (file, response) => {
        this.$emit('success', file, response);
      });

      this.uploader.on('uploadError', (file, reason) => {
        console.error(reason);
        this.$emit('uploadError', file, reason);
      });

      this.uploader.on('error', (type) => {
        let errorMessage = '';
        if (type === 'F_EXCEED_SIZE') {
          errorMessage = `File size cannot exceed more${this.fileSingleSizeLimit / (1024 * 1000)}M`;
        } else if (type === 'Q_EXCEED_NUM_LIMIT') {
          errorMessage = 'File upload has reached the maximum upper limit number';
        } else {
          errorMessage = `Upload wrong!Please upload it after checking!error code${type}`;
        }

        this.$message({
          message: errorMessage,
          type: 'error',
          duration: 2000
        })
        this.$emit('error', errorMessage);
      });

      this.uploader.on('uploadComplete', (file, response) => {

        this.$emit('complete', file, response);
      });
    },

    upload(file) {
      this.uploader.upload(file);
    },
    stop(file) {
      this.uploader.stop(file);
    },
    //Cancel and interrupt file upload
    cancelFile(file) {
      this.uploader.cancelFile(file);
    },
    //Remove files from the queue
    removeFile(file, bool) {
      this.uploader.removeFile(file, bool);
    },

    getAccept(accept) {
      switch (accept) {
        case 'text':
          return {
            title: 'Texts',
            exteensions: 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt',
            mimeTypes: '.doc,docx,.xls,.xlsx,.ppt,.pptx,.pdf,.txt'
          };
          break;
        case 'video':
          return {
            title: 'Videos',
            exteensions: 'mp4',
            mimeTypes: '.mp4'
          };
          break;
        case 'image':
          return {
            title: 'Images',
            exteensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: '.gif,.jpg,.jpeg,.bmp,.png'
          };
          break;
        default:
          return accept
      }
    },

  },
};

</script>
<style lang="scss">
.webuploader-container {
  position: relative;
}

.webuploader-element-invisible {
  position: absolute !important;
  clip: rect(1px 1px 1px 1px);
  /* IE6, IE7 */
  clip: rect(1px, 1px, 1px, 1px);
}

.webuploader-pick {
  position: relative;
  display: inline-block;
  cursor: pointer;
  background: #00b7ee;
  padding: 10px 15px;
  color: #fff;
  text-align: center;
  border-radius: 3px;
  overflow: hidden;
}

.webuploader-pick-hover {
  background: #00a2d4;
}

.webuploader-pick-disable {
  opacity: 0.6;
  pointer-events: none;
}

</style>
