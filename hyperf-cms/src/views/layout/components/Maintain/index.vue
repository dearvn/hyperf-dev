<template>
  <div>
    <el-tooltip
      class="item"
      effect="dark"
      content="In the background maintenance (the permissions of all users except the super administrator are prohibited)"
      placement="top-start"
    >
      <svg-icon icon-class="maintain" style="width:4.0em" />
    </el-tooltip>
  </div>
</template>
<script>
export default {
  name: 'Maintain',
  data() {
    return {}
  },
  methods: {},
}
</script>
<style scoped>
.item {
  cursor: pointer;
}
</style>
