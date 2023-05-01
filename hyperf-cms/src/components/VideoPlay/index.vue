<template>
  <div class="video">
    <video-player
      class="video-player vjs-custom-skin"
      ref="videoPlayer"
      :playsinline="true"
      :options="playerOptions"
      id="video"
    ></video-player>
  </div>
</template>
<script>
import { getToken } from '@/utils/auth'
export default {
  name: 'VideoPlay',
  props: {
    value: String,
    src: {
      default: '',
    },
    aspectRatio: {
      default: '16:9',
    },
  },
  watch: {
    src(newVal, oldVal) {
      //监控单个变量
      this.playerOptions.sources[0].src = newVal
    },
  },
  data() {
    return {
      playerOptions: {
        //Play speed
        playbackRates: [0.5, 1.0, 1.5, 2.0],
        //If true, playback will start when the browser is ready.
        autoplay: false,
        //Any audio will be muted by default.
        muted: false,
        //Causes the video to restart as soon as it ends.
        loop: false,
        //Suggests whether the browser should start downloading video data after the <video> element is loaded. auto The browser chooses the best behavior and starts loading the video immediately (if the browser supports it)
        preload: false,
        //Puts the player in fluid mode and uses this value when calculating the dynamic size of the player. Value should represent a ratio -two numbers separated by a colon (eg "16:9" or "4:3")
        aspectRatio: this.aspectRatio,
        //When true, the Video.js player will have a fluid size. In other words, it will scale proportionally to fit its container.
        fluid: true,
        sources: [
          {
            //type
            type: 'video/mp4',
            //URL address
            src: this.src,
          },
        ],
        //your cover address
        poster: '',
        //Allows to override the default message that video.js displays when the media source cannot be played.
        notSupportedMessage: 'This video cannot be played for the time being, please try again later',
        controlBar: {
          timeDivider: true,
          durationDisplay: true,
          remainingTimeDisplay: false,
          //full screen button
          fullscreenToggle: true,
        },
      },
    }
  },
  methods: {
    pauseVideo() {
      this.$refs.videoPlayer.player.pause()
    },
  },
}
</script>
<style>
.video {
  display: inline-block;
  width: 600px;
  height: 338px;
  text-align: center;
  line-height: 100px;
  border: 1px solid transparent;
  border-radius: 4px;
  overflow: hidden;
  background: #fff;
  position: relative;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
  margin-right: 4px;
}

.demo:hover {
  display: block;
}
</style>
