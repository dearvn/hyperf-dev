<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :addButton="false"
      :batchDelete="false"
      :list="list"
      :columns="columns"
      :multipleSelection="multipleSelection"
      @getList="getList"
      @handleBatchDelete="handleBatchDelete"
      excelTitle="UP main data report"
    >
      <template slot="extraForm">
        <el-form-item label="UP main ID:">
          <el-select
            style="width:300px"
            v-model="listQuery.mid"
            filterable
            remote
            reserve-keyword
            placeholder="Please fill in the ID of the search UP master"
            :remote-method="remoteMethod"
            :loading="loading"
            clearable
          >
            <el-option v-for="item in options" :key="item.mid" :label="item.name" :value="item.mid"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="time limit:">
          <el-date-picker
            v-model="listQuery.date"
            :picker-options="pickerOptions"
            type="daterange"
            range-separator="to"
            start-placeholder="Start date"
            end-placeholder="Ending date"
            :clearable="true"
            :editable="false"
            value-format="yyyy-MM-dd HH:mm:ss"
            :default-time="['00:00:00', '23:59:59']"
          />
        </el-form-item>
        <el-form-item label="Preview：">
          <el-select
            style="width:200px"
            v-model="listQuery.is_trend"
            filterable
            placeholder="Whether to open the trend preview"
            clearable
          >
            <el-option label="Open" :value="1"></el-option>
            <el-option label="Closure" :value="0"></el-option>
          </el-select>
        </el-form-item>
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-table ref="upUserDataReport" :data="list" style="width: 100%;" size="mini" border>
        <el-table-column label="Time" sortable width="150" prop="time" v-if="columns[0].visible"></el-table-column>
        <el-table-column label="Pay attention" sortable prop="following" v-if="columns[1].visible">
          <template slot-scope="scope">
            {{scope.row.following}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.following_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.following_trend < 0"></svg-icon>
              {{scope.row.following_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Number of fans" sortable prop="follower" v-if="columns[2].visible">
          <template slot-scope="scope">
            {{scope.row.follower}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.follower_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.follower_trend < 0"></svg-icon>
              {{scope.row.follower_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Video playback" sortable prop="video_play" v-if="columns[3].visible">
          <template slot-scope="scope">
            {{scope.row.video_play}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.video_play_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.video_play_trend < 0"></svg-icon>
              {{scope.row.video_play_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Number of reading" sortable prop="readling" v-if="columns[4].visible">
          <template slot-scope="scope">
            {{scope.row.readling}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.readling_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.readling_trend < 0"></svg-icon>
              {{scope.row.readling_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Praise" sortable prop="likes" v-if="columns[5].visible">
          <template slot-scope="scope">
            {{scope.row.likes}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.likes_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.likes_trend < 0"></svg-icon>
              {{scope.row.likes_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Monthly charging number" sortable prop="recharge_month" v-if="columns[6].visible">
          <template slot-scope="scope">
            {{scope.row.recharge_month}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.recharge_month_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.recharge_month_trend < 0"></svg-icon>
              {{scope.row.recharge_month_trend}}
              ）
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Total charging number" sortable prop="recharge_total" v-if="columns[7].visible">
          <template slot-scope="scope">
            {{scope.row.recharge_total}}
            <span v-if="listQuery.is_trend == 1">
              （
              <svg-icon icon-class="upward_trend" v-if="scope.row.recharge_total_trend > 0"></svg-icon>
              <svg-icon icon-class="down_trend" v-if="scope.row.recharge_total_trend < 0"></svg-icon>
              {{scope.row.recharge_total_trend}}
              ）
            </span>
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
  upUserDataReport,
  upUserSearch,
} from '@/api/laboratory/bilibili_module/upUser'
import { getDefaultDate } from '@/utils/date'
const defaultListQuery = {
  cur_page: 1,
  page_size: 50,
  mid: null,
  name: null,
  date: null,
  is_trend: null,
}
export default {
  components: {},
  data() {
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      options: [],
      multipleSelection: [],
      timedStatusOptions: [],
      columns: [
        { key: 0, field: 'time', label: `Time`, visible: true },
        { key: 1, field: 'following', label: `Pay attention`, visible: true },
        { key: 2, field: 'follower', label: `Number of fans`, visible: true },
        { key: 3, field: 'video_play', label: `Video playback`, visible: true },
        { key: 4, field: 'readling', label: `Number of reading`, visible: true },
        { key: 5, field: 'likes', label: `Praise`, visible: true },
        { key: 6, field: 'recharge_total', label: `Monthly charging number`, visible: true },
        { key: 7, field: 'recharge_month', label: `Total charging number`, visible: true },
      ],
    }
  },
  created() {
    upUserSearch().then((response) => {
      this.options = response.data.list
    })

    this.listQuery.date = getDefaultDate(1)

    const mid = this.$route.query && this.$route.query.mid
    const name = this.$route.query && this.$route.query.name
    this.listQuery.mid = mid
    this.listQuery.name = name

    this.getList()
  },
  methods: {
    getList() {
      upUserDataReport(this.listQuery).then((response) => {
        this.list = response.data.list
        this.total = response.data.total
      })
    },
    remoteMethod(query) {
      if (query !== '') {
        this.loading = true
        setTimeout(() => {
          this.loading = false
          upUserSearch().then((response) => {
            this.options = response.data.list.filter((item) => {
              return item.mid.toLowerCase().indexOf(query.toLowerCase()) > -1
            })
          })
        }, 200)
      } else {
        this.options = []
      }
    },
  },
}
</script>
<style scoped>
.input-width {
  width: 203px;
}
</style>
