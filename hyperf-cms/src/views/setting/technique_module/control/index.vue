<template>
  <div class="app-container">
    <el-card class="box-card">
      <el-card shadow="never" style="margin: 0 auto;width: 50%;">
        <div slot="header" class="clearfix" style="font-weight: bold;">Switch</div>
        <el-form :model="menuConfigData" ref="menuConfigForm" label-width="150px">
          <el-form-item label="Maintenance mode：">
            <el-switch
              v-model="menuConfigData.maintain_switch"
              :active-value="true"
              :inactive-value="false"
              active-color="#13ce66"
              inactive-color="#ff4949"
              @change="changeSwitch('maintain_switch', menuConfigData.maintain_switch)"
            ></el-switch>
            <span style="color: #999;">（Disable the ownership of all users except the super administrator after opening)</span>
          </el-form-item>
          <el-form-item label="Simple maintenance mode：">
            <el-switch
              v-model="menuConfigData.simple_maintain_switch"
              :active-value="true"
              :inactive-value="false"
              active-color="#13ce66"
              inactive-color="#ff4949"
              @change="changeSwitch('simple_maintain_switch', menuConfigData.simple_maintain_switch)"
            ></el-switch>
            <span style="color: #999;">(After opening, disable all users except the super administrator to add, deletion, change permissions)</span>
          </el-form-item>
          <el-form-item label="Backstage registration entrance：">
            <el-switch
              v-model="menuConfigData.register_switch"
              :active-value="true"
              :inactive-value="false"
              active-color="#13ce66"
              inactive-color="#ff4949"
              @change="changeSwitch('register_switch', menuConfigData.register_switch)"
            ></el-switch>
            <span style="color: #999;">（Whether to open the registered entrance to the background list))</span>
          </el-form-item>
        </el-form>
      </el-card>
      <el-card shadow="never" style="margin: 20px auto 0;width: 50%;">
        <div slot="header" class="clearfix" style="font-weight: bold;">operate</div>
        <el-form ref="buttonForm" label-width="150px">
          <el-form-item label="Cleanings Excel file：">
            <el-button type="danger" plain @click="handleClearExcel()" size="small">Implement</el-button>
            <span style="color: #999;">(Export files of cleaning/runtime/excel/</span>
          </el-form-item>
          <el-form-item label="Clean up SQL file:">
            <el-button type="danger" plain @click="handleClearSql()" size="small">Implement</el-button>
            <span style="color: #999;">（Export files of cleaning/runtime/sql/</span>
          </el-form-item>
          <el-form-item label="Clean up the TEMP file：">
            <el-button type="danger" plain @click="handleClearSql()" size="small">Implement</el-button>
            <span style="color: #999;">（Cleaning/runtime/test file)</span>
          </el-form-item>
          <el-form-item label="Backup log：">
            <el-button type="primary" plain @click="handleBackupLog()" size="small">Implement</el-button>
            <el-button type="info" plain @click="handleCheckBackupLog()" size="small">View backup list</el-button>
          </el-form-item>
          <el-form-item label="Clean up log file：">
            <el-button type="danger" plain @click="handleClearLog()" size="small">Implement</el-button>
            <span style="color: #999;">(Clean the export file in the log system)</span>
          </el-form-item>
        </el-form>
      </el-card>
    </el-card>
    <!-- Log backup list -->
    <el-dialog
      title="logBackupList"
      :visible.sync="backupListDialog"
      width="60%"
      :close-on-click-modal="false"
    >
      <el-button
        type="danger"
        size="mini"
        @click="handleClearBackupLog"
        style="margin-bottom: 15px;"
      >Clear all files</el-button>
      <el-table ref="backupLogTable" size="mini" :data="backupList" style="width: 100%;" border>
        <el-table-column label="File name">
          <template slot-scope="scope">{{scope.row.name}}</template>
        </el-table-column>
        <el-table-column label="Action" width="160">
          <template slot-scope="scope">
            <el-button type="primary" size="mini" @click="handleDownloadBackupLog(scope.row.url)">Download</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import {
  getControlList,
  changeControl,
} from '@/api/setting/system_module/control'
const defaultMenuConfigData = {
  maintain_switch: null,
  simple_maintain_switch: null,
  register_switch: null,
}
export default {
  data() {
    return {
      menuConfigData: Object.assign({}, defaultMenuConfigData),
      menuConfigList: [],
      backupListDialog: false,
      backupList: [],
    }
  },
  created() {
    this.getConfigList()
  },
  methods: {
    confirm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$confirm('Do you want to submit a modification?', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            updateOrderSetting(1, this.orderSetting).then((response) => {
              if (response.code == 200) {
                this.$message({
                  type: 'success',
                  message: 'Submitted successfully!',
                  duration: 1000,
                })
              }
            })
          })
        } else {
          this.$message({
            message: 'Submit parameters invalid',
            type: 'warning',
          })
          return false
        }
      })
    },
    getConfigList() {
      getControlList().then((response) => {
        if (response.code == 200) {
          this.$nextTick(() => {
            this.menuConfigData = response.data.list
          })
        }
      })
    },
    changeSwitch(name, status) {
      // Switch to switch events
      this.$confirm('Do you confirm the execution of this operation?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          changeControl({ key: name, value: status })
            .then((response) => {
              if (response.code != 200) {
                this.menuConfigData[name] = !status
              }
            })
            .catch((err) => {
              this.menuConfigData[name] = !status
            })
        })
        .catch((err) => {
          this.menuConfigData[name] = !status
        })
    },
    handleClearExcel() {
      // Cleanings Excel file
      this.$confirm('Do you confirm the cleaning Excel file?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // clearExcel()
        })
        .catch((err) => {})
    },
    handleClearSql() {
      // Clean up SQL file
      this.$confirm('Do you confirm the cleaning SQL file?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // clearSql()
        })
        .catch((err) => {})
    },
    handleConfigRebulid() {
      this.$confirm('Do you want to generate configuration?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // configCreate({ type: 'rebuild' }).then((response) => {})
        })
        .catch((err) => {})
    },
    handleBackupLog() {
      this.$confirm('Do you back up all the logs?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // backupLog().then((response) => {})
        })
        .catch((err) => {})
    },
    handleCheckBackupLog() {
      this.backupListDialog = true
      getBackupLog().then((response) => {
        if (response.code == 200) {
          this.backupList = response.data.list
        }
      })
    },
    handleClearBackupLog() {
      this.$confirm('Do you confirm the Log Log Backup file?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // clearBackupLog().then((response) => {
          //   if (response.errorCode == 200) {
          //     this.backupListDialog = false
          //   }
          // })
        })
        .catch((err) => {})
    },
    handleClearLog() {
      this.$confirm('Do you confirm the files in the log system?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.msgError('This function is not open')
          // clearLog()
        })
        .catch((err) => {})
    },
    handleDownloadBackupLog(url) {
      window.open(url, '_blank')
    },
  },
}
</script>