<template>
  <el-dialog
    :title="userDetailDialogData.userDetailTitle"
    :visible.sync="userDetailDialogData.userDetailDialogVisible"
    width="50%"
    :close-on-click-modal="false"
  >
    <el-form :model="user" :rules="rules" ref="userForm" label-width="150px">
      <el-form-item label="Username:" prop="username">
        <el-input
          v-model="user.username"
          :disabled="userDetailDialogData.isEdit"
          placeholder="Please fill in the username (only)"
        ></el-input>
      </el-form-item>
      <el-form-item label="User's Nickname:">
        <el-input v-model="user.desc" placeholder="Please fill in the user's nickname"></el-input>
      </el-form-item>
      <el-form-item label="Password:" prop="password" v-show="userDetailDialogData.isEdit == false">
        <el-input v-model="user.password" type="password" autocomplete="off" placeholder="Please enter the password"></el-input>
      </el-form-item>
      <el-form-item
        label="Confirm Password:"
        prop="password_confirmation"
        v-show="userDetailDialogData.isEdit == false"
      >
        <el-input
          type="password"
          v-model="user.password_confirmation"
          autocomplete="off"
          placeholder="Please enter the password again"
        ></el-input>
      </el-form-item>
      <el-form-item label="Phone number:" prop="mobile">
        <el-input v-model="user.mobile" placeholder="Please fill in the user's mobile phone number"></el-input>
      </el-form-item>
      <el-form-item label="Email:" prop="email">
        <el-input v-model="user.email" placeholder="Please fill in the user mailbox address"></el-input>
      </el-form-item>
      <el-form-item label="Role:" prop="roleData">
        <el-select
          v-model="user.roleData"
          multiple
          filterable
          allow-create
          default-first-option
          size="medium"
          placeholder="Please select a user role"
        >
          <el-option
            v-for="(item, key) in roles"
            :key="key"
            :label="item.description"
            :value="item.name"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Avatar:">
        <single-upload v-model="user.avatar" savePath="admin_face"></single-upload>
      </el-form-item>
      <el-form-item label="Gender:">
        <el-radio-group v-model="user.sex">
          <el-radio
            v-for="dict in userDetailDialogData.sexOptions"
            :key="dict.dict_value"
            :label="dict.dict_value"
          >{{dict.dict_label}}</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="state:">
        <el-radio-group v-model="user.status">
          <el-radio
            v-for="dict in userDetailDialogData.statusOptions"
            :key="dict.dict_value"
            :label="dict.dict_value"
          >{{dict.dict_label}}</el-radio>
        </el-radio-group>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('userForm')">Submit</el-button>
      <el-button @click="resetForm('userForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createUser,
  updateUser,
  editUser,
} from '@/api/setting/user_module/user'
import { getRoleByTree } from '@/api/setting/user_module/role'
import { validatPhone } from '@/utils/validate'
import SingleUpload from '@/components/Upload/singleUpload'
const defaultUser = {
  username: '',
  password: '',
  desc: '',
  avatar: '',
  status: 1,
  sex: 0,
  mobile: '',
  roleData: '',
  email: '',
  password_confirmation: '',
}
export default {
  name: 'UserDetail',
  components: { SingleUpload },
  props: {
    userDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    var validatePhone = (rule, value, callback) => {
      if (!validatPhone(value)) {
        callback(new Error('The mobile phone number format is incorrect'))
      } else {
        callback()
      }
    }
    return {
      user: Object.assign({}, defaultUser),
      roles: '',
      rules: {
        username: [
          { required: true, message: 'Please input user name', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
        password: [
          { required: true, message: 'Please enter the password', trigger: 'blur' },
          {
            min: 6,
            max: 18,
            message: 'The length is from 6 to 18 characters',
            trigger: 'blur',
          },
        ],
        mobile: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        password_confirmation: [
          { required: true, message: 'Please enter the password', trigger: 'blur' },
        ],
        roleData: [
          { required: true, message: 'Please choose at least one role', trigger: 'blur' },
        ],
      },
    }
  },
  created() {
    getRoleByTree({ type: 'tree' }).then((response) => {
      if (response.code == 200) this.roles = response.data.list
    })
  },
  methods: {
    getUserInfo() {
      //Judging whether it is a modification
      if (this.userDetailDialogData.isEdit == true) {
        editUser(this.userDetailDialogData.userId).then((response) => {
          if (response.code == 200) {
            let userData = response.data.list
            this.user = Object.assign({}, userData)
          }
        })
        delete this.rules.password_confirmation
        delete this.rules.password
        delete this.rules.username
        delete this.user.username
        delete this.user.password_confirmation
        delete this.user.password
      } else {
        this.user = Object.assign({}, defaultUser)
      }
    },
    onSubmit(userForm) {
      this.$refs[userForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.userDetailDialogData.isEdit) {
              updateUser(this.user.id, this.user).then((response) => {
                if (response.code == 200) {
                  this.$refs[userForm].resetFields()
                  this.$parent.getList()
                  this.userDetailDialogData.userDetailDialogVisible = false
                }
              })
            } else {
              createUser(this.user).then((response) => {
                if (response.code == 200) {
                  this.$refs[userForm].resetFields()
                  this.user = Object.assign({}, defaultUser)
                  this.$parent.getList()
                  this.userDetailDialogData.userDetailDialogVisible = false
                }
              })
            }
          })
        } else {
          this.$message({
            message: 'Verification failed',
            type: 'error',
            duration: 1000,
          })
          return false
        }
      })
    },
  },
}
</script>

<style>
</style>
