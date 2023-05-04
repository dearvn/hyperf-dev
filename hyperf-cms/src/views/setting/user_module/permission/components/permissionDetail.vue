<template>
  <el-dialog
    :title="permissionDetailDialogData.permissionDetailTitle"
    :visible.sync="permissionDetailDialogData.permissionDetailDialogVisible"
    width="600px"
    :close-on-click-modal="false"
  >
    <el-form
      ref="permissionForm"
      :model="permission"
      :rules="rules"
      label-width="80px"
      size="medium"
    >
      <el-row>
        <el-col :span="24">
          <el-form-item label="Superior menu">
            <treeselect
              v-model="permission.parent_id"
              :options="menuOptions"
              :normalizer="normalizer"
              :show-count="true"
              placeholder="Choose a superior menu"
            />
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="Permissions" prop="type">
            <el-radio-group v-model="permission.type">
              <el-radio :label="1">Table of contents</el-radio>
              <el-radio :label="2">menu</el-radio>
              <el-radio :label="3">Button/interface</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item v-if="permission.type != 3" label="Menu icon">
            <el-popover
              placement="bottom-start"
              width="460"
              trigger="click"
              @show="$refs['iconSelect'].reset()"
            >
              <IconSelect ref="iconSelect" @selected="selected" />
              <el-input slot="reference" v-model="permission.icon" placeholder="Click to select icon" readonly>
                <svg-icon
                  v-if="permission.icon"
                  slot="prefix"
                  :icon-class="permission.icon"
                  class="el-input__icon"
                  style="height: 32px;width: 16px;"
                />
                <i v-else slot="prefix" class="el-icon-search el-input__icon" />
              </el-input>
            </el-popover>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Display sorting" prop="orderNum">
            <el-input-number v-model="permission.sort" controls-position="right" :min="0" />
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="Permission name" prop="display_name">
            <el-input v-model="permission.display_name" placeholder="Please enter the permissions name" />
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="Permissions description" prop="display_desc">
            <el-input v-model="permission.display_desc" placeholder="Please enter permissions description" />
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="Permissions">
            <el-input v-model="permission.name" placeholder="Please authorize" />
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item v-if="permission.type != 3" label="Routing address" prop="path">
            <el-input v-model="permission.url" placeholder="Please enter the routing address" />
          </el-form-item>
        </el-col>
        <el-col :span="12" v-if="permission.type != 3">
          <el-form-item label="Component path" prop="component">
            <el-input v-model="permission.component" placeholder="Please enter the component path" />
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item v-if="permission.type != '3'" label="Whether an external link">
            <el-radio-group v-model="permission.is_link">
              <el-radio
                v-for="dict in permissionIsLinkOptions"
                :key="dict.dict_value"
                :label="dict.dict_value"
              >{{dict.dict_label}}</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item v-if="permission.type == '2'" label="Whether to hide">
            <el-radio-group v-model="permission.hidden">
              <el-radio
                v-for="dict in permissionHiddenOptions"
                :key="dict.dict_value"
                :label="dict.dict_value"
              >{{dict.dict_label}}</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Menu status">
            <el-radio-group v-model="permission.status">
              <el-radio
                v-for="dict in permissionStatusOptions"
                :key="dict.dict_value"
                :label="dict.dict_value"
              >{{dict.dict_label}}</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('permissionForm')">Submit</el-button>
      <el-button @click="resetForm('permissionForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  getPermission,
  editPermission,
  createPermission,
  updatePermission,
} from '@/api/setting/user_module/permission'
import { validatPhone } from '@/utils/validate'
import SingleUpload from '@/components/Upload/singleUpload'
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import IconSelect from '@/components/IconSelect'
import { handleTree } from '@/utils/functions'

const defaultPermission = {
  parent_id: 0,
  name: '',
  display_name: '',
  display_desc: '',
  url: '',
  component: '',
  icon: '',
  type: 1,
  hidden: 0,
  status: 1,
  sort: 99,
  is_link: 0,
}
export default {
  name: 'permissionDetail',
  components: { SingleUpload, Treeselect, IconSelect },
  props: {
    permissionDetailDialogData: {
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
      permission: Object.assign({}, defaultPermission),
      menuOptions: [],
      permissionHiddenOptions: [],
      permissionStatusOptions: [],
      permissionIsLinkOptions: [],
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
            min: 2,
            max: 18,
            message: 'The length is from 2 to 18 characters',
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
    this.getDicts('sys_permission_hidden').then((response) => {
      if (response.code == 200)
        this.permissionHiddenOptions = response.data.list
    })
    this.getDicts('sys_permission_status').then((response) => {
      if (response.code == 200)
        this.permissionStatusOptions = response.data.list
    })
    this.getDicts('sys_permission_is_link').then((response) => {
      if (response.code == 200)
        this.permissionIsLinkOptions = response.data.list
    })
  },
  methods: {
    // Select icon
    selected(name) {
      this.permission.icon = name
    },
    /** Conversion menu data structure */
    normalizer(node) {
      if (node.children && !node.children.length) {
        delete node.children
      }
      return {
        id: node.id,
        label: node.display_name,
        children: node.children,
      }
    },
    /** Query menu pull down tree structure */
    getTreeselect() {
      getPermission().then((response) => {
        this.menuOptions = []
        const menu = { id: 0, display_name: 'Mainstay', children: [] }
        menu.children = response.data.list
        this.menuOptions.push(menu)
      })
    },
    getPermissionInfo() {
      //Determine whether it is modified
      if (this.permissionDetailDialogData.isEdit == true) {
        editPermission(this.permissionDetailDialogData.permissionId).then(
          (response) => {
            if (response.code == 200) {
              let permissionData = response.data.list
              this.permission = Object.assign({}, permissionData)
            }
          }
        )
      } else {
        this.permission = Object.assign({}, defaultPermission)
        this.permission.parent_id = this.permissionDetailDialogData.parent_id
      }
    },
    onSubmit(permissionForm) {
      this.$refs[permissionForm].validate((valid) => {
        if (valid) {
          this.$confirm('Do you want to sumit data?', 'Alert', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.permissionDetailDialogData.isEdit) {
              updatePermission(this.permission.id, this.permission).then(
                (response) => {
                  if (response.code == 200) {
                    this.$refs[permissionForm].resetFields()
                    this.$parent.getList()
                    this.permissionDetailDialogData.permissionDetailDialogVisible = false
                  }
                }
              )
            } else {
              createPermission(this.permission).then((response) => {
                if (response.code == 200) {
                  this.$refs[permissionForm].resetFields()
                  this.permission = Object.assign({}, defaultPermission)
                  this.$parent.getList()
                  this.permissionDetailDialogData.permissionDetailDialogVisible = false
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
    resetForm(permissionForm) {
      this.$refs[permissionForm].resetFields()
      this.brand = Object.assign({}, defaultPermission)
    },
  },
}
</script>

<style>
</style>
