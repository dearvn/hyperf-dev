<template>
  <el-dialog
    :title="dictionaryDetailDialogData.dictionaryDetailTitle"
    :visible.sync="dictionaryDetailDialogData.dictionaryDetailDialogVisible"
    width="30%"
    :close-on-click-modal="false"
  >
    <el-form :model="dictType" :rules="rules" ref="dictTypeForm" label-width="150px">
      <el-form-item label="Dictionary name" prop="dict_name">
        <el-input
          v-model="dictType.dict_name"
          plachod
          auto-complete="off"
          size="medium"
          placeholder="Please enter the dictionary name"
        ></el-input>
      </el-form-item>
      <el-form-item label="Dictionary" prop="dict_type">
        <el-input
          v-model="dictType.dict_type"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the dictionary type"
        ></el-input>
      </el-form-item>
      <el-form-item label="Status：">
        <el-radio-group v-model="dictType.status">
          <el-radio :label="1">Enable</el-radio>
          <el-radio :label="0">Disable</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="Remark">
        <el-input
          v-model="dictType.remark"
          type="textarea"
          auto-complete="off"
          size="medium"
          placeholder="Please enter the content"
        ></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('dictTypeForm')">Submit</el-button>
      <el-button v-if="!dictionaryDetailDialogData.isEdit" @click="resetForm('dictTypeForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>

<script>
import {
  createDictType,
  updateDictType,
  editDictType,
} from '@/api/setting/system_module/dictType'
const defaultDictType = {
  dict_name: '',
  dict_type: '',
  status: 1,
  remark: '',
}
export default {
  name: 'dictionaryDetail',
  props: {
    dictionaryDetailDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      dictType: Object.assign({}, defaultDictType),
      rules: {
        dict_name: [
          { required: true, message: 'Please enter the dictionary name', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
            trigger: 'blur',
          },
        ],
        dict_type: [
          { required: true, message: 'Please enter the dictionary type', trigger: 'blur' },
          {
            min: 2,
            max: 60,
            message: 'The length is from 2 to 60 characters',
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
      if (this.dictionaryDetailDialogData.isEdit == true) {
        editDictType(this.dictionaryDetailDialogData.dict_id).then(
          (response) => {
            if (response.code == 200) {
              let dictTypeData = response.data.list
              this.dictType = Object.assign({}, dictTypeData)
            }
          }
        )
      } else {
        this.dictType = Object.assign({}, defaultDictType)
      }
    },
    onSubmit(dictTypeForm) {
      this.$refs[dictTypeForm].validate((valid) => {
        if (valid) {
          this.$confirm('Whether to submit data', 'hint', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
          }).then(() => {
            if (this.dictionaryDetailDialogData.isEdit) {
              updateDictType(this.dictType.dict_id, this.dictType).then(
                (response) => {
                  if (response.code == 200) {
                    this.$refs[dictTypeForm].resetFields()
                    this.$parent.getList()
                    this.dictionaryDetailDialogData.dictionaryDetailDialogVisible = false
                  }
                }
              )
            } else {
              createDictType(this.dictType).then((response) => {
                if (response.code == 200) {
                  this.$refs[dictTypeForm].resetFields()
                  this.dictType = Object.assign({}, defaultDictType)
                  this.$parent.getList()
                  this.dictionaryDetailDialogData.dictionaryDetailDialogVisible = false
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
    resetForm(dictTypeForm) {
      this.$refs[dictTypeForm].resetFields()
      this.brand = Object.assign({}, defaultDictType)
    },
  },
}
</script>

<style>
</style>
