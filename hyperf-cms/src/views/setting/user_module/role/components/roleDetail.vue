<template>
  <el-dialog
    :title="roleDetailDialogData.roleDetailTitle"
    :visible.sync="roleDetailDialogData.roleDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="role" :rules="rules" ref="roleForm" label-width="150px">
      <el-form-item label="Role Name" prop="name">
        <el-input v-model="role.name" plachod auto-complete="off" size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Desc" prop="description">
        <el-input v-model="role.description" auto-complete="off" size="medium"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('roleForm')">Submit</el-button>
      <el-button @click="resetForm('roleForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createRole,
  updateRole,
  editRole,
  getRoleByTree,
} from '@/api/setting/user_module/role'
const defaultRole = {
  name: '',
  description: '',
  id: '',
}
export default {
  name: 'roleDetail',
  props: {
    roleDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      role: Object.assign({}, defaultRole),
      rules: {
        name: [
          { required: true, message: 'Love input character unique identification', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
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
    getRoleInfo() {
      //Judging whether it is a modification
      if (this.roleDetailDialogData.isEdit == true) {
        editRole(this.roleDetailDialogData.roleId).then((response) => {
          if (response.code == 200) {
            let roleData = response.data.list
            this.role = Object.assign({}, roleData)
          }
        })
        delete this.rules.name
        delete this.role.name
        delete this.role.description
      } else {
        this.role = Object.assign({}, defaultRole)
      }
    },
    onSubmit(roleForm) {
      this.$refs[roleForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit the data', 'prompt', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.roleDetailDialogData.isEdit) {
              updateRole(this.role.id, this.role).then((response) => {
                if (response.code == 200) {
                  this.$refs[roleForm].resetFields()
                  this.$parent.getList()
                  this.roleDetailDialogData.roleDetailDialogVisible = false
                }
              })
            } else {
              createRole(this.role).then((response) => {
                if (response.code == 200) {
                  this.$refs[roleForm].resetFields()
                  this.role = Object.assign({}, defaultRole)
                  this.$parent.getList()
                  this.roleDetailDialogData.roleDetailDialogVisible = false
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
    resetForm(roleForm) {
      this.$refs[roleForm].resetFields()
      this.brand = Object.assign({}, defaultRole)
    },
  },
}
</script>

<style>
</style>
