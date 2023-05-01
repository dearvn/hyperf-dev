<template lang="html">
  <div :val="value_">
    <div>
      <el-radio v-model="type" label="1" size="mini" border>weekly</el-radio>
    </div>
    <div>
      <el-radio v-model="type" label="5" size="mini" border>Not specify</el-radio>
    </div>
    <div>
      <el-radio v-model="type" label="2" size="mini" border>cycle</el-radio>
      <span style="margin-left: 10px; margin-right: 5px;">Weekly</span>
      <el-input-number @change="type = '2'" v-model="cycle.start" :min="1" :max="7" size="mini" style="width: 100px;"></el-input-number>
      <span style="margin-left: 5px; margin-right: 5px;">On the week</span>
      <el-input-number @change="type = '2'" v-model="cycle.end" :min="2" :max="7" size="mini" style="width: 100px;"></el-input-number>
    </div>
    <div>
      <el-radio v-model="type" label="3" size="mini" border>cycle</el-radio>
      <span style="margin-left: 10px; margin-right: 5px;">Weekly</span>
      <el-input-number @change="type = '3'" v-model="loop.start" :min="1" :max="7" size="mini" style="width: 100px;"></el-input-number>
      <span style="margin-left: 5px; margin-right: 5px;">Start, every</span>
      <el-input-number @change="type = '3'" v-model="loop.end" :min="1" :max="7" size="mini" style="width: 100px;"></el-input-number>
      Once the sky is executed
    </div>
    <div>
      <el-radio v-model="type" label="7" size="mini" border>Specify week</el-radio>
      <span style="margin-left: 10px; margin-right: 5px;">This month</span>
      <el-input-number @change="type = '7'" v-model="week.start" :min="1" :max="4" size="mini" style="width: 100px;"></el-input-number>
      <span style="margin-left: 5px; margin-right: 5px;">Week, week</span>
      <el-input-number @change="type = '7'" v-model="week.end" :min="1" :max="7" size="mini" style="width: 100px;"></el-input-number>
    </div>
    <div>
      <el-radio v-model="type" label="6" size="mini" border>The last one this month</el-radio>
      <span style="margin-left: 10px; margin-right: 5px;">Week</span>
      <el-input-number @change="type = '6'" v-model="last" :min="1" :max="7" size="mini" style="width: 100px;"></el-input-number>
    </div>
    <div>
      <el-radio v-model="type" label="4" size="mini" border>specify</el-radio>
      <el-select size="small" multiple v-model="appoint" @change="type = '4'">
        <el-option v-for="val in 7" :key="val" :value="val">{{
          val
        }}</el-option>
      </el-select>
      <!-- <el-checkbox-group v-model="appoint" style="margin-left: 0px;  line-height: 25px;">
          <el-checkbox @change="type = '4'"  v-for="i in 7" :key="i" :label="i"></el-checkbox>
      </el-checkbox-group> -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: '*',
    },
  },
  data() {
    return {
      type: '1', //type
      cycle: {
        //cycle
        start: 0,
        end: 0,
      },
      loop: {
        //cycle
        start: 0,
        end: 0,
      },
      week: {
        //specified week
        start: 0,
        end: 0,
      },
      work: 0,
      last: 0,
      appoint: [], //specify
    }
  },
  computed: {
    value_() {
      let result = []
      switch (this.type) {
        case '1': //per second
          result.push('*')
          break
        case '2': //annual
          result.push(`${this.cycle.start}-${this.cycle.end}`)
          break
        case '3': //cycle
          result.push(`${this.loop.start}/${this.loop.end}`)
          break
        case '4': //specify
          result.push(this.appoint.join(','))
          break
        case '6': //at last
          result.push(`${this.last === 0 ? '' : this.last}L`)
          break
        case '7': //specified week
          result.push(`${this.week.start}#${this.week.end}`)
          break
        default:
          //Not specify
          result.push('?')
          break
      }
      this.$emit('input', result.join(''))
      return result.join('')
    },
  },
  watch: {
    value(a, b) {
      this.updateVal()
    },
  },
  methods: {
    updateVal() {
      if (!this.value) {
        return
      }
      if (this.value === '?') {
        this.type = '5'
      } else if (this.value.indexOf('-') !== -1) {
        //2 cycles
        if (this.value.split('-').length === 2) {
          this.type = '2'
          this.cycle.start = this.value.split('-')[0]
          this.cycle.end = this.value.split('-')[1]
        }
      } else if (this.value.indexOf('/') !== -1) {
        //3 cycles
        if (this.value.split('/').length === 2) {
          this.type = '3'
          this.loop.start = this.value.split('/')[0]
          this.loop.end = this.value.split('/')[1]
        }
      } else if (this.value.indexOf('*') !== -1) {
        //1 each
        this.type = '1'
      } else if (this.value.indexOf('L') !== -1) {
        //6 last
        this.type = '6'
        this.last = this.value.replace('L', '')
      } else if (this.value.indexOf('#') !== -1) {
        //7 designated weeks
        if (this.value.split('#').length === 2) {
          this.type = '7'
          this.week.start = this.value.split('#')[0]
          this.week.end = this.value.split('#')[1]
        }
      } else if (this.value.indexOf('W') !== -1) {
        //8 working days
        this.type = '8'
        this.work = this.value.replace('W', '')
      } else {
        // *
        this.type = '4'
        // this.appoint = this.value.split(',').map(Number)
      }
    },
  },
  created() {
    this.updateVal()
  },
}
</script>

<style lang="css">
.el-checkbox + .el-checkbox {
  margin-left: 10px;
}
.el-checkbox {
  margin-right: 0px;
}
</style>
