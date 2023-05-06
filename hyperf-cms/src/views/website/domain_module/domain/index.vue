<template>
  <div class="app-container">
    <conditional-filter
      excelTitle="Domain list"
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :columns.sync="columns"
      :list="list"
      :multipleSelection="multipleSelection"
      :batchDelete="false"
      @getList="getList"
      @handleAdd="handleAdd"
      @handleBatchDelete="handleBatchDelete"
    >
      <template slot="extraForm">
        <el-form-item label="Domain Name:">
          <el-input
            v-model="listQuery.description"
            class="input-width"
            placeholder="Domain name"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
      </template>
    </conditional-filter>

    <div class="table-container">
      <el-table
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="60" align="center"></el-table-column>
        <el-table-column
          sortable
          label="ID"
          prop="id"
          width="80"
          align="center"
          v-if="columns[0].visible"
        ></el-table-column>
        <el-table-column
          label="Domain Name"
          prop="name"
          width="180"
          align="center"
          v-if="columns[1].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Desc"
          prop="description"
          width="180"
          align="center"
          v-if="columns[2].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Created at"
          width="400"
          prop="created_at"
          align="center"
          v-if="columns[3].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Updated at"
          width="400"
          prop="updated_at"
          align="center"
          v-if="columns[4].visible"
        ></el-table-column>
        <el-table-column label="Action" align="center">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-edit"
              type="primary"
              size="mini"
              @click="handleEdit(scope.$index, scope.row)"
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
    </div>
    <div class="pagination-container">
      <Pagination
        v-show="total>0"
        :total="total"
        :page.sync="listQuery.cur_page"
        :limit.sync="listQuery.page_size"
        @pagination="getList"
      ></Pagination>
    </div>

    <domain-detail ref="domainDetail" :domainDetailDialogData="domainDetailDialogData"></domain-detail>
  </div>
</template>
<script>
import { getDomainList, deleteDomain } from '@/api/website/domain_module/domain'
import { formatDate } from '@/utils/date'
import DomainDetail from './components/domainDetail'

const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
}
export default {
  name: 'Api:website/domain_module/domain/list-index',
  components: {
    DomainDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `domain Name`, visible: true },
        { key: 2, field: 'description', label: `Desc`, visible: true },
        { key: 3, field: 'created_at', label: `Created at`, visible: true },
        { key: 4, field: 'updated_at', label: `Updated at`, visible: true },
      ],
      domainDetailDialogData: {
        domainDetailDialogVisible: false,
        domainDetailTitle: '',
        isEdit: false,
        domainId: '',
      },
      
    }
  },
  created() {
    this.getList()
  },
  watch: {},
  filters: {
    formatLoginTime(time) {
      let date = new Date(time * 1000)
      return formatDate(date, 'yyyy-MM-dd hh:mm:ss')
    },
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    //get Domain list
    getList() {
      getDomainList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    //Add Domain operation
    handleAdd() {
      this.domainDetailDialogData.domainDetailDialogVisible = true
      this.domainDetailDialogData.domainDetailTitle = 'Add a Domain'
      this.domainDetailDialogData.isEdit = false
      this.$refs['domainDetail'].getDomainInfo()
    },
    //Edit Domain action
    handleEdit(index, row) {
      this.domainDetailDialogData.domainDetailDialogVisible = true
      this.domainDetailDialogData.domainDetailTitle = 'Edit "' + row.name + '" Domain'
      this.domainDetailDialogData.isEdit = true
      this.domainDetailDialogData.domainId = row.id
      this.$refs['domainDetail'].getDomainInfo()
    },
    //Edit user function permissions
    handleViewDomain(row) {
      this.permissionDetailData.domainId = row.id
      this.$refs['permissionDetail'].init()
      this.permissionDetailData.visible = true
    },
    handleDelete(row) {
      this.$confirm('Confirm the delete ','Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          deleteDomain(row.id).then(
            (response) => {
              if (response.code == 200) {
                this.getList()
              }
            }
          )
        })
        .catch((err) => {})
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
