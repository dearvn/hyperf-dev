<template>
  <el-menu
    :default-active="currentModule"
    class="el-menu-demo"
    mode="horizontal"
    background-color="#fff"
    text-color="#606266"
    active-text-color="#303133"
    style="margin-left: 5px;line-height: 55px;font-size: 15px"
  >
    <el-menu-item index="home" @click="clickHome">
      <svg-icon icon-class="home" />Home
    </el-menu-item>
    <el-menu-item
      v-for="(item, index) in menuHeader"
      :key="index"
      :index="item.name"
      @click="clickMenu(item.name)"
    >
      <svg-icon :icon-class="item.icon" />
      {{ item.title }}
    </el-menu-item>
  </el-menu>
</template>
<script>
export default {
  name: 'TopNav',
  created() {
    this.getMenu()
    this.initCurrentModule()
  },
  data() {
    return {
      menuHeader: [],
      menuList: [],
      currentModule: '',
    }
  },
  watch: {
    $route(to, from) {
      this.currentModule = this.$store.state.permission.currentModule
      if (this.currentModule != 'home') {
        this.changeMenuLeft(this.$store.state.permission.currentModule)
        this.getMenu()
      } else {
        this.$store.state.permission.menuLeft = []
      }
    },
    currentModule(newValue, oldValue) {
      //TODO monitor the change of store value
      this.changeMenuLeft(newValue)
    },
  },
  methods: {
    getMenu() {
      this.menuHeader = this.$store.state.permission.menuHeader
      this.menuList = this.$store.state.permission.menuList
    },
    initCurrentModule() {
      this.currentModule = this.$store.state.permission.currentModule
    },
    clickHome() {
      this.$store.commit('SET_CURRENT_MODULE', 'home')
      this.$store.state.permission.menuLeft = []
      this.$router.push('/')
    },
    clickMenu(item) {
      this.changeMenuLeft(item)
      //Determine whether the click module has a submenu, if not, jump to the url under the module, if there is, jump to the navigation page
      for (var i = 0; i < this.menuList.length; i++) {
        if (item == this.menuList[i].name) {
          if (this.menuList[i].child == undefined) {
            this.$router.push(this.menuList[i].url)
          } else {
            this.$router.push('/navigation')
          }
        }
      }
    },
    changeMenuLeft(item) {
      this.$store.commit('SET_CURRENT_MODULE', item)
      for (var i = 0; i < this.menuList.length; i++) {
        if (item == this.menuList[i].name) {
          if (this.menuList[i].child == undefined) {
            this.$store.state.permission.menuLeft = []
          } else {
            this.$store.state.permission.menuLeft = this.menuList[i].child
          }
        }
      }
    },
  },
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.el-menu-demo {
  display: inline-block;
  font-size: 16px;
  line-height: 60px;
  margin-left: 10px;

  .no-redirect {
    color: #97a8be;
    cursor: text;
  }
}
</style>
