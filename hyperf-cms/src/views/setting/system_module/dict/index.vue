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
      excelTitle="Dictionary list"
    >
      <template slot="extraForm">
        <el-form-item label="Dictionary name：">
          <el-input
            v-model="listQuery.dict_name"
            class="input-width"
            placeholder="Please enter the dictionary name："
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Dictionary">
          <el-input
            v-model="listQuery.dict_type"
            class="input-width"
            placeholder="Please enter the dictionary label"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Status：">
          <el-select v-model="listQuery.status" clearable class="input-width" placeholder="Status：">
            <el-option value="1" label="Normal"></el-option>
            <el-option value="0" label="Stop"></el-option>
          </el-select>
        </el-form-item>
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-table
        ref="dictTypeTable"
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column label="Dictionary number" align="center" prop="dict_id" v-if="columns[0].visible"></el-table-column>
        <el-table-column label="Dictionary name" prop="dict_name" align="center" v-if="columns[1].visible"></el-table-column>
        <el-table-column label="Dictionary" align="center" v-if="columns[2].visible">
          <template slot-scope="scope">
            <router-link
              :to="'/setting/system_module/dict_data/list/' + scope.row.dict_id"
              class="link-type"
            >
              <span>{{ scope.row.dict_type }}</span>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column label="Status" width="140" align="center" v-if="columns[3].visible">
          <template slot-scope="scope">{{scope.row.status | status}}</template>
        </el-table-column>
        <el-table-column label="Remark" align="center" prop="remark" v-if="columns[4].visible"></el-table-column>
        <el-table-column
          label="Createt at"
          width="180"
          prop="created_at"
          align="center"
          v-if="columns[5].visible"
        ></el-table-column>
        <el-table-column label="Action" align="center" width="250">
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
              @click="handleDelete(scope.$index, scope.row)"
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

    <!-- Add/modify the dictionary type -->
    <dictionary-detail
      ref="dictionaryDetail"
      :dictionaryDetailDialogData="dictionaryDetailDialogData"
    ></dictionary-detail>
  </div>
</template>
<script>
import {
  dictTypeList,
  deleteDictType,
} from '@/api/setting/system_module/dictType'
import { formatDate } from '@/utils/date'
import DictionaryDetail from './components/dictionaryDetail'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  dict_name: '',
  dict_type: '',
  status: '',
}
export default {
  name: 'Api:setting/system_module/dict_type/list-index',
  components: {
    DictionaryDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      columns: [
        { key: 0, field: 'dict_id', label: `Dictionary number`, visible: true },
        { key: 1, field: 'dict_name', label: `Dictionary name`, visible: true },
        { key: 2, field: 'dict_type', label: `Dictionary`, visible: true },
        { key: 3, field: 'status', label: `Status`, visible: true },
        { key: 4, field: 'remark', label: `Remark`, visible: true },
        { key: 5, field: 'created_at', label: `Created at`, visible: true },
      ],
      dictionaryDetailDialogData: {
        dictionaryDetailDialogVisible: false,
        dictionaryDetailTitle: '',
        isEdit: false,
        dict_id: '',
      },
    }
  },
  created() {
    this.getList()
  },
  filters: {
    formatLoginTime(time) {
      let date = new Date(time * 1000)
      return formatDate(date, 'yyyy-MM-dd hh:mm:ss')
    },
    status(status) {
      if (status == 0) return 'Disable'
      if (status == 1) return 'Enable'
    },
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleAdd() {
      this.dictionaryDetailDialogData.dictionaryDetailDialogVisible = true
      this.dictionaryDetailDialogData.dictionaryDetailTitle = 'Add dictionary type'
      this.dictionaryDetailDialogData.isEdit = false
      this.$refs['dictionaryDetail'].getDictTypeInfo()
    },
    handleEdit(index, row) {
      this.dictionaryDetailDialogData.dictionaryDetailDialogVisible = true
      this.dictionaryDetailDialogData.dictionaryDetailTitle =
        'Edit "' + row.dict_name + '" Dictionary'
      this.dictionaryDetailDialogData.isEdit = true
      this.dictionaryDetailDialogData.dict_id = row.dict_id
      this.$refs['dictionaryDetail'].getDictTypeInfo()
    },
    handleDelete(index, row) {
      this.deleteDictType(row.dict_id)
    },
    handleBatchDelete() {
      let id_arr = []
      for (let i = 0; i < this.multipleSelection.length; i++) {
        id_arr.push(this.multipleSelection[i].dict_id)
      }
      this.deleteDictType(id_arr, true)
    },
    getList() {
      dictTypeList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.total = response.data.total
          this.list = response.data.list
        }
      })
    },
    deleteDictType(id, isBatch = false) {
      this.$confirm('Do you want to do this delete operation?', 'Alert', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        if (isBatch) {
          deleteDictType(0, { id: id }).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        } else {
          deleteDictType(id).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        }
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
