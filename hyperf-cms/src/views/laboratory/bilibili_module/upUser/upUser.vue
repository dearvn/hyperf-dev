<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :columns.sync="columns"
      :addButton="false"
      :batchDelete="false"
      :list="list"
      :multipleSelection="multipleSelection"
      @getList="getList"
      @handleBatchDelete="handleBatchDelete"
      excelTitle="UP main list"
    >
      <template slot="extraForm">
        <el-form-item label="Account:">
          <el-input
            v-model="listQuery.mid"
            class="input-width"
            placeholder="Account:"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Name:">
          <el-input
            v-model="listQuery.name"
            class="input-width"
            placeholder="Name:"
            @keyup.enter.native="getList"
          ></el-input>
        </el-form-item>
        <el-form-item label="Timing status">
          <el-select
            v-model="listQuery.timed_status"
            clearable
            class="input-width"
            placeholder="Timing status："
          >
            <el-option
              v-for="dict in timedStatusOptions"
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
        ref="upUserTable"
        :data="list"
        style="width: 100%;"
        size="mini"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column label="MID" width="80" align="center" prop="mid" v-if="columns[0].visible"></el-table-column>
        <el-table-column label="Avatar" width="120" align="center" v-if="columns[1].visible">
          <template slot-scope="scope">
            <image-view
              :image_url="getImages(scope.row.face)"
              :image_list="srcList"
              style="width: 65px;height: 65px"
            ></image-view>
          </template>
        </el-table-column>
        <el-table-column sortable label="Name" prop="name" align="center" v-if="columns[2].visible"></el-table-column>
        <el-table-column label="Gender" width="100" align="center" prop="sex" v-if="columns[3].visible"></el-table-column>
        <el-table-column
          label="Sign"
          width="240"
          align="center"
          prop="sign"
          v-if="columns[4].visible"
        ></el-table-column>
        <el-table-column
          label="Level"
          width="140"
          align="center"
          prop="level"
          v-if="columns[5].visible"
        ></el-table-column>
        <el-table-column
          label="Birthday"
          width="120"
          align="center"
          prop="birthday"
          v-if="columns[6].visible"
        ></el-table-column>
        <el-table-column
          label="Pay attention"
          width="100"
          align="center"
          prop="following"
          v-if="columns[7].visible"
        ></el-table-column>
        <el-table-column
          label="Number of fans"
          width="120"
          align="center"
          prop="follower"
          v-if="columns[8].visible"
        ></el-table-column>
        <el-table-column
          label="Video playback"
          width="120"
          align="center"
          prop="video_play"
          v-if="columns[9].visible"
        ></el-table-column>
        <el-table-column
          label="Number of reading"
          width="120"
          align="center"
          prop="readling"
          v-if="columns[10].visible"
        ></el-table-column>
        <el-table-column
          label="Praise"
          width="120"
          align="center"
          prop="likes"
          v-if="columns[11].visible"
        ></el-table-column>
        <el-table-column sortable label="Status" width="80" align="center" v-if="columns[12].visible">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.timed_status"
              :active-value="1"
              :inactive-value="0"
              @change="handleStatusChange(scope.row)"
            ></el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Action" align="center" width="140">
          <template slot-scope="scope">
            <el-button
              icon="el-icon-download"
              type="success"
              size="mini"
              @click="handleSyncVideoReport(scope.row.mid)"
            >Sync video</el-button>
          </template>
        </el-table-column>
        <el-table-column label="Data report" align="center" width="140">
          <template slot-scope="scope">
            <el-dropdown size="mini" type="warning" trigger="click" v-if="scope.row.id != 1">
              <el-button icon="el-icon-more" type="primary" size="mini" class="button-color-violet">
                Data
                <i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item divided>
                  <el-button
                    icon="el-icon-data-line"
                    type="primary"
                    size="mini"
                    class="button-color-green"
                    @click="handleToUpUserChartTrend(scope.row.mid)"
                  >Chart trend</el-button>
                </el-dropdown-item>
                <el-dropdown-item divided>
                  <el-button
                    icon="el-icon-data-analysis"
                    type="danger"
                    size="mini"
                    class="button-color-pink"
                    @click="handleToUpUserDataReport(scope.row.mid)"
                  >Data report</el-button>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
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
  </div>
</template>
<script>
import {
  upUser,
  syncVideoReportFromUpUser,
} from '@/api/laboratory/bilibili_module/upUser'
import ImageView from '@/components/ImageView'
import store from '@/store'
const defaultListQuery = {
  cur_page: 1,
  page_size: 20,
  mid: null,
  name: null,
  timed_status: null,
}
export default {
  components: {
    ImageView,
  },
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      multipleSelection: [],
      srcList: [],
      timedStatusOptions: [],
      columns: [
        { key: 0, field: 'mid', label: `MID`, visible: true },
        { key: 1, field: 'face', label: `Avatar`, visible: true },
        { key: 2, field: 'name', label: `Name`, visible: true },
        { key: 3, field: 'sex', label: `Gender`, visible: true },
        { key: 4, field: 'sign', label: `Sign`, visible: true },
        { key: 5, field: 'level', label: `Grade`, visible: true },
        { key: 6, field: 'birthday', label: `Birthday`, visible: true },
        { key: 7, field: 'following', label: `Pay attention`, visible: true },
        { key: 8, field: 'follower', label: `Number of fans`, visible: true },
        { key: 9, field: 'video_play', label: `Video playback`, visible: true },
        { key: 10, field: 'readling', label: `Number of reading`, visible: true },
        { key: 11, field: 'likes', label: `Praise`, visible: true },
        { key: 12, field: 'timed_status', label: `Timing state`, visible: true },
      ],
    }
  },
  created() {
    this.getDicts('lab_up_user_time_status').then((response) => {
      if (response.code == 200) this.timedStatusOptions = response.data.list
    })
    const mid = this.$route.query && this.$route.query.mid
    this.listQuery.mid = mid
    this.getList()
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    getList() {
      upUser(this.listQuery).then((response) => {
        if (response.code == 200) {
          this.list = response.data.list
          this.total = response.data.total
          for (let i = 0; i < this.list.length; i++) {
            this.srcList.push(this.getImages(this.list[i].face))
          }
        }
      })
    },
    handleSyncVideoReport(mid) {
      this.$confirm('Do you want to simultaneously synchronize the UP main video?', 'Alert', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        syncVideoReportFromUpUser({ mid: mid }).then((response) => {})
      })
    },
    handleStatusChange(row) {
      let text = row.status === 0 ? 'Deactive' : 'Active'
      this.$confirm('Confirmation "' + text + '""' + row.desc + '"Users?', 'warn', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(function () {
          return changeStatus({
            id: row.id,
            status: row.status,
          })
        })
        .catch(function () {
          row.status = row.status === 0 ? 1 : 0
        })
    },
    handleToUpUserChartTrend(mid) {
      this.$router.push({
        path: '/laboratory/bilibili_module/up_user/up_user_chart_trend',
        query: { mid: mid },
      })
    },
    handleToUpUserDataReport(mid) {
      this.$router.push({
        path: '/laboratory/bilibili_module/up_user/up_user_data_report',
        query: { mid: mid },
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
