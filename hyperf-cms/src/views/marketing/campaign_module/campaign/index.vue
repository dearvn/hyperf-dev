<template>
  <div class="app-container">
    <conditional-filter
      excelTitle="Campaign List"
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
        <el-form-item label="Campaign Name:">
          <el-input
            v-model="listQuery.description"
            class="input-width"
            placeholder="Campaign name"
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
          label="Campaign Name"
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

    <campaign-detail ref="campaignDetail" :campaignDetailDialogData="campaignDetailDialogData"></campaign-detail>
  </div>
</template>
<script>
import { getCampaignList, deleteCampaign } from '@/api/marketing/campaign_module/campaign'
import { formatDate } from '@/utils/date'
import CampaignDetail from './components/campaignDetail'

const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
}
export default {
  name: 'Api:marketing/campaign_module/campaign/list-index',
  components: {
    CampaignDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `Campaign Name`, visible: true },
        { key: 2, field: 'description', label: `Desc`, visible: true },
        { key: 3, field: 'created_at', label: `Created at`, visible: true },
        { key: 4, field: 'updated_at', label: `Updated at`, visible: true },
      ],
      campaignDetailDialogData: {
        campaignDetailDialogVisible: false,
        campaignDetailTitle: '',
        isEdit: false,
        campaignId: '',
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
    //get campaign list
    getList() {
      getCampaignList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    //Add campaign operation
    handleAdd() {
      this.campaignDetailDialogData.campaignDetailDialogVisible = true
      this.campaignDetailDialogData.campaignDetailTitle = 'Add a campaign'
      this.campaignDetailDialogData.isEdit = false
      this.$refs['campaignDetail'].getCampaignInfo()
    },
    //Edit campaign action
    handleEdit(index, row) {
      this.campaignDetailDialogData.campaignDetailDialogVisible = true
      this.campaignDetailDialogData.campaignDetailTitle = 'Edit "' + row.name + '" campaign'
      this.campaignDetailDialogData.isEdit = true
      this.campaignDetailDialogData.campaignId = row.id
      this.$refs['campaignDetail'].getCampaignInfo()
    },
    //Edit user function permissions
    handleViewCampaign(row) {
      this.permissionDetailData.campaignId = row.id
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
          deleteCampaign(row.id).then(
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
