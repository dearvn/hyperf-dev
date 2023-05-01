<script>
import TextFormat from './components/TextFormat'
export default {
  //The name of the component must start with lemonMessage followed by Message.type
  name: 'lemonMessageText',
  inheritAttrs: true,
  //If you need to use the method of the parent component, you can use injection.
  inject: ['IMUI'],
  components: { TextFormat },
  render() {
    //The lemon-message-basic component handles information such as balloons, avatars, and events in common.
    return (
      <lemon-message-basic
        class="lemon-message-text"
        props={{ ...this.$attrs }}
        scopedSlots={{
          content: (props) => {
            const content = this.IMUI.emojiNameToImage(props.content)
            return <text-format content={content}></text-format>
          },
        }}
      />
    )
  },
}
</script>
<style lang="stylus" >