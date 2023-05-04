<template>
  <el-dialog
    :title="dictDataDetailDialogData.dictDataDetailTitle"
    :visible.sync="dictDataDetailDialogData.dictDataDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="dictData" :rules="rules" ref="dictDataForm" label-width="150px">
      <el-form-item label="Dictionary" prop="dict_type">
        <el-input v-model="dictData.dict_type" auto-complete="off" disabled readonly size="medium"></el-input>
      </el-form-item>
      <el-form-item label="Data label" prop="dict_label">
        <el-input
          v-model="dictData.dict_label"
          plachod
          auto-complete="off"
          size="medium"
          placeholder="Please enter the data tag"
        ></el-input>
      </el-form-item>
      <el-form-item label="Data key" prop="dict_value">
        <el-input
          v-model="dictData.dict_value"
          plachod
          auto-complete="off"
          size="medium"
          placeholder="Please enter the data key value"
        ></el-input>
      </el-form-item>
      <el-form-item label="Display sortinglay sorting" prop="dict_sort">
        <el-input-number
          v-model="dictData.dict_sort"
          :min="1"
          :max="99"
          auto-complete="off"
          size="medium"
        ></el-input-number>
      </el-form-item>
      <el-form-item label="Status：">
        <el-radio-group v-model="dictData.status">
          <el-radio :label="1">Enable</el-radio>
          <el-radio :label="0">Disable</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="Remark">
        <el-input
          v-model="dictData.remark"
          type="textarea"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the contente enter the content"
        ></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('dictDataForm')">Submit</el-button>
      <el-button v-if="!dictDataDetailDialogData.isEdit" @click="resetForm('dictDataForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createDictData,
  updateDictData,
  editDictData,
} from '@/api/setting/system_module/dictData'
const defaultDictType = {
  dict_type: '',
  dict_label: '',
  dict_value: '',
  dict_sort: '',
  status: 1,
  remark: '',
}
export default {
  name: 'dictDataDetail',
  props: {
    dictDataDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      dictData: Object.assign({}, defaultDictType),
      rules: {
        dict_label: [
          { required: true, message: 'Please enter the data tag', trigger: 'blur' },
          {
            min: 1,
            max: 60,
            message: 'The length is 1 to 60 characters',
            trigger: 'blur',
          },
        ],
        dict_value: [
          { required: true, message: 'Please enter the data key value', trigger: 'blur' },
          {
            min: 1,
            max: 60,
            message: 'The length is 1 to 60 characters',
            trigger: 'blur',
          },
        ],
        dict_type: [
          { required: true, message: 'Please enter the dictionary type', trigger: 'blur' },
          {
            min: 1,
            max: 60,
            message: 'The length is 1 to 60 characters',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  created() {},
  methods: {
    getDictTypeInfo() {
      //Judging whether it is a modification
      if (this.dictDataDetailDialogData.isEdit == true) {
        editDictData(this.dictDataDetailDialogData.dict_code).then(
          (response) => {
            if (response.code == 200) {
              let dictDataData = response.data.list
              this.dictData = Object.assign({}, dictDataData)
            }
          }
        )
      } else {
        this.dictData = Object.assign({}, defaultDictType)
        this.dictData.dict_type = this.dictDataDetailDialogData.dictType
      }
    },
    onSubmit(dictDataForm) {
      this.$refs[dictDataForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.dictDataDetailDialogData.isEdit) {
              updateDictData(this.dictData.dict_code, this.dictData).then(
                (response) => {
                  this.$refs[dictDataForm].resetFields()
                  this.$parent.getList()
                  this.dictDataDetailDialogData.dictDataDetailDialogVisible = false
                }
              )
            } else {
              createDictData(this.dictData).then((response) => {
                if (response.code == 200) {
                  this.$refs[dictDataForm].resetFields()
                  this.dictData = Object.assign({}, defaultDictType)
                  this.$parent.getList()
                  this.dictDataDetailDialogData.dictDataDetailDialogVisible = false
                }
              })
            }
          })
        } else {
          this.$message({
            message: 'Verification failed',
            type: 'error',
            duration: 1000,
          })
          return false
        }
      })
    },
    resetForm(dictDataForm) {
      this.$refs[dictDataForm].resetFields()
      this.brand = Object.assign({}, defaultDictType)
    },
  },
}
</script>

<style>
</style>
