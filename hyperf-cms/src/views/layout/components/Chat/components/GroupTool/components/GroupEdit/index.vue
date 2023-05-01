<template>
  <el-dialog
    :title="groupTool.contact.displayName"
    :visible.sync="groupTool.groupEditDialogVisible"
    :append-to-body="true"
    destroy-on-close
    width="45%"
    @close="closeDialog"
  >
    <el-form :model="group" :rules="rules" ref="groupForm" label-width="150px">
      <el-form-item label="Group ID:">
        <el-input v-model="group.group_id" disabled></el-input>
      </el-form-item>
      <el-form-item label="Group name:" prop="group_name">
        <el-input v-model="group.group_name" placeholder="Please fill in the group name"></el-input>
      </el-form-item>
      <el-form-item label="Group scale：" prop="size">
        <el-radio-group v-model="group.size">
          <el-radio :label="200">200 people</el-radio>
          <el-radio :label="500">500 people</el-radio>
          <el-radio :label="1000">1,000 people</el-radio>
          <el-radio :label="1500">1500 people</el-radio>
          <el-radio :label="2000">2000 people</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="Group introduction：">
        <tinymce
          v-if="groupTool.groupEditDialogVisible"
          :height="300"
          ref="introductionRef"
          id="tinymce"
          v-model="group.introduction"
        ></tinymce>
      </el-form-item>
      <el-form-item label="群验证：" prop="validation">
        <el-radio-group v-model="group.validation">
          <el-radio :label="0">Unnecessary</el-radio>
          <el-radio :label="1">Need</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="Group avatar：">
        <single-upload v-model="group.avatar" savePath="chat/group/avatar"></single-upload>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="onSubmit('groupForm')">Submit</el-button>
      <el-button @click="resetForm('groupForm')">Reset</el-button>
    </div>
  </el-dialog>
</template>
<script>
import Tinymce from '@/components/Tinymce'
import SingleUpload from '@/components/Upload/singleUpload'
const defaultGroup = {
  group_id: '',
  group_name: '',
  avatar: '',
  size: '',
  introduction: '',
  validation: '',
}
export default {
  name: 'GroupEdit',
  components: { Tinymce, SingleUpload },
  props: {
    groupTool: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      group: Object.assign({}, defaultGroup),
      rules: {
        group_name: [
          { required: true, message: 'Please fill in the group name', trigger: 'blur' },
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
  mounted() {},
  watch: {},
  created() {},
  methods: {
    init() {
      this.group.group_id = this.groupTool.contact.id
      this.group.group_name = this.groupTool.contact.displayName
      this.group.avatar = this.groupTool.contact.avatar
      this.group.size = this.groupTool.contact.size
      this.group.validation = this.groupTool.contact.validation
      this.group.introduction = this.groupTool.contact.introduction
      this.$nextTick(function () {
        this.$refs.introductionRef.setContent(
          this.groupTool.contact.introduction
        )
      })
    },
    onSubmit(groupForm) {
      this.$refs[groupForm].validate((valid) => {
        if (!valid) {
          this.$message({
            message: 'Verification failed',
            type: 'error',
            duration: 1000,
          })
          return false
        } else {
          this.$parent.$parent.$parent.sendEditGroup(this.group)
          this.groupTool.groupEditDialogVisible = false
        }
      })
    },
    closeDialog() {
      this.$refs.introductionRef.setContent('')
    },
  },
}
</script>
<style lang="scss" scoped>
.icon {
  width: 1.8em;
  height: 1.8em;
  cursor: pointer;
  &:hover {
    color: #000;
    border-color: #333;
  }
}
</style>
 