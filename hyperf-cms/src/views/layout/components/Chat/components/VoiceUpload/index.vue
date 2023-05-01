<template>
  <div class="lum-dialog-mask animated fadeIn">
    <el-container class="lum-dialog-box">
      <el-header class="no-padding header no-select" height="50px">
        <p>Voice messages</p>
        <p class="tools">
          <i class="el-icon-close" @click="closeBox" />
        </p>
      </el-header>

      <el-main class="no-padding mian">
        <div class="music">
          <span class="line line1" :class="{ 'line-ani': animation }"></span>
          <span class="line line2" :class="{ 'line-ani': animation }"></span>
          <span class="line line3" :class="{ 'line-ani': animation }"></span>
          <span class="line line4" :class="{ 'line-ani': animation }"></span>
          <span class="line line5" :class="{ 'line-ani': animation }"></span>
        </div>
        <div style="margin-top: 35px; color: #676262; font-weight: 300">
          <template v-if="recorderStatus == 0">
            <p style="font-size: 13px; margin-top: 5px">
              <span>Voice messages, make the chat easier and convenient ...</span>
            </p>
          </template>
          <template
            v-else-if="
              recorderStatus == 1 || recorderStatus == 2 || recorderStatus == 3
            "
          >
            <p>{{ datetime }}</p>
            <p style="font-size: 13px; margin-top: 5px">
              <span v-if="recorderStatus == 1">Recording</span>
              <span v-else-if="recorderStatus == 2">Has been suspended</span>
              <span v-else-if="recorderStatus == 3">Recording duration</span>
            </p>
          </template>
          <template
            v-else-if="
              recorderStatus == 4 || recorderStatus == 5 || recorderStatus == 6
            "
          >
            <p>{{ formatPlayTime }}</p>
            <p style="font-size: 13px; margin-top: 5px">
              <span v-if="recorderStatus == 4">Now Playing</span>
              <span v-else-if="recorderStatus == 5">Played up</span>
              <span v-else-if="recorderStatus == 6">Play is over</span>
            </p>
          </template>
        </div>
      </el-main>

      <el-footer class="footer" height="50px">
        <!--0: Recording not started 1: Recording 2: Pause recording 3: End recording 4: Play recording 5: Stop playing-->
        <el-button
          v-show="recorderStatus == 0"
          type="primary"
          size="mini"
          round
          icon="el-icon-microphone"
          @click="startRecorder"
        >Start</el-button>
        <el-button
          v-show="recorderStatus == 1"
          type="primary"
          size="mini"
          round
          icon="el-icon-video-pause"
          @click="pauseRecorder"
        >Pause</el-button>
        <el-button
          v-show="recorderStatus == 2"
          type="primary"
          size="mini"
          round
          icon="el-icon-microphone"
          @click="resumeRecorder"
        >Resume</el-button>
        <el-button
          v-show="recorderStatus == 2"
          type="primary"
          size="mini"
          round
          icon="el-icon-microphone"
          @click="stopRecorder"
        >Stop</el-button>
        <el-button
          v-show="recorderStatus == 3 || recorderStatus == 6"
          type="primary"
          size="mini"
          round
          icon="el-icon-video-play"
          @click="playRecorder"
        >Play</el-button>

        <el-button
          v-show="
            recorderStatus == 3 || recorderStatus == 5 || recorderStatus == 6
          "
          type="primary"
          size="mini"
          round
          icon="el-icon-video-play"
          @click="startRecorder"
        >Recording</el-button>

        <el-button
          v-show="recorderStatus == 4"
          type="primary"
          size="mini"
          round
          icon="el-icon-video-pause"
          @click="pausePlayRecorder"
        >Pause</el-button>
        <el-button
          v-show="recorderStatus == 5"
          type="primary"
          size="mini"
          round
          icon="el-icon-video-play"
          @click="resumePlayRecorder"
        >Resume</el-button>

        <el-button
          v-show="
            recorderStatus == 3 || recorderStatus == 5 || recorderStatus == 6
          "
          type="primary"
          size="mini"
          round
          @click="submit"
        >Submit</el-button>
      </el-footer>
    </el-container>
  </div>
</template>
<script>
// import Recorder from 'js-audio-recorder'
export default {
  name: 'VoiceUpload',
  data() {
    return {
      //Recording instance
      recorder: null,

      //recording time
      duration: 0,

      //play time
      playTime: 0,

      animation: false,

      //current status
      recorderStatus: 0, //0: Recording not started 1: Recording 2: Pausing recording 3: Ending recording 4: Playing recording 5: Stop playing 6: Playing over

      playTimeout: null,
    }
  },
  computed: {
    datetime() {
      let hour = parseInt((this.duration / 60 / 60) % 24) //Hour
      let minute = parseInt((this.duration / 60) % 60) //minute
      let seconds = parseInt(this.duration % 60) //Second

      if (hour < 10) hour = `0${hour}`
      if (minute < 10) minute = `0${minute}`
      if (seconds < 10) seconds = `0${seconds}`

      return `${hour}:${minute}:${seconds}`
    },
    formatPlayTime() {
      let hour = parseInt((this.playTime / 60 / 60) % 24) //Hour
      let minute = parseInt((this.playTime / 60) % 60) //minute
      let seconds = parseInt(this.playTime % 60) //Second

      if (hour < 10) hour = `0${hour}`
      if (minute < 10) minute = `0${minute}`
      if (seconds < 10) seconds = `0${seconds}`

      return `${hour}:${minute}:${seconds}`
    },
  },
  destroyed() {
    if (this.recorder) {
      this.destroyRecorder()
    }
  },
  methods: {
    closeBox() {
      if (this.recorder == null) {
        this.$emit('close', false)
        return
      }

      if (this.recorderStatus == 1) {
        this.stopRecorder()
      } else if (this.recorderStatus == 4) {
        this.pausePlayRecorder()
      }

      //Close the window after destroying the recording
      this.destroyRecorder(() => {
        this.$emit('close', false)
      })
    },

    //start recording
    startRecorder() {
      let _this = this
      // http://recorder.api.zhuyuntao.cn/Recorder/event.html
      // https://blog.csdn.net/qq_41619796/article/details/107865602
      this.recorder = new Recorder()
      this.recorder.onprocess = (duration) => {
        duration = parseInt(duration)
        _this.duration = duration
      }

      this.recorder.start().then(
        () => {
          this.recorderStatus = 1
          this.animation = true
        },
        (error) => {
          console.log(`${error.name} : ${error.message}`)
        }
      )
    },
    //pause recording
    pauseRecorder() {
      this.recorder.pause()
      this.recorderStatus = 2
      this.animation = false
    },
    //continue recording
    resumeRecorder() {
      this.recorderStatus = 1
      this.recorder.resume()
      this.animation = true
    },
    //end recording
    stopRecorder() {
      this.recorderStatus = 3
      this.recorder.stop()
      this.animation = false
    },
    //recording playback
    playRecorder() {
      this.recorderStatus = 4
      this.recorder.play()
      this.playTimeouts()
      this.animation = true
    },
    //Pause recording playback
    pausePlayRecorder() {
      this.recorderStatus = 5
      this.recorder.pausePlay()
      clearInterval(this.playTimeout)
      this.animation = false
    },
    //resume playback
    resumePlayRecorder() {
      this.recorderStatus = 4
      this.recorder.resumePlay()
      this.playTimeouts()
      this.animation = true
    },
    //destroy recording
    destroyRecorder(callBack) {
      this.recorder.destroy().then(() => {
        this.recorder = null
        console.log('Destroy...')
        if (callBack) {
          callBack()
        }
      })
    },
    //Get the recording file size (unit: byte)
    recorderSize() {
      return this.recorder.fileSize
    },

    playTimeouts() {
      this.playTimeout = setInterval(() => {
        let time = parseInt(this.recorder.getPlayTime())
        this.playTime = time
        if (time == this.duration) {
          clearInterval(this.playTimeout)
          this.animation = false
          this.recorderStatus = 6
        }
      }, 100)
    },

    submit() {
      alert('In functional research and development, stay tuned ...')
    },
  },
}
</script>
<style lang="scss" scoped>
.lum-dialog-box {
  width: 500px;
  max-width: 500px;
  height: 450px;

  .mian {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }

  .footer {
    height: 50px;
    text-align: center;
    line-height: 50px;
    border-top: 1px solid #f7f3f3;
  }
}

.music {
  position: relative;
  width: 180px;
  height: 160px;
  border: 8px solid #bebebe;
  border-bottom: 0px;
  border-top-left-radius: 110px;
  border-top-right-radius: 110px;
}

.music:before,
.music:after {
  content: '';
  position: absolute;
  bottom: -20px;
  width: 40px;
  height: 82px;
  background-color: #bebebe;
  border-radius: 15px;
}

.music:before {
  right: -25px;
}

.music:after {
  left: -25px;
}

.line {
  position: absolute;
  width: 6px;
  min-height: 30px;
  transition: 0.5s;

  vertical-align: middle;
  bottom: 0 !important;
  box-shadow: inset 0px 0px 16px -2px rgba(0, 0, 0, 0.15);
}

.line-ani {
  animation: equalize 4s 0s infinite;
  animation-timing-function: linear;
}

.line1 {
  left: 30%;
  bottom: 0px;
  animation-delay: -1.9s;
  background-color: #ff5e50;
}

.line2 {
  left: 40%;
  height: 60px;
  bottom: -15px;
  animation-delay: -2.9s;
  background-color: #a64de6;
}

.line3 {
  left: 50%;
  height: 30px;
  bottom: -1.5px;
  animation-delay: -3.9s;
  background-color: #5968dc;
}

.line4 {
  left: 60%;
  height: 65px;
  bottom: -16px;
  animation-delay: -4.9s;
  background-color: #27c8f8;
}

.line5 {
  left: 70%;
  height: 60px;
  bottom: -12px;
  animation-delay: -5.9s;
  background-color: #cc60b5;
}

@keyframes equalize {
  0% {
    height: 48px;
  }

  4% {
    height: 42px;
  }

  8% {
    height: 40px;
  }

  12% {
    height: 30px;
  }

  16% {
    height: 20px;
  }

  20% {
    height: 30px;
  }

  24% {
    height: 40px;
  }

  28% {
    height: 10px;
  }

  32% {
    height: 40px;
  }

  36% {
    height: 48px;
  }

  40% {
    height: 20px;
  }

  44% {
    height: 40px;
  }

  48% {
    height: 48px;
  }

  52% {
    height: 30px;
  }

  56% {
    height: 10px;
  }

  60% {
    height: 30px;
  }

  64% {
    height: 48px;
  }

  68% {
    height: 30px;
  }

  72% {
    height: 48px;
  }

  76% {
    height: 20px;
  }

  80% {
    height: 48px;
  }

  84% {
    height: 38px;
  }

  88% {
    height: 48px;
  }

  92% {
    height: 20px;
  }

  96% {
    height: 48px;
  }

  100% {
    height: 48px;
  }
}
</style>
