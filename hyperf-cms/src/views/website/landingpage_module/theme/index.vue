<template>
  <div class="app-container">
    <conditional-filter
      excelTitle="Theme list"
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
        <el-form-item label="Theme Name:">
          <el-input
            v-model="listQuery.description"
            class="input-width"
            placeholder="Theme name"
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
          label="Theme Name"
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

    <theme-detail ref="themeDetail" :themeDetailDialogData="themeDetailDialogData"></theme-detail>
  </div>
</template>
<script>
import { getThemeList, deleteTheme } from '@/api/website/landingpage_module/theme'
import { formatDate } from '@/utils/date'
import ThemeDetail from './components/themeDetail'

const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
}
export default {
  name: 'Api:website/landingpage_module/theme/list-index',
  components: {
    ThemeDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `Theme Name`, visible: true },
        { key: 2, field: 'description', label: `Desc`, visible: true },
        { key: 3, field: 'created_at', label: `Created at`, visible: true },
        { key: 4, field: 'updated_at', label: `Updated at`, visible: true },
      ],
      themeDetailDialogData: {
        themeDetailDialogVisible: false,
        themeDetailTitle: '',
        isEdit: false,
        themeId: '',
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
    //get theme list
    getList() {
      getThemeList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    //Add theme operation
    handleAdd() {
      this.themeDetailDialogData.themeDetailDialogVisible = true
      this.themeDetailDialogData.themeDetailTitle = 'Add a theme'
      this.themeDetailDialogData.isEdit = false
      this.$refs['themeDetail'].getThemeInfo()
    },
    //Edit theme action
    handleEdit(index, row) {
      this.themeDetailDialogData.themeDetailDialogVisible = true
      this.themeDetailDialogData.themeDetailTitle = 'Edit "' + row.name + '" theme'
      this.themeDetailDialogData.isEdit = true
      this.themeDetailDialogData.themeId = row.id
      this.$refs['themeDetail'].getThemeInfo()
    },
    //Edit user function permissions
    handleViewTheme(row) {
      this.permissionDetailData.themeId = row.id
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
          deleteTheme(row.id).then(
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
