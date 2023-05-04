<template>
  <el-dialog
    title="Add group chat"
    :visible.sync="createGroupDialogData.visible"
    width="1000px"
    :close-on-click-modal="false"
    :append-to-body="true"
    @close="closeDialog"
    class="field-dialog"
    destroy-on-close
  >
    <el-steps :active="active" simple>
      <el-step title="Fill in the info" icon="el-icon-edit"></el-step>
      <el-step title="Upload your avatar" icon="el-icon-upload"></el-step>
      <el-step title="Invite member" icon="el-icon-user"></el-step>
    </el-steps>
    <div v-if="active == 0" style="margin-top:40px">
      <el-form :model="group" ref="groupForm" :rules="rules" label-width="180px">
        <el-form-item label="Group name：" prop="name">
          <el-input
            v-model="group.name"
            auto-complete="off"
            size="medium"
            placeholder="Please fill in the group name"
            style="width:450px"
          ></el-input>
        </el-form-item>
        <el-form-item label="Group scale：" prop="size">
          <el-radio-group v-model="group.size">
            <el-radio :label="200">200people</el-radio>
            <el-radio :label="500">500people</el-radio>
            <el-radio :label="1000">1000people</el-radio>
            <el-radio :label="1500">1500people</el-radio>
            <el-radio :label="2000">2000people</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Group introduction：" prop="introduction">
          <tinymce
            :height="300"
            v-model="group.introduction"
            id="tinymce"
            v-if="createGroupDialogData.visible"
          ></tinymce>
        </el-form-item>
        <el-form-item label="Group verification：" prop="validation">
          <el-radio-group v-model="group.validation">
            <el-radio :label="0">Unnecessary</el-radio>
            <el-radio :label="1">Need</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
    </div>
    <div v-if="active == 1" style="margin-top:40px">
      <group-avatar :group="group" ref="groupAvatarRef"></group-avatar>
    </div>
    <div v-if="active == 2" style="margin-top:40px">
      <group-invite
        :contacts="createGroupDialogData.contacts"
        :creator="createGroupDialogData.creator"
        :group="group"
      ></group-invite>
    </div>

    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="prev()" :disabled="active == 0 ? true: false">Previous</el-button>
      <el-button size="small" @click="next('groupForm')" v-if="active != 2">Next step</el-button>
      <el-button size="small" type="primary" @click="handleCreateGroup('groupForm')" v-else>Complete creation</el-button>
    </div>
  </el-dialog>
</template>
<script>
import GroupAvatar from './components/GroupAvatar'
import GroupInvite from './components/GroupInvite'
import Tinymce from '@/components/Tinymce'
const defaultGroup = {
  name: '',
  validation: 0,
  content: '',
  size: 200,
  introduction: '',
  avatar: '',
  checkedContacts: [],
}
export default {
  name: 'CreateGroup',
  components: { GroupAvatar, GroupInvite, Tinymce },
  props: {
    createGroupDialogData: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      group: Object.assign({}, defaultGroup),
      active: 0,
      rules: {
        name: [
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
    init() {},
    prev() {
      this.active -= 1
    },
    next(groupForm) {
      if (this.active == 0) {
        this.$refs[groupForm].validate((valid) => {
          if (!valid) {
            this.$message({
              message: 'Verification failed',
              type: 'error',
              duration: 1000,
            })
            return false
          } else {
            this.active += 1
          }
        })
      } else if (this.active == 1) {
        this.$refs.groupAvatarRef.uploadImg()
      } else {
        this.active += 1
      }
    },
    uploadAvatarSuccess() {
      this.active += 1
    },
    handleCreateGroup(groupForm) {
      this.$parent.$parent.sendCreateGroup(this.group)
      this.msgSuccess('Successful creation group')
      this.closeDialog()
    },
    closeDialog() {
      this.createGroupDialogData.visible = false
      this.group = Object.assign({}, defaultGroup)
      this.active = 0
    },
  },
}
</script>
<style lang="scss" scoped>
</style>
 