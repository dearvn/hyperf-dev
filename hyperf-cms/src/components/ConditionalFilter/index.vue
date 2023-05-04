<template>
  <div>
    <el-card class="filter-container" shadow="never" v-if="showSearch">
      <div>
        <i class="el-icon-search"></i>
        <span>Screen for search</span>
        <el-button style="float:right" type="primary" @click="handleSearchList()" size="small">Search</el-button>
        <el-button
          style="float:right;margin-right: 15px"
          @click="handleResetSearch()"
          size="small"
        >Reset</el-button>
      </div>
      <div style="margin-top: 30px">
        <el-form :inline="true" :model="listQuery" size="small">
          <slot name="extraForm"></slot>
        </el-form>
      </div>
    </el-card>
    <div class="operate-container" style="height:30px">
      <el-button
        style="float: left;"
        icon="el-icon-plus"
        type="primary"
        size="small"
        plain
        v-if="addButton"
        @click="handleAdd"
      >Add</el-button>
      <el-button
        style="float: left;"
        icon="el-icon-delete"
        type="danger"
        size="small"
        v-if="batchDelete"
        :disabled="multipleSelection.length == 0"
        plain
        @click="handleBatchDelete"
      >Delete</el-button>
      <el-button
        style="float: left;"
        icon="el-icon-download"
        type="warning"
        size="small"
        plain
        v-if="exportExcel"
        @click="handleExportExcel"
      >Export Excel</el-button>
      <el-button
        style="float: left;"
        icon="el-icon-copy-document"
        type="success"
        size="small"
        plain
        v-if="copyExcel"
        @click="handleCopyExcel"
      >Copy Excel</el-button>
      <span class="excel_copy" ref="copy" :data-clipboard-text="excelContent" @click="copy"></span>
      <slot name="extraButton"></slot>
      <el-popover
        placement="bottom"
        trigger="click"
        style="float:right;margin-right:10px;"
        v-if="tableToolTip"
      >
        <el-checkbox-group v-model="checkedColumns">
          <el-checkbox
            v-for="item in columns"
            :label="item.label"
            :key="item.key"
            @change="handleCheckedColumnsChange(item.label, $event)"
          >{{item.label}}</el-checkbox>
        </el-checkbox-group>
        <el-button
          slot="reference"
          style="float: right;"
          icon="el-icon-menu"
          size="small"
          type="primary"
          circle
          plain
        ></el-button>
      </el-popover>
      <el-tooltip
        class="item"
        effect="dark"
        :content="showSearch ? 'Hidden search' : 'Display search'"
        placement="top"
      >
        <el-button
          style="float: right;margin-right:10px"
          circle
          icon="el-icon-search"
          size="small"
          type="primary"
          :plain="showSearch ? false : true"
          @click="toggleSearch"
        ></el-button>
      </el-tooltip>
      <div style="height: 0; overflow: hidden;" v-html="excelContent"></div>
    </div>
  </div>
</template>
<script>
import { formatDate, getDefaultTime } from '@/utils/date'
import { dateSelection } from '@/mixins/dateSelection'
import { setStore, getStore, removeStore } from '@/utils/store'
import { getExcelContent } from '@/api/common/excel'
import Clipboard from 'clipboard'
import tableExport from '@/assets/js/exportExcel/tableExport.min.js'

export default {
  name: 'conditionalFilter',
  props: {
    //default condition
    defaultListQuery: {
      type: Object,
      default: {},
    },
    //search condition
    listQuery: {
      type: Object,
      default: {},
    },
    columns: {
      type: Object,
      default: {},
    },
    list: {
      type: String,
      default: '',
    },
    excelTitle: {
      type: String,
      default: '',
    },
    multipleSelection: {
      type: Array,
      default: [],
    },
    batchDelete: {
      type: Boolean,
      default: true,
    },
    addButton: {
      type: Boolean,
      default: true,
    },
    exportExcel: {
      type: Boolean,
      default: true,
    },
    copyExcel: {
      type: Boolean,
      default: true,
    },
    tableToolTip: {
      type: Boolean,
      default: true,
    },
  },
  mixins: [dateSelection],
  data() {
    return {
      lists: [],
      checkedColumns: [],
      showSearch: true,
    }
  },
  watch: {
    listQuery: {
      deep: true,
      handler: function (val) {
        let route = this.$route.name
        let data = getStore({ name: 'query_selection' })
        if (data == undefined) data = {}
        this.setStorageValue(route, data)
        return val
      },
    },
  },
  created() {
    //Listen for carriage return events
    this.enterSearch()
    if (this.columns.length && this.columns.length > 0) {
      this.checkedColumns = this.columns.map((o) => {
        return [o.label].toString()
      })
    }

    if (
      Object.keys(this.$route.params).length == 0 &&
      Object.keys(this.$route.query).length == 0
    ) {
      //Read the specified filter item from the cache
      let route = this.$route.name
      let data = getStore({ name: 'query_selection' })
      if (data == undefined) data = {}

      //Get the filter cache of the current route from query_selection
      let queryData = data[route]
      let expiredAt = queryData == undefined ? 0 : queryData['storageExpiredAt']
      let now = new Date().getTime()

      if (queryData == undefined || now > expiredAt) {
        this.setStorageValue(route, data)
      } else {
        for (let i in queryData) {
          if (i == 'storageExpiredAt') continue
          this.$set(this.listQuery, i, queryData[i])
        }
      }
    }
  },
  methods: {
    /**
    *Set the filter item to the cache
    */
    setStorageValue(route, data) {
      let queryData = {}
      let date = new Date()
      queryData.storageExpiredAt = date.getTime() + 3600 * 1000

      for (let i in this.listQuery) {
        this.$set(queryData, i, this.listQuery[i])
      }

      data[route] = queryData
      setStore({ name: 'query_selection', content: data })
    },
    /**
     * Resetting screen
     */
    handleResetSearch() {
      this.listQuery = Object.assign({}, this.defaultListQuery)
      this.$emit('update:listQuery', this.listQuery) //Synchronously update the list query of the parent component
      this.getList()
    },
    /**
     * Inquire
     */
    handleSearchList() {
      this.getList()
    },
    /**
     * Get the query list
     */
    getList() {
      this.$emit('update:listQuery', this.listQuery) //Synchronously update the list query of the parent component
      this.$emit('getList') // 触发查询事件
    },
    /**
     * 监听回车事件
     */
    enterSearch() {
      document.onkeydown = (e) => {
        //13 means the Enter key, and the base uri is the address of the current page. In order to be more rigorous, you can also add other things, you can print e to see
        if (e.keyCode === 13 && e.target.baseURI.match(/freshmanage/)) {
          //Execute the search method after carriage return
        }
      }
    },
    /**
     * Show/hidden search
     */
    toggleSearch() {
      this.showSearch = !this.showSearch
    },
    /**
     * Show/hidden
     */
    handleCheckedColumnsChange(label, $event) {
      for (let i = 0; i < this.columns.length; i++) {
        if (label == this.columns[i].label) {
          this.columns[i].visible = $event
        }
      }
      this.$emit('update:columns', this.columns) //Synchronously update the list query of the parent component

      let checkedCount = this.columns.length
      this.checkAll = checkedCount === this.checkedColumns.length
      this.isIndeterminate =
        checkedCount > 0 && checkedCount > this.checkedColumns.length
    },
    /**
     * 导出Excel
     */
    handleExportExcel() {
      this.$confirm('Do you confirm the export data?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          let table_header = []
          let table_header_mean = {}
          for (let i = 0; i < this.columns.length; i++) {
            if (this.columns[i].field != '') {
              table_header.push(this.columns[i].field)
              table_header_mean[this.columns[i].field] = this.columns[i].label
            }
          }
          getExcelContent({
            data: this.list,
            table_header: table_header,
            table_header_mean: table_header_mean,
          }).then((response) => {
            this.excelContent = response.data.excel_content
            //compatible with ios
            this.$forceUpdate()
            setTimeout(() => {
              //Simulate clicking the button that actually copies the link
              $('#tables').tableExport({
                type: 'excel',
                escape: 'true',
                fileName: this.excelTitle,
              })
            }, 10)
          })
        })
        .catch((err) => {})
    },
    /**
     * Copy Excel table data
     */
    handleCopyExcel() {
      let table_header = []
      let table_header_mean = {}
      for (let i = 0; i < this.columns.length; i++) {
        if (this.columns[i].field != '') {
          table_header.push(this.columns[i].field)
          table_header_mean[this.columns[i].field] = this.columns[i].label
        }
      }
      getExcelContent({
        data: this.list,
        table_header: table_header,
        table_header_mean: table_header_mean,
      }).then((response) => {
        this.excelContent = response.data.excel_content
        //compatible with ios
        this.$forceUpdate()
        setTimeout(() => {
          //Simulate clicking the button that actually copies the link
          this.$refs.copy.click()
        }, 10)
      })
    },
    /**
     * copy
     */
    copy(event) {
      event.preventDefault()
      var clipboard = new Clipboard('.excel_copy')
      clipboard.on('success', (e) => {
        this.msgSuccess('Copy the excel form successfully')
        //  Release memory
        clipboard.destroy()
      })
      clipboard.on('error', (e) => {
        //Copy not supported
        this.msgError('该浏览器不支持复制')
        //free memory
        clipboard.destroy()
      })
    },
    /**
     * Add event
     */
    handleAdd() {
      this.$emit('handleAdd') //trigger add
    },
    /**
     * 删除事件
     */
    handleBatchDelete() {
      this.$emit('handleBatchDelete') //trigger deletion
    },
  },
}
</script>
