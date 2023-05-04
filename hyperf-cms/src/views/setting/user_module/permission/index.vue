<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :columns.sync="columns"
      :list="list"
      :multipleSelection="multipleSelection"
      :batchDelete="false"
      @getList="getList"
      @handleAdd="handleAdd"
      @handleBatchDelete="handleBatchDelete"
      excelTitle="Permission list"
    >
      <template slot="extraForm">
        <el-form-item label="Permission name search：">
          <el-input
            v-model="listQuery.display_name"
            class="input-width"
            placeholder="Account search："
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Permissions logo search：">
          <el-input
            v-model="listQuery.name"
            class="input-width"
            placeholder="Permissions logo search："
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Status：">
          <el-select v-model="listQuery.status" clearable class="input-width" placeholder="Status：">
            <el-option
              v-for="dict in statusOptions"
              :key="dict.dict_value"
              :label="dict.dict_label"
              :value="dict.dict_value"
            ></el-option>
          </el-select>
        </el-form-item>
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-table
        :data="list"
        style="width: 100%;margin-bottom: 20px;"
        row-key="id"
        size="small"
        :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
      >
        <el-table-column prop="display_name" label="Display name" width="200" v-if="columns[0].visible"></el-table-column>
        <el-table-column
          prop="icon"
          label="icon"
          align="center"
          width="150"
          v-if="columns[1].visible"
        >
          <template slot-scope="scope">
            <svg-icon :icon-class="scope.row.icon" />
          </template>
        </el-table-column>
        <el-table-column prop="sort" label="Sort" width="80" v-if="columns[2].visible"></el-table-column>
        <el-table-column prop="name" label="Permissionsissions" v-if="columns[3].visible"></el-table-column>
        <el-table-column prop="component" label="Component pathonent path" v-if="columns[4].visible"></el-table-column>
        <el-table-column
          prop="status"
          label="状态"
          width="80"
          :formatter="statusFormat"
          v-if="columns[5].visible"
        ></el-table-column>
        <el-table-column prop="created_at" label="Created at" width="180" v-if="columns[6].visible"></el-table-column>
        <el-table-column label="Action" width="280">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-plus"
              type="primary"
              size="mini"
              @click="handleAdd(scope.row)"
            >Add</el-button>
            <el-button
              icon="el-icon-edit"
              type="warning"
              size="mini"
              @click="handleEdit(scope.row)"
            >Edit</el-button>
            <el-button
              icon="el-icon-delete"
              type="danger"
              size="mini"
              @click="handleDelete(scope.row)"
            >Delete</el-button>
          </template>
        </el-table-column>
      </el-table>

      <permission-detail
        ref="permissionDetail"
        :permissionDetailDialogData="permissionDetailDialogData"
      ></permission-detail>
    </div>
  </div>
</template>
<script>
import {
  getPermission,
  deletePermission,
} from '@/api/setting/user_module/permission'
import PermissionDetail from './components/permissionDetail'
const defaultListQuery = {
  display_name: '',
  name: '',
  status: '',
}
export default {
  components: {
    PermissionDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      statusOptions: [],
      permissionDetailDialogData: {
        permissionDetailDialogVisible: false,
        permissionDetailTitle: '',
        isEdit: false,
        permissionId: '',
      },
      columns: [
        { key: 0, field: 'display_name', label: `Display name`, visible: true },
        { key: 1, field: 'icon', label: `Icon`, visible: true },
        { key: 2, field: 'sort', label: `Sort`, visible: true },
        { key: 3, field: 'name', label: `Name`, visible: true },
        { key: 4, field: 'component', label: `Component`, visible: true },
        { key: 5, field: 'status', label: `Status`, visible: true },
        { key: 6, field: 'created_at', label: `Created At`, visible: true },
      ],
    }
  },
  created() {
    this.getList()
    this.getDicts('sys_permission_status').then((response) => {
      if (response.code == 200) this.statusOptions = response.data.list
    })
  },
  watch: {},
  methods: {
    //Obtaining permissions list
    getList() {
      getPermission(this.listQuery).then((response) => {
        if (response.code == 200) this.list = response.data.list
      })
    },
    //Add permissions operation
    handleAdd(row) {
      if (row != undefined) {
        this.permissionDetailDialogData.parent_id = row.id
      } else {
        this.permissionDetailDialogData.parent_id = 0
      }
      this.permissionDetailDialogData.permissionDetailDialogVisible = true
      this.permissionDetailDialogData.permissionDetailTitle = 'Add permissions'
      this.permissionDetailDialogData.isEdit = false
      this.$refs['permissionDetail'].getPermissionInfo()
      this.$refs['permissionDetail'].getTreeselect()
    },
    //Edit permission operation
    handleEdit(row) {
      this.permissionDetailDialogData.permissionDetailDialogVisible = true
      this.permissionDetailDialogData.permissionDetailTitle =
        'Revise "' + row.display_name + '" Authority'
      this.permissionDetailDialogData.isEdit = true
      this.permissionDetailDialogData.permissionId = row.id
      this.$refs['permissionDetail'].getPermissionInfo()
      this.$refs['permissionDetail'].getTreeselect()
    },
    handleDelete(row) {
      this.deletePermission(row.id)
    },
    //Delete permissions operation
    deletePermission(id) {
      this.$confirm('Do you want to do this delete operation?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        deletePermission(id).then((response) => {
          if (response.code == 200) this.getList()
        })
      })
    },
    //Permission status dictionary translation
    statusFormat(row, column) {
      return this.selectDictLabel(this.statusOptions, row.status)
    },
  },
}
</script>
<style type="text/css">
</style>
<style scoped>
.input-width {
  width: 203px;
}

.tree {
  margin-top: 20px;
}

.el-tree > .el-tree-node__content {
  height: 46px;
}

.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 16px;
  padding-right: 8px;
}
</style>
