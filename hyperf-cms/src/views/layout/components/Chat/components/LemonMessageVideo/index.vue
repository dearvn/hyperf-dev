<script>
import VideoPreview from './components/VideoPreview'
export default {
  //The name of the component must start with lemonMessage followed by Message.type
  name: 'LemonMessageVideo',
  inheritAttrs: true,
  //If you need to use the method of the parent component, you can use injection.
  inject: ['IMUI'],
  components: { VideoPreview },
  render() {
    //The lemon-message-basic component handles information such as balloons, avatars, and events in common.
    return (
      <lemon-message-basic
        class="lemon-message-video"
        props={{ ...this.$attrs }}
        scopedSlots={{
          content: (props) => {
            //return html structure
            return <video-preview content={props.content}></video-preview>
          },
        }}
      />
    )
  },
}
</script>
<style lang="stylus">
.lemon-message.lemon-message-video {
  user-select: none;
  background-color: #fff;

  .lemon-message__content {
    font-size: 12px;
    cursor: pointer;

    &::before {
      display: none;
    }
  }
}
</style>
<style lang="scss" scoped>
.lemon-message-video ::v-deep .lemon-message__content {
  padding: 0px;
  line-height: 0px;
}
</style>