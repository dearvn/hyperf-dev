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
        :model="registerForm"
        :rules="registerRules"
        ref="registerForm"
        label-position="left"
      >
        <div style="text-align: center"></div>
        <h2 class="login-title color-main" style="font-size:25px">
          Hyperf-dev
        </h2>
        <el-form-item prop="username">
          <el-input name="username" v-model="registerForm.username" placeholder="Please fill in your username (unique)">
            <span slot="prefix">
              <svg-icon icon-class="user" class="color-main"></svg-icon>
            </span>
          </el-input>
        </el-form-item>
        <el-form-item prop="desc">
          <el-input name="desc" v-model="registerForm.desc" placeholder="Please fill in your user's nickname">
            <span slot="prefix">
              <svg-icon icon-class="form" class="color-main"></svg-icon>
            </span>
          </el-input>
        </el-form-item>
        <el-form-item prop="password">
          <el-input
            name="password"
            :type="pwdType"
            v-model="registerForm.password"
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
        <el-form-item prop="password_confirmation">
          <el-input
            name="password_confirmation"
            :type="pwdType"
            v-model="registerForm.password_confirmation"
            autocomplete="on"
            placeholder="Please enter the password again"
          >
            <span slot="prefix">
              <svg-icon icon-class="password" class="color-main"></svg-icon>
            </span>
            <span slot="suffix" @click="showPwd">
              <svg-icon icon-class="eye" class="color-main"></svg-icon>
            </span>
          </el-input>
        </el-form-item>
        <el-form-item prop="captcha">
          <el-input
            name="captcha"
            type="text"
            style="width: 190px; margin-right: 10px"
            v-model="registerForm.captcha"
            @keyup.enter.native="handleRegister"
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
          <el-button style="width: 100%" type="primary" @click.native.prevent="handleRegister">Register</el-button>
        </el-form-item>
        <el-button style="float:right" type="text" @click.native.prevent="handleLogin">Login</el-button>
      </el-form>
    </el-card>
  </div>
</template>
<script>
import { isvalidUsername } from '@/utils/validate'
import { setSupport, getSupport, SupportUrl } from '@/utils/support'
import login_center_bg from '@/assets/images/login_center_bg.png'
import { getVerificationCode } from '@/api/common/AuthCommon'
import store from '@/store'
import { Message, MessageBox } from 'element-ui'
import { removeStore } from '@/utils/store'
import { register } from '@/api/auth/login'

export default {
  name: 'register',
  data() {
    const validateUsername = (rule, value, callback) => {
      if (value.length > 18 || value.length < 3) {
        callback(new Error('Please enter the correct user name'))
      } else {
        callback()
      }
    }
    const validateDesc = (rule, value, callback) => {
      if (value.length > 18 || value.length < 3) {
        callback(new Error('Please enter the correct user nickname'))
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
    const validateConfirmPass = (rule, value, callback) => {
      if (value.length < 3) {
        callback(new Error('The password cannot be less than 3 digits'))
      } else if (value != this.registerForm.password) {
        callback(new Error('Inconsistent password'))
      } else {
        callback()
      }
    }
    return {
      registerForm: {
        username: '',
        password: '',
        password_confirmation: '',
        desc: '',
        captcha: '',
        code_key: '',
      },
      registerRules: {
        username: [
          { required: true, trigger: 'blur', validator: validateUsername },
        ],
        desc: [{ required: true, trigger: 'blur', validator: validateDesc }],
        password: [
          { required: true, trigger: 'blur', validator: validatePass },
        ],
        password_confirmation: [
          { required: true, trigger: 'blur', validator: validateConfirmPass },
        ],
        captcha: [{ required: true, trigger: 'blur', message: 'Please fill in the verification code' }],
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
      this.$router.push('/login')
    },
    handleRegister() {
      this.$refs.registerForm.validate((valid) => {
        if (valid) {
          register(this.registerForm).then((response) => {
            if (response.code == 200) {
              this.$router.push('/login/')
            } else {
              this.getVerificationCode()
            }
          })
        } else {
          return false
        }
      })
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
          this.registerForm.code_key = response.data.code_key
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
  width: 400px;
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
