<template>
  <div class="app-container">
    <conditional-filter
      :listQuery.sync="listQuery"
      :defaultListQuery="defaultListQuery"
      :addButton="false"
      :batchDelete="false"
      :copyExcel="false"
      :exportExcel="false"
      :tableToolTip="false"
      :list="list"
      :multipleSelection="multipleSelection"
      @getList="getList"
      @handleBatchDelete="handleBatchDelete"
      excelTitle="UP main data trend chart"
    >
      <template slot="extraForm">
        <el-form-item label="UP master ID search:">
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
        <el-form-item label="Time limit：">
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
          <span style="color: #969696;">（The range can only be selected within 7 days）</span>
        </el-form-item>
      </template>
    </conditional-filter>
    <div class="table-container">
      <el-card
        class="operate-container"
        shadow="never"
        v-for="(item, key) in list"
        :key="key"
        style="width: 100%; display: inline-block;"
      >
        <div>
          <span>{{ item.label }}</span>
          <tip :content="item.desc" placement="top"></tip>
          <ve-line :data="item" :settings="item.chartSettings" :toolbox="toolbox" :colors="colors"></ve-line>
        </div>
      </el-card>
    </div>
  </div>
</template>
<script>
import {
  upUserChartTrend,
  upUserSearch,
} from '@/api/laboratory/bilibili_module/upUser'
import { getDefaultDate } from '@/utils/date'
import Tip from '@/components/Tip'
const defaultListQuery = {
  mid: null,
  name: null,
  date: null,
}
export default {
  components: {
    Tip,
  },
  data() {
    this.colors = [
      '#d50000',
      '#aa00ff',
      '#2962ff',
      '#00bfa5',
      '#ffd600',
      '#4e342e',
      '#c4ccd3',
    ]
    this.toolbox = {
      feature: {
        magicType: { type: ['line', 'bar'] },
        saveAsImage: {},
        restore: {},
        dataZoom: {},
        dataView: {},
      },
    }
    return {
      listQuery: Object.assign({}, defaultListQuery),
      defaultListQuery: Object.assign({}, defaultListQuery),
      list: [],
      total: 0,
      options: [],
      loading: false,
      multipleSelection: [],
      srcList: [],
      timedStatusOptions: [],
    }
  },
  created() {
    upUserSearch().then((response) => {
      this.options = response.data.list
    })

    this.listQuery.date = getDefaultDate(6)

    const mid = this.$route.query && this.$route.query.mid
    const name = this.$route.query && this.$route.query.name
    this.listQuery.mid = mid
    this.listQuery.name = name

    this.getList()
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    getList() {
      let date = this.listQuery.date
      let begin =
        new Date(new Date(date[0]).toLocaleDateString()).getTime() / 1000
      let end =
        new Date(new Date(date[1]).toLocaleDateString()).getTime() / 1000 +
        86400
      if ((end - begin) / 86400 > 7) {
        this.$message({
          type: 'error',
          message: 'The time range cannot exceed 7 days',
        })

        return false
      }

      upUserChartTrend(this.listQuery).then((response) => {
        this.list = response.data.rows
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
