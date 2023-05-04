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
      excelTitle="Album management"
    >
      >
      <template slot="extraForm">
        <el-form-item label="Album name search：">
          <el-input
            v-model="listQuery.album_name"
            class="input-width"
            placeholder="Album name search："
            style="width:300px;"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="State selection">
          <el-select
            v-model="listQuery.album_status"
            clearable
            class="input-width"
            placeholder="State selection"
          >
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
        ref="albumTable"
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column v-if="columns[0].visible" label="ID" align="center" width="120" prop="id"></el-table-column>
        <el-table-column
          v-if="columns[1].visible"
          label="Album preview"
          prop="album_name"
          align="center"
          width="400"
        >
          <template slot-scope="scope">
            <el-image
              fit="scale-down"
              style="width: 360px;height: 180px"
              :src="scope.row.album_cover"
              :preview-src-list="srcList"
            ></el-image>
          </template>
        </el-table-column>
        <el-table-column
          v-if="columns[2].visible"
          label="Album name"
          width="140"
          align="center"
          prop="album_name"
        ></el-table-column>
        <el-table-column
          v-if="columns[3].visible"
          label="album description"
          width="250"
          align="center"
          prop="album_desc"
        ></el-table-column>
        <el-table-column
          v-if="columns[4].visible"
          label="Album author"
          width="120"
          align="center"
          prop="album_author"
        ></el-table-column>
        <el-table-column
          v-if="columns[5].visible"
          label="Number of viewers"
          width="120"
          align="center"
          prop="album_click_num"
        ></el-table-column>
        <el-table-column
          v-if="columns[6].visible"
          label="Album sorting"
          width="120"
          align="center"
          prop="album_sort"
        ></el-table-column>
        <el-table-column
          v-if="columns[7].visible"
          label="Album state"
          width="120"
          align="center"
          prop="album_status"
          :formatter="statusFormat"
        ></el-table-column>
        <el-table-column sortable v-if="columns[8].visible" label="Creation time" align="center">
          <template slot-scope="scope">{{scope.row.created_at}}</template>
        </el-table-column>
        <el-table-column label="operate" align="center" width="300">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-view"
              type="success"
              size="mini"
              @click="handleViewAlbum(scope.row)"
            >Check</el-button>
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
        v-show="total>0"
        :total="total"
        :page.sync="listQuery.cur_page"
        :limit.sync="listQuery.page_size"
        @pagination="getList"
      ></Pagination>
    </div>

    <!--Add/modify album-->
    <album-detail ref="albumDetail" :albumDetailDialogData="albumDetailDialogData"></album-detail>
  </div>
</template>
<script>
import { albumList, deleteAlbum } from '@/api/blog/picture_module/album'
import AlbumDetail from './components/detail'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  album_status: null,
  album_name: '',
}
export default {
  name: 'Api:blog/picture_module/album/list-index',
  components: {
    AlbumDetail,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      srcList: [],
      total: 0,
      columns: [
        {key: 0, field: 'id', label: `ID`, visible: true},
        {key: 1, field: 'album_cover', label: `Album preview diagram`, visible: true},
        {key: 2, field: 'album_name', label: `Album name`, visible: true},
        {key: 3, field: 'album_desc', label: `Album description`, visible: true},
        {Key: 4, Field: 'Album_author', label: `Album Author`, visible: true},
        {key: 5, field: 'album_click_num', label: `Number of viewers`, visible: true},
        {key: 6, field: 'album_sort', label: `Album sorting`, visible: true},
        {key: 7, field: 'album_status', label: `Album status`, visible: true},
        {Key: 8, Field: 'created_at', label: `Create time`, visible: true},
      ],
      multipleSelection: [],
      statusOptions: [],
      typeOptions: [],
      albumDetailDialogData: {
        albumDetailDialogVisible: false,
        statusOptions: [],
        typeOptions: [],
        albumDetailTitle: '',
        isEdit: false,
        id: '',
      },
      albumShowDialogData: {
        albumShowDialogVisible: false,
        albumShowData: [],
      },
    }
  },
  created() {
    this.getDicts('blog_album_status').then((response) => {
      if (response.code == 200) this.statusOptions = response.data.list
    })
    this.getDicts('blog_album_type').then((response) => {
      if (response.code == 200) this.typeOptions = response.data.list
    })

    this.getList()
  },
  filters: {},
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleViewAlbum(row) {
      this.$router.push({
        name: 'Api:blog/picture_module/photo/list-index',
        params: { photo_album: row.id },
      })
    },
    handleAdd() {
      this.albumDetailDialogData.albumDetailDialogVisible = true
      this.albumDetailDialogData.statusOptions = this.statusOptions
      this.albumDetailDialogData.typeOptions = this.typeOptions
      this.albumDetailDialogData.albumDetailTitle = 'Add album'
      this.albumDetailDialogData.isEdit = false
      this.$refs['albumDetail'].getAlbumInfo()
    },
    handleEdit(row) {
      this.albumDetailDialogData.albumDetailDialogVisible = true
      this.albumDetailDialogData.statusOptions = this.statusOptions
      this.albumDetailDialogData.typeOptions = this.typeOptions
      this.albumDetailDialogData.albumDetailTitle =
        '修改 "' + row.title + '" 相册'
      this.albumDetailDialogData.isEdit = true
      this.albumDetailDialogData.id = row.id
      this.$refs['albumDetail'].getAlbumInfo()
    },
    handleDelete(row) {
      this.deleteAlbum(row.id)
    },
    handleBatchDelete() {
      let id_arr = []
      for (let i = 0; i < this.multipleSelection.length; i++) {
        id_arr.push(this.multipleSelection[i].id)
      }
      this.deleteAlbum(id_arr, true)
    },
    getList() {
      albumList(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.total = response.data.total
          this.list = response.data.list
          this.srcList = []
          for (let i = 0; i < this.list.length; i++) {
            this.srcList.push(this.list[i].album_cover)
          }
        }
      })
    },
    deleteAlbum(id, isBatch = false) {
      this.$confirm('Do you want to do this delete?', 'hint', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        if (isBatch) {
          deleteAlbum(0, { id: id }).then((response) => {
            if (response.code == 200) {
              this.multipleSelection = []
              this.getList()
            }
          })
        } else {
          deleteAlbum(id).then((response) => {
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
      return this.selectDictLabel(this.statusOptions, row.album_status)
    },
    //Status dictionary translation
    typeFormat(row, column) {
      return this.selectDictLabel(this.typeFormat, row.album_type)
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
