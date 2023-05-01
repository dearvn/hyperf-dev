<template>
  <el-form ref="form" :model="user" :rules="rules" label-width="80px">
    <el-form-item label="Old Password" prop="old_password">
      <el-input v-model="user.old_password" placeholder="Please enter the old password" type="password" />
    </el-form-item>
    <el-form-item label="New Password" prop="new_password">
      <el-input v-model="user.new_password" placeholder="Please enter a new password" type="password" />
    </el-form-item>
    <el-form-item label="Confirm Password" prop="confirm_password">
      <el-input v-model="user.confirm_password" placeholder="Please confirm your password" type="password" />
    </el-form-item>
    <el-form-item>
      <el-button type="primary" size="mini" @click="submit">Submit</el-button>
      <el-button type="danger" size="mini" @click="close">Close</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { resetPassword } from '@/api/setting/user_module/user'

export default {
  data() {
    const equalToPassword = (rule, value, callback) => {
      if (this.user.new_password !== value) {
        callback(new Error('The passwords in two inputs are inconsistent'))
      } else {
        callback()
      }
    }
    return {
      test: '1test',
      user: {
        old_password: undefined,
        new_password: undefined,
        confirm_password: undefined,
      },
      //form validation
      rules: {
        old_password: [
          { required: true, message: 'The old password cannot be empty', trigger: 'blur' },
        ],
        new_password: [
          { required: true, message: 'The new password cannot be empty', trigger: 'blur' },
          {
            min: 6,
            max: 20,
            message: 'The length is 6 to 20 characters',
            trigger: 'blur',
          },
        ],
        confirm_password: [
          { required: true, message: 'confirm password can not be blank', trigger: 'blur' },
          { required: true, validator: equalToPassword, trigger: 'blur' },
        ],
      },
    }
  },
  methods: {
    submit() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          let postData = {
            uid: this.$store.getters.userId,
            old_password: this.user.old_password,
            new_password: this.user.new_password,
            confirm_password: this.user.confirm_password,
          }
          resetPassword({ postData: postData }).then((response) => {})
        }
      })
    },
    close() {
      this.$router.push({ path: '/' })
    },
  },
}
</script>
