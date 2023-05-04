<template>
  <el-dialog
    :title="timedTaskDetailDialogData.timedTaskDetailTitle"
    :visible.sync="timedTaskDetailDialogData.timedTaskDetailDialogVisible"
    width="700px"
    :close-on-click-modal="false"
  >
    <el-form
      :model="timedTask"
      :rules="rules"
      ref="timedTaskForm"
      label-width="120px"
    >
      <el-form-item label="Mission name：" prop="name">
        <el-input
          v-model="timedTask.name"
          auto-complete="off"
          size="medium"
          placeholder="Please fill in the task name"
          style="width:500px;"
        ></el-input>
      </el-form-item>
      <el-form-item label="Task name：" prop="task">
        <el-input
          v-model="timedTask.task"
          auto-complete="off"
          size="medium"
          placeholder="Please fill in the task name"
          style="width:500px;"
        ></el-input>
      </el-form-item>
      <el-form-item>
        <div slot="label">
          <tip
            content="The left is a field, and the corresponding value on the right"
            placement="left"
          />&nbsp;Parameter：
        </div>
        <div
          class="dataItem"
          v-for="(item, index) in timedTask.params"
          :key="index"
        >
          <el-input
            v-model="item[0]"
            style="width:200px"
            placeholder="Please fill in the field"
          ></el-input>
          <el-input
            v-model="item[1]"
            style="width:200px"
            placeholder="Please fill in the parameter"
          ></el-input>
          <el-button type="text" @click="handleDeleteParamsItem(index)"
            >Delete</el-button
          >
        </div>
        <el-button @click="handleAddParamsItem()" size="small">Add to</el-button>
      </el-form-item>
      <el-form-item label="Crontab expression：">
        <el-input
          size="medium"
          v-model="timedTask.execute_time"
          auto-complete="off"
          placeholder="Please fill in the Crontab expression"
        >
          <el-button
            slot="append"
            type="primary"
            v-if="!showCronBox"
            icon="el-icon-arrow-up"
            @click="showCronBox = true"
            title="Open the graphics configuration"
          ></el-button>
          <el-button
            type="primary"
            slot="append"
            v-else
            icon="el-icon-arrow-down"
            @click="showCronBox = false"
            title="Turn off the graphics configuration"
          ></el-button>
        </el-input>
      </el-form-item>
      <el-form-item style="margin-top: -10px; margin-bottom:0px;">
        <span style="color: #E6A23C; font-size: 12px;"
          >CORN from left to right (separated with spaces): Date in the month of the month in seconds in seconds
          years</span
        >
        <crontab v-if="showCronBox" v-model="timedTask.execute_time"></crontab>
      </el-form-item>
      <el-form-item label="Mission details：" prop="desc">
        <el-input
          v-model="timedTask.desc"
          auto-complete="off"
          size="medium"
          placeholder="Please fill in the task description"
          type="textarea"
          :rows="5"
        ></el-input>
      </el-form-item>
      <el-form-item label="Start status：" prop="status">
        <el-radio
          v-model="timedTask.status"
          v-for="dict in timedTaskDetailDialogData.statusOptions"
          :key="dict.dict_value"
          :label="dict.dict_value"
          >{{ dict.dict_label }}</el-radio
        >
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('timedTaskForm')"
        >Submit</el-button
      >
      <el-button
        v-if="!timedTaskDetailDialogData.isEdit"
        @click="resetForm('timedTaskForm')"
        >Reset</el-button
      >
    </div>
  </el-dialog>
</template>

<script>
import {
  createTimedTask,
  updateTimedTask,
  editTimedTask
} from "@/api/setting/monitoring_module/timedTask";
import Crontab from "@/components/Crontab";
const defaultTimedTask = {
  name: "",
  params: [],
  task: "",
  status: 1,
  execute_time: "",
  desc: ""
};
export default {
  name: "TimedTaskDetail",
  components: {
    Crontab
  },
  props: {
    timedTaskDetailDialogData: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      timedTask: Object.assign({}, defaultTimedTask),
      showCronBox: false,
      rules: {
        name: [
          { required: true, message: "Please fill in the task name", trigger: "blur" },
          {
            min: 2,
            max: 60,
            message: "The length is from 2 to 60 characters",
            trigger: "blur"
          }
        ],
        task: [
          { required: true, message: "Please fill in the task name", trigger: "blur" },
          {
            min: 2,
            max: 60,
            message: "The length is from 2 to 60 characters",
            trigger: "blur"
          }
        ],
        execute_time: [
          { required: true, message: "Please fill in the Crontab expression", trigger: "blur" },
          {
            min: 5,
            max: 60,
            message: "The length is 5 to 60 characters",
            trigger: "blur"
          }
        ],
        status: [{ required: true, message: "Please choose the type", trigger: "blur" }]
      }
    };
  },
  created() {},
  methods: {
    getTimedTaskInfo() {
      //Judging whether it is a modification
      if (this.timedTaskDetailDialogData.isEdit == true) {
        editTimedTask(this.timedTaskDetailDialogData.id).then(response => {
          let timedTaskData = response.data.list;
          this.timedTask = Object.assign({}, timedTaskData);
        });
      } else {
        this.timedTask = Object.assign({}, defaultTimedTask);
      }
    },
    onSubmit(timedTaskForm) {
      this.$refs[timedTaskForm].validate(valid => {
        if (valid) {
          this.$confirm("Do you want to sumit data?", 'Alert', {
            confirmButtonText: 'OK',
            cancelButtonText: "Cancel",
            type: "warning"
          }).then(() => {
            if (this.timedTaskDetailDialogData.isEdit) {
              updateTimedTask(this.timedTask.id, this.timedTask).then(
                response => {
                  this.$refs[timedTaskForm].resetFields();
                  this.$parent.getList();
                  this.timedTaskDetailDialogData.timedTaskDetailDialogVisible = false;
                }
              );
            } else {
              createTimedTask(this.timedTask).then(response => {
                this.$refs[timedTaskForm].resetFields();
                this.timedTask = Object.assign({}, defaultTimedTask);
                this.$parent.getList();
                this.timedTaskDetailDialogData.timedTaskDetailDialogVisible = false;
              });
            }
          });
        } else {
          this.$message({
            message: "Verification failed",
            type: "error",
            duration: 1000
          });
          return false;
        }
      });
    },
    resetForm(timedTaskForm) {
      this.$refs[timedTaskForm].resetFields();
      this.brand = Object.assign({}, defaultTimedTask);
    },
    handleAddParamsItem() {
      this.timedTask.params.push(["", ""]);
    },
    handleDeleteParamsItem(index) {
      this.timedTask.params.splice(index, 1);
    }
  }
};
</script>

<style></style>
