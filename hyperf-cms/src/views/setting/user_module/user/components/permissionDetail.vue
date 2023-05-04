<template>
  <el-dialog
    title="Edit user permission"
    :visible.sync="permissionDetailData.visible"
    width="35%"
    :close-on-click-modal="false"
    class="field-dialog"
  >
    <div class="filter-container">
      <el-input style="margin-bottom:10px" placeholder="Enter the keyword for filtering" v-model="filterText"></el-input>
      <div class="selected-fields">
        <div class="selected-box" ref="selectedBox">
          <div class="selected-group">
            <el-tree
              :data="permissionList"
              :props="defaultProps"
              show-checkbox
              node-key="name"
              ref="tree"
              :default-expand-all="true"
              indent="40"
              :filter-node-method="filterNode"
              :check-strictly="checkStrictly"
            ></el-tree>
          </div>
        </div>
      </div>
      <el-button
        style="margin-top: 10px;float: right;"
        @click="permissionDetailData.visible = false"
      >Cancel</el-button>
      <el-button
        type="primary"
        style="margin: 10px 10px 0 0;float: right;"
        @click="handleConfirm"
      >Confirm</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  accordUserPermission,
  getPermissionTreeByUser,
} from '@/api/setting/user_module/permission'
export default {
  name: 'PermissionDetail',
  props: {
    permissionDetailData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      //Tree node configuration
      defaultProps: {
        children: 'child',
        label: 'display_name',
      },
      //Filter permissions field
      filterText: '',
      //Whether the Tree component is parent-child related
      checkStrictly: false,
      //permission list
      permissionList: [],
      //list of user permissionsuser permissions
      userHasPermissionList: [],
    }
  },
  mounted() {},
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val)
    },
  },
  created() {},
  methods: {
    init() {
      getPermissionTreeByUser({
        user_id: this.permissionDetailData.userId,
      }).then((response) => {
        if (response.code == 200) {
          this.permissionList = response.data.permission_list

          this.checkStrictly = true //Key point: set to true before assigning a value to the number node
          this.$nextTick(() => {
            this.$refs.tree.setCheckedKeys(response.data.user_has_permission)
            this.checkStrictly = false //Key point: set to true before assigning a value to the number node
          })
        }
      })
    },
    filterNode(value, data) {
      if (!value) return true
      return data.display_name.indexOf(value) !== -1
    },
    handleConfirm() {
      this.$confirm('Confirm the submission of the user permissions, do you continue?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          var checkedKeys = this.$refs.tree.getCheckedKeys()
          var halfCheckedKeys = this.$refs.tree.getHalfCheckedKeys()
          var checkedPermission = checkedKeys.concat(halfCheckedKeys)
          var postData = {
            user_id: this.permissionDetailData.userId,
            user_has_permission: checkedPermission,
          }
          accordUserPermission(postData).then((response) => {
            if (response.code == 200) this.permissionDetailData.visible = false
          })
        })
        .catch(() => {})
    },
  },
}
</script>


<style lang="scss" scoped>
.filter-container::after {
  content: '';
  display: block;
  clear: both;
}

.box-head {
  margin-bottom: 15px;
  padding: 10px 0;
  font-size: 16px;
  font-weight: bold;
  border-bottom: 1px solid #ddd;
}

.all-fields,
.selected-fields {
  padding: 5px 10px;
  border: 1px solid #ddd;
  overflow-y: scroll;
  height: 500px;
}

.all-fields {
  margin-right: 2%;
  //width: 69%;
}

.checkAll {
  display: block;
  margin-bottom: 20px;
  padding: 10px 20px;
  background-color: #f7f9fc;
}

.all-fields ::v-deep .el-checkbox__label {
  font-size: 16px;
}
.all-fields ::v-deep .el-checkbox__inner {
  width: 16px;
  height: 16px;
  &::before {
    top: 6px;
  }
  &::after {
    top: 2px;
    left: 5px;
  }
}
.checkAll ::v-deep .el-checkbox__label {
  font-weight: bold;
}

.checkItem {
  margin: 0 0 10px 0;
  width: 33.333333%;
}
.checkItem:hover ::v-deep .el-checkbox__inner {
  border-color: #409eff;
}

.selected-group {
  margin-bottom: 15px;
  padding-bottom: 15px;
  border-bottom: 1px solid #ddd;
  &:last-child {
    border-bottom: none;
  }
}

.selected-item {
  position: relative;
  margin-bottom: 10px;
  padding: 10px 20px;
  background-color: #f8f8f8;
  border-radius: 10px;
  cursor: move;
  &--active {
    background-color: #ddd;
  }
  &:last-child {
    margin-bottom: 0;
  }

  .cancelItemBtn {
    position: absolute;
    top: 50%;
    right: 20px;
    margin-top: -8px;
    width: 16px;
    height: 16px;
    cursor: pointer;

    &::before,
    &::after {
      content: '';
      display: block;
      position: absolute;
      top: 50%;
      margin-top: -1px;
      width: 100%;
      height: 2px;
      background-color: #9f9f9f;
      transition: all 0.3s;
    }
    &::before {
      transform: rotate(45deg);
    }
    &::after {
      transform: rotate(-45deg);
    }

    &:hover {
      &::before,
      &::after {
        background-color: #f00;
      }
    }
  }
}
</style>
