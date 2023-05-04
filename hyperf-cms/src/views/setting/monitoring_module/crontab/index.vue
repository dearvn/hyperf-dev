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
      excelTitle="Timing task"
    >
      <template slot="extraForm">
        <el-form-item label="Task name：">
          <el-input
            v-model="listQuery.name"
            class="input-width"
            placeholder="Task name："
            style="width:300px;"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Task name：">
          <el-input
            v-model="listQuery.task"
            class="input-width"
            placeholder="Task name："
            style="width:300px;"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
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
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-table
        ref="timedTaskTable"
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column label="ID" align="center" width="120" prop="id" v-if="columns[0].visible"></el-table-column>
        <el-table-column label="Mission name" align="center" prop="name" v-if="columns[1].visible"></el-table-column>
        <el-table-column label="Task name" prop="task" align="center" v-if="columns[2].visible"></el-table-column>
        <el-table-column
          label="crontab expression"
          prop="execute_time"
          align="center"
          v-if="columns[3].visible"
        ></el-table-column>
        <el-table-column
          label="The next execution time"
          prop="next_execute_time"
          align="center"
          v-if="columns[4].visible"
        ></el-table-column>
        <el-table-column label="Number of executions" align="center" prop="times" v-if="columns[5].visible"></el-table-column>
        <el-table-column label="Status" align="center" v-if="columns[6].visible">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              @change="changeStatus(scope.row)"
            ></el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Describe" prop="desc" align="center" v-if="columns[7].visible"></el-table-column>

        <el-table-column
          label="Created at"
          width="180"
          prop="created_at"
          align="center"
          v-if="columns[8].visible"
        ></el-table-column>
        <el-table-column label="Action" align="center" width="300">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-view"
              type="primary"
              size="mini"
              class="button-color-green"
              @click="handleViewTaskLog(scope.row)"
            >View log</el-button>
            <el-button
              icon="el-icon-edit"
              type="primary"
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
    </div>
    <div class="pagination-container">
      <Pagination
        v-show="total > 0"
        :total="total"
        :page.sync="listQuery.cur_page"
        :limit.sync="listQuery.page_size"
        @pagination="getList"
      ></Pagination>
    </div>
    <!--Add/modify scheduled tasks-->
    <timed-task-detail ref="timedTaskDetail" :timedTaskDetailDialogData="timedTaskDetailDialogData"></timed-task-detail>
    <!--Monitoring task logs-->
    <task-log ref="taskLog" :taskLogDrawerData="taskLogDrawerData"></task-log>
  </div>
</template>
<script>
import {
  timedTaskList,
  deleteTimedTask,
  changeStatus,
} from '@/api/setting/monitoring_module/timedTask'
import TimedTaskDetail from './components/detail'
import TaskLog from './components/taskLog'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  status: '',
  name: '',
  task: '',
}
export default {
  name: 'crontab',
  components: {
    TimedTaskDetail,
    TaskLog,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      columns: [
        { key: 0, field: 'id', label: `ID`, visible: true },
        { key: 1, field: 'name', label: `Mission name`, visible: true },
        { key: 2, field: 'task', label: `Task name`, visible: true },
        {
          key: 3,
          field: 'execute_time',
          label: `crontab expression`,
          visible: true,
        },
        {
          key: 4,
          field: 'next_execute_time',
          label: `The next execution time`,
          visible: true,
        },
        { key: 5, field: 'times', label: `Number of executions`, visible: true },
        { key: 6, field: 'status', label: `State`, visible: true },
        { key: 7, field: 'desc', label: `Describe`, visible: true },
        { key: 8, field: 'created_at', label: `Created at`, visible: true },
      ],
      multipleSelection: [],
      statusOptions: [],
      timedTaskDetailDialogData: {
        timedTaskDetailDialogVisible: false,
        statusOptions: [],
        timedTaskDetailTitle: '',
        isEdit: false,
        id: '',
      },
      taskLogDrawerData: {
        taskLogDrawerVisible: false,
        direction: 'rtl',
      },
    }
  },
  created() {
    this.getDicts('sys_timed_task_status').then((response) => {
      if (response.code == 200) {
        this.statusOptions = response.data.list
      }
    })
    this.getList()
  },
  filters: {},
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleAdd() {
      this.timedTaskDetailDialogData.timedTaskDetailDialogVisible = true
      this.timedTaskDetailDialogData.statusOptions = this.statusOptions
      this.timedTaskDetailDialogData.timedTaskDetailTitle = 'Add timing task'
      this.timedTaskDetailDialogData.isEdit = false
      this.$refs['timedTaskDetail'].getTimedTaskInfo()
    },
    handleEdit(row) {
      this.timedTaskDetailDialogData.timedTaskDetailDialogVisible = true
      this.timedTaskDetailDialogData.statusOptions = this.statusOptions
      this.timedTaskDetailDialogData.timedTaskDetailTitle =
        'Edit "' + row.title + '" Timing task'
      this.timedTaskDetailDialogData.isEdit = true
      this.timedTaskDetailDialogData.id = row.id
      this.$refs['timedTaskDetail'].getTimedTaskInfo()
    },
    handleDelete(row) {
      this.deleteTimedTask(row.id)
    },
    handleBatchDelete() {
      let id_arr = []
      for (let i = 0; i < this.multipleSelection.length; i++) {
        id_arr.push(this.multipleSelection[i].id)
      }
      this.deleteTimedTask(id_arr, true)
    },
    handleViewTaskLog(row) {
      this.taskLogDrawerData.taskLogDrawerVisible = true
    },
    getList() {
      timedTaskList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.total = response.data.total
          this.list = response.data.list
        }
      })
    },
    deleteTimedTask(id, isBatch = false) {
      this.$confirm('Do you want to do this delete?', 'Alert', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        if (isBatch) {
          deleteTimedTask(0, { id: id }).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        } else {
          deleteTimedTask(id).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        }
      })
    },
    changeStatus(row) {
      let text = row.status === 0 ? 'Disable': 'Enable'
      this.$confirm(
        'Confirm"' + text + '""' + row.name + '"Do you monitor the task?',
        'warn',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(function () {
          return changeStatus(row.id, {
            status: row.status,
          })
          this.getList()
        })
        .catch(function () {
          row.status = row.status === 0 ? 1 : 0
        })
    },
    //Status dictionary translation
    statusFormat(row, column) {
      return this.selectDictLabel(this.statusOptions, row.status)
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
