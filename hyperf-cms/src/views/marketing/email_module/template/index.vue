<template>
  <div class="app-container">
    <conditional-filter
      excelTitle="Email Template list"
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
        <el-form-item label="Template Name:">
          <el-input
            v-model="listQuery.description"
            class="input-width"
            placeholder="Template name"
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
          label="Template Name"
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
            <el-dropdown-item divided>
              <el-button
                icon="el-icon-delete"
                type="danger"
                size="mini"
                @click="handleDelete(scope.row)"
              >Delete</el-button>
            </el-dropdown-item>
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

    <template-detail ref="templateDetail" :templateDetailDialogData="templateDetailDialogData"></template-detail>
  </div>
</template>
<script>
import { getTemplateList, deleteTemplate } from '@/api/marketing/email_module/template'
import { formatDate } from '@/utils/date'
import TemplateDetail from './components/templateDetail'

const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
}
export default {
  name: 'Api:marketing/email_module/template/list-index',
  components: {
    TemplateDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `Template Name`, visible: true },
        { key: 2, field: 'description', label: `Desc`, visible: true },
        { key: 3, field: 'created_at', label: `Created at`, visible: true },
        { key: 4, field: 'updated_at', label: `Updated at`, visible: true },
      ],
      templateDetailDialogData: {
        templateDetailDialogVisible: false,
        templateDetailTitle: '',
        isEdit: false,
        templateId: '',
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
    //get template list
    getList() {
      getTemplateList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    //Add template operation
    handleAdd() {
      this.templateDetailDialogData.templateDetailDialogVisible = true
      this.templateDetailDialogData.templateDetailTitle = 'Add a template'
      this.templateDetailDialogData.isEdit = false
      this.$refs['templateDetail'].getTemplateInfo()
    },
    //Edit template action
    handleEdit(index, row) {
      this.templateDetailDialogData.templateDetailDialogVisible = true
      this.templateDetailDialogData.templateDetailTitle = 'Edit "' + row.name + '" template'
      this.templateDetailDialogData.isEdit = true
      this.templateDetailDialogData.templateId = row.id
      this.$refs['templateDetail'].getTemplateInfo()
    },
    //Edit user function permissions
    handleViewTemplate(row) {
      this.permissionDetailData.templateId = row.id
      this.$refs['permissionDetail'].init()
      this.permissionDetailData.visible = true
    },
    handleDelete(row) {
      this.$confirm('Confirm the delete ','prompt', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          deleteTemplate(row.id).then(
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
