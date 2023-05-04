<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :columns.sync="columns"
      :list="list"
      :multipleSelection="multipleSelection"
      @getList="getList"
      @handleAdd="handleAdd"
      @handleBatchDelete="handleBatchDelete"
      excelTitle="System suggestion"
    >
      <template slot="extraForm">
        <el-form-item label="Status">
          <el-select v-model="listQuery.status" clearable class="input-width" placeholder="Status：">
            <el-option
              v-for="dict in statusOptions"
              :key="dict.dict_value"
              :label="dict.dict_label"
              :value="dict.dict_value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Type selection">
          <el-select v-model="listQuery.type" clearable class="input-width" placeholder="Type selection">
            <el-option
              v-for="dict in typeOptions"
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
        ref="adviceTable"
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column v-if="columns[0].visible" label="ID" align="center" width="120" prop="id"></el-table-column>
        <el-table-column v-if="columns[1].visible" label="Title" prop="title"></el-table-column>
        <el-table-column
          v-if="columns[2].visible"
          label="Announcer"
          width="140"
          align="center"
          prop="get_user_name.desc"
        ></el-table-column>
        <el-table-column
          v-if="columns[3].visible"
          label="Status"
          align="center"
          prop="status"
          width="140"
          :formatter="statusFormat"
        ></el-table-column>
        <el-table-column
          v-if="columns[4].visible"
          label="Category"
          align="center"
          prop="type"
          width="140"
          :formatter="typeFormat"
        ></el-table-column>
        <el-table-column v-if="columns[5].visible" label="Created at" width="180" prop="created_at"></el-table-column>
        <el-table-column label="Action" align="center" width="400">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-view"
              type="primary"
              size="mini"
              @click="handleViewAdvice(scope.row)"
            >Check</el-button>
            <el-button
              icon="el-icon-edit"
              type="primary"
              size="mini"
              @click="handleEdit(scope.row)"
            >Edit</el-button>
            <el-button
              icon="el-icon-finished"
              type="primary"
              size="mini"
              @click="handleReplyAdvice(scope.row)"
            >Reply</el-button>
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

    <!--Add/modify system suggestions-->
    <advice-detail ref="adviceDetail" :adviceDetailDialogData="adviceDetailDialogData"></advice-detail>

    <!--view content-->
    <advice-show ref="adviceShow" :adviceShowDialogData="adviceShowDialogData"></advice-show>

    <!--reply content-->
    <advice-reply ref="adviceReply" :adviceReplyDialogData="adviceReplyDialogData"></advice-reply>
  </div>
</template>
<script>
import { adviceList, deleteAdvice } from '@/api/setting/system_module/advice'
import AdviceDetail from './components/adviceDetail'
import AdviceShow from './components/adviceShow'
import AdviceReply from './components/adviceReply'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  status: '',
  type: '',
}
export default {
  name: 'Api:setting/system_module/advice/list-index',
  components: {
    AdviceDetail,
    AdviceShow,
    AdviceReply,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'title', label: `Title`, visible: true },
        { key: 2, field: 'get_user_name.desc', label: `Announcer`, visible: true },
        { key: 3, field: 'status', label: `State`, visible: true },
        { key: 4, field: 'type', label: `Category`, visible: true },
        { key: 5, field: 'created_at', label: `Created at`, visible: true },
      ],
      multipleSelection: [],
      statusOptions: [],
      typeOptions: [],
      adviceDetailDialogData: {
        adviceDetailDialogVisible: false,
        typeOptions: [],
        adviceDetailTitle: '',
        isEdit: false,
        id: '',
      },
      adviceShowDialogData: {
        adviceShowDialogVisible: false,
        adviceShowData: [],
      },
      adviceReplyDialogData: {
        adviceReplyDialogVisible: false,
        adviceReplyTitle: '',
        id: '',
        statusOptions: [],
      },
    }
  },
  created() {
    this.getDicts('sys_advice_status').then((response) => {
      if (response.code == 200) this.statusOptions = response.data.list
    })
    this.getDicts('sys_advice_type').then((response) => {
      if (response.code == 200) this.typeOptions = response.data.list
    })
    this.getList()
  },
  filters: {},
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleViewAdvice(row) {
      this.adviceShowDialogData.adviceShowData = row
      this.adviceShowDialogData.adviceShowDialogVisible = true
    },
    handleReplyAdvice(row) {
      this.adviceReplyDialogData.id = row.id
      this.adviceReplyDialogData.statusOptions = this.statusOptions
      this.adviceReplyDialogData.adviceReplyTitle =
        '回复   ' + '"' + row.title + '"'
      this.adviceReplyDialogData.adviceReplyDialogVisible = true
      this.$refs['adviceReply'].getAdviceInfo()
    },
    handleAdd() {
      this.adviceDetailDialogData.adviceDetailDialogVisible = true
      this.adviceDetailDialogData.typeOptions = this.typeOptions
      this.adviceDetailDialogData.adviceDetailTitle = 'Add system suggestion'
      this.adviceDetailDialogData.isEdit = false
      this.$refs['adviceDetail'].getAdviceInfo()
    },
    handleEdit(row) {
      this.adviceDetailDialogData.adviceDetailDialogVisible = true
      this.adviceDetailDialogData.typeOptions = this.typeOptions
      this.adviceDetailDialogData.adviceDetailTitle =
        'Revise "' + row.title + '" System suggestion'
      this.adviceDetailDialogData.isEdit = true
      this.adviceDetailDialogData.id = row.id
      this.$refs['adviceDetail'].getAdviceInfo()
    },
    handleDelete(row) {
      this.deleteAdvice(row.id)
    },
    handleBatchDelete() {
      let id_arr = []
      for (let i = 0; i < this.multipleSelection.length; i++) {
        id_arr.push(this.multipleSelection[i].id)
      }
      this.deleteAdvice(id_arr, true)
    },
    getList() {
      adviceList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.total = response.data.total
          this.list = response.data.list
        }
      })
    },

    deleteAdvice(id, isBatch = false) {
      this.$confirm('Do you want to do this delete?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        if (isBatch) {
          deleteAdvice(0, { id: id }).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        } else {
          deleteAdvice(id).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        }
      })
    },
    //Status dictionary translation
    statusFormat(row, column) {
      return this.selectDictLabel(this.statusOptions, row.status)
    },
    //type dictionary translation
    typeFormat(row, column) {
      return this.selectDictLabel(this.typeOptions, row.type)
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
