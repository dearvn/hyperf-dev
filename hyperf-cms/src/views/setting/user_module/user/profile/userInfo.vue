<template>
  <el-form ref="form" :model="user" :rules="rules" label-width="80px">
    <el-form-item label="Desc" prop="desc">
      <el-input v-model="user.desc" />
    </el-form-item>
    <el-form-item label="Mobile" prop="mobile">
      <el-input v-model="user.mobile" maxlength="11" />
    </el-form-item>
    <el-form-item label="Email" prop="email">
      <el-input v-model="user.email" maxlength="50" />
    </el-form-item>
    <el-form-item label="Address" prop="address">
      <el-input v-model="user.address" maxlength="50" />
    </el-form-item>
    <el-form-item label="Gender">
      <el-radio-group v-model="user.sex">
        <el-radio
          v-for="dict in sexOptions"
          :key="dict.dict_value"
          :label="dict.dict_value"
        >{{dict.dict_label}}</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" size="mini" @click="submit">Submit</el-button>
      <el-button type="danger" size="mini" @click="close">Close</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { profileEdit } from '@/api/setting/user_module/user'

export default {
  props: {
    user: {
      type: Object,
    },
  },
  created() {
    this.getDicts('sys_user_sex').then((response) => {
      if (response.code == 200) this.sexOptions = response.data.list
    })
  },
  data() {
    return {
      sexOptions: [],
      // 表单校验
      rules: {
        desc: [
          { required: true, message: 'User nickname cannot be empty', trigger: 'blur' },
        ],
        email: [
          { required: true, message: 'The mailbox address cannot be empty', trigger: 'blur' },
          {
            type: 'email',
            message: "'Please input the correct email address",
            trigger: ['blur', 'change'],
          },
        ],
        mobile: [
          { required: true, message: 'Phone number can not be blank', trigger: 'blur' },
          {
            pattern: /^1[3|4|5|6|7|8|9][0-9]\d{8}$/,
            message: 'Please enter the correct phone number',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  methods: {
    submit() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          profileEdit(this.user.id, this.user).then((response) => {})
        }
      })
    },
    close() {
      this.$store.dispatch('tagsView/delView', this.$route)
      this.$router.push({ path: '/index' })
    },
  },
}
</script>
