<template>
  <div class="app-container">
    <el-card class="filter-container" shadow="never"></el-card>
    <div class="table-container">
      <el-table
        ref="logTable"
        :data="list"
        style="width: 100%;"
        size="normal"
        border
        row-key="path"
        :tree-props="{children: 'children'}"
        @row-click="clickRowHandle"
      >
        <el-table-column label="Log path">
          <template slot-scope="scope">{{scope.row.path}}</template>
        </el-table-column>
        <el-table-column label="Action" width="200">
          <template slot-scope="scope">
            <div v-if="scope.row.type == 'file'">
              <el-button type="primary" size="mini" @click="handleViewDetail(scope.row)">Detail</el-button>
              <el-button type="danger" size="mini" @click="handleDelete(scope.row)">Delete</el-button>
            </div>
            <span v-else>-</span>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <log-detail ref="logDetail" :logDetailDialogData="logDetailDialogData"></log-detail>
  </div>
</template>

<script>
import { getLogPath, destroyLog } from '@/api/setting/log_module/systemLog'
import LogDetail from './components/logDetail'
const defaultListQuery = {}
export default {
  name: 'Api:setting/log_module/system_log/log_path-getLogPath',
  components: { LogDetail },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      logDetailDialogData: {
        logDetailDialogVisible: false,
        logDetailTitle: 'System log details',
        logDetailData: '',
        listQuery: {
          cur_page: 1,
          page_size: 10,
        },
      },
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      getLogPath(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
    handleViewDetail(row) {
      this.logDetailDialogData.logDetailDialogVisible = true
      this.logDetailDialogData.listQuery.file_path = row.path
      this.$refs['logDetail'].getLogContent()
    },
    handleDelete(row) {
      this.$confirm('Confirm the delete ','prompt', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          destroyLog({ path: encodeURIComponent(row.path) }).then(
            (response) => {
              if (response.code == 200) {
                this.getList()
              }
            }
          )
        })
        .catch((err) => {})
    },
    clickRowHandle(row) {
      this.$refs.logTable.toggleRowExpansion(row)
    },
  },
}
</script>

<style lang="scss" scoped>
.log-dialog ::v-deep .el-dialog {
  margin-top: 10vh !important;
}

.log-dialog ::v-deep .el-dialog__body::after {
  content: '';
  display: block;
  clear: both;
}
</style>