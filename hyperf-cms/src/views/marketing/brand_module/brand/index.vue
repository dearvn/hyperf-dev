<template>
  <div class="app-container">
    <conditional-filter
      excelTitle="Brand List"
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
        <el-form-item label="Brand Name:">
          <el-input
            v-model="listQuery.description"
            class="input-width"
            placeholder="Brand name"
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
          label="Brand Name"
          prop="brand_name"
          width="180"
          align="center"
          v-if="columns[1].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Send via"
          prop="smtp_provider"
          width="180"
          align="center"
          v-if="columns[2].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Sending limits"
          width="100"
          prop="choose_limit"
          align="center"
          v-if="columns[3].visible"
        ></el-table-column>
        <el-table-column
          sortable
          label="Used"
          width="100"
          prop="used"
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

    <brand-detail ref="brandDetail" :brandDetailDialogData="brandDetailDialogData"></brand-detail>
  </div>
</template>
<script>
import { getBrandList, deleteBrand } from '@/api/marketing/brand_module/brand'
import { formatDate } from '@/utils/date'
import BrandDetail from './components/brandDetail'

const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
}
export default {
  name: 'Api:marketing/brand_module/brand/list-index',
  components: {
    BrandDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `Brand Name`, visible: true },
        { key: 2, field: 'description', label: `Desc`, visible: true },
        { key: 3, field: 'created_at', label: `Created at`, visible: true },
        { key: 4, field: 'updated_at', label: `Updated at`, visible: true },
      ],
      brandDetailDialogData: {
        brandDetailDialogVisible: false,
        brandDetailTitle: '',
        isEdit: false,
        brandId: '',
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
    //get brand list
    getList() {
      getBrandList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    //Add brand operation
    handleAdd() {
      this.brandDetailDialogData.brandDetailDialogVisible = true
      this.brandDetailDialogData.brandDetailTitle = 'Add a brand'
      this.brandDetailDialogData.isEdit = false
      this.$refs['brandDetail'].getBrandInfo()
    },
    //Edit brand action
    handleEdit(index, row) {
      this.brandDetailDialogData.brandDetailDialogVisible = true
      this.brandDetailDialogData.brandDetailTitle = 'Edit "' + row.name + '" brand'
      this.brandDetailDialogData.isEdit = true
      this.brandDetailDialogData.brandId = row.id
      this.$refs['brandDetail'].getBrandInfo()
    },
    //Edit user function permissions
    handleViewBrand(row) {
      this.permissionDetailData.brandId = row.id
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
          deleteBrand(row.id).then(
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
