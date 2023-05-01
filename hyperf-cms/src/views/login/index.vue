<template>
  <div>
    <div id="particles">
      <canvas
        class="particles-js-canvas-el"
        width="1747"
        height="1000"
        style="width: 100%; height: 100%;"
      ></canvas>
    </div>
    <el-card class="login-form-layout">
      <el-form
        autocomplete="on"
        :model="loginForm"
        :rules="loginRules"
        ref="loginForm"
        label-position="left"
      >
        <div style="text-align: center"></div>
        <h2 class="login-title color-main" style="font-size:25px">
          Hyperf-dev
        </h2>
        <el-form-item prop="username">
          <el-input
            name="username"
            type="text"
            v-model="loginForm.username"
            autocomplete="on"
            placeholder="please enter user name"
          >
            <span slot="prefix">
              <svg-icon icon-class="user" class="color-main"></svg-icon>
            </span>
          </el-input>
        </el-form-item>
        <el-form-item prop="password">
          <el-input
            name="password"
            :type="pwdType"
            v-model="loginForm.password"
            autocomplete="on"
            placeholder="Please enter the password"
          >
            <span slot="prefix">
              <svg-icon icon-class="password" class="color-main"></svg-icon>
            </span>
            <span slot="suffix" @click="showPwd">
              <svg-icon icon-class="eye" class="color-main"></svg-icon>
            </span>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-input
            name="captcha"
            type="text"
            style="width: 170px; margin-right: 10px"
            v-model="loginForm.captcha"
            @keyup.enter.native="handleLogin"
            placeholder="Clear input verification code"
          >
            <span slot="prefix">
              <svg-icon icon-class="message" class="color-main"></svg-icon>
            </span>
          </el-input>
          <span>
            <img
              :src="codeSrc"
              alt
              style="width: 130px; vertical-align: middle; cursor: pointer;"
              @click="getVerificationCode()"
            />
          </span>
        </el-form-item>
        <el-form-item>
          <el-button style="width: 100%" type="primary" @click.native.prevent="handleLogin">Log in</el-button>
        </el-form-item>
        <el-button
          style="float:right"
          type="text"
          @click.native.prevent="handleRegister"
          v-if="registerSwitch"
        >Register</el-button>
      </el-form>
    </el-card>
  </div>
</template>
<script>
import { setSupport, getSupport, SupportUrl } from '@/utils/support'
import { getVerificationCode } from '@/api/common/AuthCommon'
import { removeStore } from '@/utils/store'

export default {
  name: 'login',
  computed: {
    registerSwitch: {
      get() {
        return this.$store.state.app.register_switch
      },
    },
  },
  data() {
    const validateUsername = (rule, value, callback) => {
      if (value.length > 18 || value.length < 3) {
        callback(new Error('Please enter the correct user name'))
      } else {
        callback()
      }
    }
    const validatePass = (rule, value, callback) => {
      if (value.length < 3) {
        callback(new Error('The password cannot be less than 3 digits'))
      } else {
        callback()
      }
    }
    return {
      loginForm: {
        username: '',
        password: '',
        captcha: '',
        code_key: '',
      },
      loginRules: {
        username: [
          { required: true, trigger: 'blur', validator: validateUsername },
        ],
        password: [
          { required: true, trigger: 'blur', validator: validatePass },
        ],
      },
      pwdType: 'password',
      login_center_bg: null,
      dialogVisible: false,
      codeSrc: null,
    }
  },
  created() {
    this.getVerificationCode()
  },
  methods: {
    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = ''
      } else {
        this.pwdType = 'password'
      }
    },
    handleLogin() {
      this.$refs.loginForm.validate((valid) => {
        if (valid) {
          this.$store
            .dispatch('Login', this.loginForm)
            .then((res) => {
              if (res) {
                if (res.code != 200) {
                  this.getVerificationCode()
                } else {
                  this.$router.push({ path: '/' })
                  removeStore({ name: 'query_selection' }) //Clear search component options cache
                  removeStore({ name: 'extra_query_selection' }) //Clear extra filter cache
                  removeStore({ name: 'pagination_selection' }) //Clear pagination component options cache
                }
              }
            })
            .catch(() => {})
        } else {
          return false
        }
      })
    },
    handleRegister() {
      this.$router.push('/register')
    },
    dialogConfirm() {
      this.dialogVisible = false
      setSupport(true)
      window.location.href = SupportUrl
    },
    dialogCancel() {
      this.dialogVisible = false
      setSupport(false)
    },
    getVerificationCode() {
      getVerificationCode().then((response) => {
        if (response.code == 200) {
          this.codeSrc = response.data.code
          this.loginForm.code_key = response.data.code_key
        }
      })
    },
  },
  mounted: function () {
    setTimeout(function () {
      particlesJS(
        'particles',

        {
          particles: {
            number: {
              value: 100,
              density: {
                enable: true,
                value_area: 800,
              },
            },
            color: {
              value: '#bddcf0',
            },
            shape: {
              type: 'circle',
            },
            opacity: {
              value: 0.8,
              random: true,
              anim: {
                enable: false,
                speed: 2,
                opacity_min: 0.1,
                sync: false,
              },
            },
            size: {
              value: 10,
              random: true,
              anim: {
                enable: true,
                speed: 0.5,
                size_min: 0.3,
                sync: false,
              },
            },
            line_linked: {
              enable: true,
              distance: 150,
              color: '#3cabff',
              opacity: 0.3,
              width: 1,
            },
          },
          interactivity: {
            detect_on: 'canvas',
            events: {
              onhover: {
                enable: true,
                mode: 'grab',
              },
              onclick: {
                enable: false,
              },
              resize: true,
            },
            modes: {
              grab: {
                distance: 200,
                line_linked: {
                  opacity: 0.8,
                },
              },
              bubble: {
                distance: 400,
                size: 40,
                duration: 2,
                opacity: 8,
                speed: 3,
              },
              repulse: {
                distance: 200,
              },
              push: {
                particles_nb: 4,
              },
              remove: {
                particles_nb: 2,
              },
            },
          },
          retina_detect: true,
        }
      )
    }, 1000) //10 seconds delay
  },
}
</script>
<style scoped>
.login-form-layout {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  width: 360px;
  margin: 140px auto;
  border-top: 10px solid #409eff;
}

.login-title {
  text-align: center;
}

.login-center-layout {
  background: #409eff;
  width: auto;
  height: auto;
  max-width: 100%;
  max-height: 100%;
  margin-top: 200px;
}

#particles {
  position: absolute;
  width: 100%;
  height: 100%;
  /*background-repeat: no-repeat;*/
  /*background-size: cover;*/
  /*background-position: 50% 50%;*/
}
</style>
