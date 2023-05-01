<script>
export default {
  //The name of the component must start with lemonMessage followed by Message.type
  name: 'LemonMessageLink',
  inheritAttrs: true,
  //If you need to use the method of the parent component, you can use injection.
  inject: ['IMUI'],
  components: {},
  render() {
    //The lemon-message-basic component handles information such as balloons, avatars, and events in common.
    return (
      <lemon-message-basic
        class="lemon-message-link"
        props={{ ...this.$attrs }}
        scopedSlots={{
          content: (props) => {
            //return html structure
            return (
              <a class="linkClass" href={props.content} target="_blank">
                {props.content}
              </a>
            )
          },
        }}
      />
    )
  },
}
</script>
<style lang="stylus">
.lemon-message.lemon-message-link {
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
.lemon-message-link ::v-deep .lemon-message__content {
  border-bottom: 1px solid #fff;
}
.lemon-message-link ::v-deep .lemon-message__content .linkClass {
  border-bottom: 1px solid;
  font-size: 14px;
}
</style>