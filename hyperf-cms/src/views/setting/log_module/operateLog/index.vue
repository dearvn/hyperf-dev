<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :columns.sync="columns"
      :list="list"
      :multipleSelection="multipleSelection"
      @getList="getList"
      :batchDelete="false"
      :addButton="false"
      excelTitle="Operating log"
    >
      <template slot="extraForm">
        <el-form-item label="Operating behavior：">
          <el-input
            v-model="listQuery.action"
            class="input-width"
            placeholder="Please fill in the operation behavior："
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Operator">
          <el-input
            v-model="listQuery.operator"
            class="input-width"
            placeholder="Please fill in the operator"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Status：">
          <el-select v-model="listQuery.status" clearable class="input-width" placeholder="Status：">
            <el-option value="1" label="Success"></el-option>
            <el-option value="0" label="Abnormal"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Time screening：">
          <el-date-picker
            v-model="listQuery.created_at"
            type="daterange"
            :picker-options="pickerOptions"
            range-separator="to"
            start-placeholder="Start date"
            end-placeholder="Ending date"
            align="right"
            value-format="yyyy-MM-dd HH:mm:ss"
            :default-time="['00:00:00', '23:59:59']"
          ></el-date-picker>
        </el-form-item>
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-table ref="dictTypeTable" :data="list" style="width: 100%;" size="mini" border>
        <el-table-column
          v-if="columns[0].visible"
          label="Log Id"
          width="100"
          align="center"
          prop="id"
        ></el-table-column>
        <el-table-column
          v-if="columns[1].visible"
          label="Username"
          width="150"
          align="center"
          prop="username"
        ></el-table-column>
        <el-table-column
          v-if="columns[2].visible"
          label="Nickname"
          width="180"
          align="center"
          prop="operator"
        ></el-table-column>
        <el-table-column
          v-if="columns[3].visible"
          label="Operating behavior"
          width="220"
          align="center"
          prop="action"
        ></el-table-column>
        <el-table-column
          v-if="columns[4].visible"
          label="Request parameters"
          prop="data"
          align="center"
          :show-overflow-tooltip="true"
          width="500"
        >
          <template slot-scope="scope">
            <span @click="copy(scope.row)" class="request_param">{{scope.row.data}}</span>
          </template>
        </el-table-column>
        <el-table-column
          v-if="columns[5].visible"
          label="Response status code"
          prop="response_code"
          align="center"
          width="120"
        ></el-table-column>
        <el-table-column
          v-if="columns[6].visible"
          label="Response result"
          prop="response_result"
          align="center"
          :show-overflow-tooltip="true"
        ></el-table-column>
        <el-table-column
          v-if="columns[7].visible"
          label="Operation time"
          prop="created_at"
          align="center"
          width="180"
        ></el-table-column>
        <el-table-column label="Action" align="center" width="150">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-view"
              type="primary"
              size="mini"
              @click="handleViewDetail(scope.$index, scope.row)"
            >Detail</el-button>
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

    <!-- 日志详情 -->
    <log-detail ref="logDetail" :logDetailDialogData="logDetailDialogData"></log-detail>
  </div>
</template>
<script>
import { operateLogList } from '@/api/setting/log_module/operateLog'
import Clipboard from 'clipboard'
import LogDetail from './components/logDetail'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  action: '',
  operator: '',
  status: '',
  created_at: '',
}
export default {
  name: 'Api:setting/system_module/operate_log/list-index',
  components: {
    LogDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      columns: [
        { key: 0, field: 'id', label: `Log Id`, visible: true },
        { key: 1, field: 'username', label: `Username`, visible: true },
        { key: 2, field: 'operator', label: `Nickname`, visible: true },
        { key: 3, field: 'action', label: `Operating behavior`, visible: true },
        { key: 4, field: 'data', label: `Request parameters`, visible: true },
        { key: 5, field: 'response_code', label: `Response status code`, visible: true },
        { key: 6, field: 'response_result', label: `Response result`, visible: true },
        { key: 7, field: 'created_at', label: `Operation time`, visible: true },
      ],
      multipleSelection: [],
      logDetailDialogData: {
        logDetailDialogVisible: false,
        logDetailTitle: 'Details of operation logs',
        logDetailData: '',
      },
    }
  },
  created() {
    this.getList()
  },
  filters: {},
  methods: {
    getList() {
      operateLogList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.total = response.data.total
          this.list = response.data.list
        }
      })
    },
    handleViewDetail(index, row) {
      this.logDetailDialogData.logDetailData = row
      this.logDetailDialogData.logDetailDialogVisible = true
    },
    copy(row) {
      let clipboard = new Clipboard('.request_param', {
        text: function () {
          return row.data
        },
      })
      clipboard.on('success', (e) => {
        this.$message({ message: 'Replication', showClose: true, type: 'success' })
        //free memory
        clipboard.destroy()
      })
      clipboard.on('error', (e) => {
        this.$message({ message: 'Copy failure,', showClose: true, type: 'error' })
        clipboard.destroy()
      })
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
