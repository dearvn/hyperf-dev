<template>
  <section class="app-main">
    <transition name="fade" mode="out-in">
      <!--Temporarily disable route caching-->
      <!-- <keep-alive>
        <router-view :key="key"></router-view>
      </keep-alive>-->
      <router-view :key="key"></router-view>
    </transition>
  </section>
</template>
<script>
import { arrayLookup } from '@/utils/functions'
export default {
  name: 'AppMain',
  data() {
    return {
      isKeepAlive: process.env.NODE_ENV,
      cacheName: [''],
    }
  },
  computed: {
    cachedViews() {
      return this.$store.state.tagsViews.cachedViews
    },
    key() {
      if (this.$route.name == undefined && this.$route.path == '/home') {
        //When the page loads for the first time, clear all tabs on the tab page and return to the home page
        this.$store.dispatch('delAllViews')
      }
      let onlykey = ''
      let clicked = ''
      if (!this.$route.meta.clicked) {
        onlykey = this.$route.path + '0'
        clicked = '0'
      } else {
        //The last status was 0
        if (this.$route.meta.clicked == '0') {
          //This time with parameters
          if (
            Object.keys(this.$route.query).length != 0 ||
            this.$route.hash == '#new'
          ) {
            onlykey = this.$route.path + '1'
            clicked = '1'
          }
          //No parameters this time
          else {
            onlykey = this.$route.path + '0'
            clicked = '0'
          }
        }
        //The last status was not 0
        else {
          //This time with parameters
          //When creating a new event pass in hash = new
          if (
            Object.keys(this.$route.query).length != 0 ||
            this.$route.hash == '#new'
          ) {
            //This time the status is the last time +1
            //Get last status
            clicked = (parseInt(this.$route.meta.clicked) + 1).toString()
            onlykey = this.$route.path + clicked
          }
          //No parameters this time, this time the state remains the same
          else {
            clicked = parseInt(this.$route.meta.clicked).toString()
            onlykey = this.$route.path + clicked
          }
        }
      }
      this.$store.dispatch('addViews', this.$route)
      this.$route.meta.clicked = clicked
      return onlykey
    },
    cacheName() {
      let allViews = this.$store.getters.allViews
      let cacheNameArr = allViews.map((item) => {
        return item.routeName
      })
      return cacheNameArr
    },
  },
}
</script>
