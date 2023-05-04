<template lang="html">
  <div class="cron" :val="value_">
    <el-tabs v-model="activeName">
      <el-tab-pane label="Point" name="m">
        <second-and-minute v-model="mVal" lable="point"></second-and-minute >
      </el-tab-pane>
      <el-tab-pane label="Hour" name="h">
        <hour v-model="hVal" lable="hour"></hour>
      </el-tab-pane>
      <el-tab-pane label="Day" name="d">
        <day v-model="dVal" lable="Day"></day>
      </el-tab-pane>
      <el-tab-pane label="Month" name="month">
        <month v-model="monthVal" lable="moon"></month>
      </el-tab-pane>
      <el-tab-pane label="Week" name="week">
        <week v-model="weekVal" lable="week"></week>
      </el-tab-pane>
    </el-tabs>
    <!-- table -->
    <el-table
       :data="tableData"
       size="mini"
       border
       style="width: 100%;">
       <el-table-column
         prop="mVal"
         label="Point"
         width="70">
       </el-table-column>
       <el-table-column
         prop="hVal"
         label="Hour"
         width="70">
       </el-table-column>
       <el-table-column
         prop="dVal"
         label="Day"
         width="70">
       </el-table-column>
       <el-table-column
         prop="monthVal"
         label="Month"
         width="70">
       </el-table-column>
       <el-table-column
         prop="weekVal"
         label="Week"
         width="70">
       </el-table-column>
       
     </el-table>
  </div>
</template>
<script>
import SecondAndMinute from './cron/secondAndMinute'
import hour from './cron/hour'
import day from './cron/day'
import month from './cron/month'
import week from './cron/week'
export default {
  props: {
    value: {
      type: String,
    },
  },
  data() {
    return {
      //
      activeName: 'm',
      mVal: '',
      hVal: '',
      dVal: '',
      monthVal: '',
      weekVal: '',
    }
  },
  watch: {
    value(a, b) {
      this.updateVal()
    },
  },
  computed: {
    tableData() {
      return [
        {
          mVal: this.mVal,
          hVal: this.hVal,
          dVal: this.dVal,
          monthVal: this.monthVal,
          weekVal: this.weekVal,
        },
      ]
    },
    value_() {
      if (!this.dVal && !this.weekVal) {
        return ''
      }
      if (this.dVal === '?' && this.weekVal === '?') {
        this.$message.error('The date and week cannot be "not specified" at the same time')
      }
      if (this.dVal !== '?' && this.weekVal !== '?') {
        this.$message.error('One date and week must be "not specified"')
      }
      let v = `${this.mVal} ${this.hVal} ${this.dVal} ${this.monthVal} ${this.weekVal}`
      if (v !== this.value) {
        this.$emit('input', v)
      }
      return v
    },
  },
  methods: {
    updateVal() {
      if (!this.value) {
        return
      }
      let arrays = this.value.split(' ')
      this.mVal = arrays[0]
      this.hVal = arrays[1]
      this.dVal = arrays[2]
      this.monthVal = arrays[3]
      this.weekVal = arrays[4]
    },
  },
  created() {
    this.updateVal()
  },
  components: {
    SecondAndMinute,
    hour,
    day,
    month,
    week,
  },
}
</script>

<style lang="css">
.cron {
  text-align: left;
  padding: 10px;
  background: #fff;
  border: 1px solid #dcdfe6;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.12), 0 0 6px 0 rgba(0, 0, 0, 0.04);
}
</style>
