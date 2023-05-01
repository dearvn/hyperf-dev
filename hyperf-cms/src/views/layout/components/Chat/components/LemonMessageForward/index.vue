<script>
import Message from './components/Message'
export default {
  //The name of the component must start with lemonMessage followed by Message.type
  name: 'LemonMessageForward',
  inheritAttrs: false,
  //If you need to use the method of the parent component, you can use injection.
  inject: ['IMUI'],
  components: {
    Message,
  },
  render() {
    //The lemon-message-basic component handles information such as balloons, avatars, and events in common.
    return (
      <lemon-message-basic
        class="lemon-message-forward"
        props={{ ...this.$attrs }}
        scopedSlots={{
          content: (props) => {
            //return html structure
            return <message content={props.content}></message>
          },
        }}
      />
    )
  },
}
</script>

<style lang="stylus">
.lemon-message.lemon-message-forward {
  user-select: none;

  .lemon-message__content {
    border: 2px solid #000;
    font-size: 12px;
    cursor: pointer;

    &::before {
      display: none;
    }
  }
}
</style>