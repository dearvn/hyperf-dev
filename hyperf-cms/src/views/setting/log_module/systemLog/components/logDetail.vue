<template>
  <el-dialog
    class="log-dialog"
    title="Log content"
    :visible.sync="logDetailDialogData.logDetailDialogVisible"
    :close-on-click-modal="false"
    width="80%"
  >
    <el-table ref="logContentTable" :data="list" size="normal" border height="650">
      <el-table-column label="Time" prop="datetime" width="160"></el-table-column>
      <el-table-column label="Environment" prop="env" width="100"></el-table-column>
      <el-table-column label="Error level" prop="level" width="100"></el-table-column>
      <el-table-column label="Content" prop="message"></el-table-column>
    </el-table>
    <div class="pagination-container">
      <Pagination
        v-show="total>0"
        :total="total"
        :page.sync="logDetailDialogData.listQuery.cur_page"
        :limit.sync="logDetailDialogData.listQuery.page_size"
        @pagination="getLogContent"
      ></Pagination>
    </div>
  </el-dialog>
</template>
<script>
import { getLogContent } from '@/api/setting/log_module/systemLog'
export default {
  name: 'LogDetail',
  props: {
    logDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      list: [],
      total: 0,
    }
  },
  methods: {
    getLogContent() {
      getLogContent(this.logDetailDialogData.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
        }
      })
    },
  },
}
</script>
</script>