<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col :span="6" :xs="24">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Personal information</span>
          </div>
          <div>
            <div class="text-center">
              <userAvatar :user="user" />
            </div>
            <ul class="list-group list-group-striped">
              <li class="list-group-item">
                <svg-icon icon-class="user" />User name
                <div class="pull-right">{{ user.desc }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="phone" />Phone
                <div class="pull-right">{{ user.mobile }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="email" />Email
                <div class="pull-right">{{ user.email }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="tree" />Last login IP
                <div class="pull-right">{{ user.last_ip }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="date" />Last Login Time
                <div class="pull-right">{{ user.last_login }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="peoples" />Belong to
                <div class="pull-right">{{ roleGroup }}</div>
              </li>
              <li class="list-group-item">
                <svg-icon icon-class="date" />Created at
                <div class="pull-right">{{ user.created_at }}</div>
              </li>
            </ul>
          </div>
        </el-card>
      </el-col>
      <el-col :span="18" :xs="24">
        <el-card>
          <div slot="header" class="clearfix">
            <span>Basic information</span>
          </div>
          <el-tabs v-model="activeTab">
            <el-tab-pane label="Basic information" name="userinfo">
              <userInfo :user="user" />
            </el-tab-pane>
            <el-tab-pane label="Change Password" name="resetPwd">
              <resetPwd :user="user" />
            </el-tab-pane>
          </el-tabs>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import userAvatar from './userAvatar'
import userInfo from './userInfo'
import resetPwd from './resetPwd'
import { getUserInfo } from '@/api/setting/user_module/user'

export default {
  name: 'Profile',
  components: { userAvatar, userInfo, resetPwd },
  data() {
    return {
      user: {},
      roleGroup: {},
      postGroup: {},
      activeTab: 'userinfo',
    }
  },
  created() {
    this.getUser()
  },
  methods: {
    getUser() {
      getUserInfo().then((response) => {
        if (response.code == 200) {
          this.user = response.data.list
          this.roleGroup = response.data.role
        }
      })
    },
  },
}
</script>
