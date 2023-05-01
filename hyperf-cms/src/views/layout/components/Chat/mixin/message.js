import { fileByBase64 } from "@/utils/file";
import { uploadPicByBase64 } from "@/api/laboratory/chat_module/upload";
import { generateUUID } from "@/utils/functions";
export default {
  methods: {
    keepLastIndex(obj) {
      if (window.getSelection) {
        obj.focus();
        var range = window.getSelection();
        range.selectAllChildren(obj);
        range.collapseToEnd();
      } else if (document.selection) {
        var range = document.selection.createRange();
        range.moveToElementText(obj);
        range.collapse(false);
        range.select();
      }
    },
    changeDrawer(contact) {
      const IMUI = this.$refs.IMUI;
      if (IMUI.drawerVisible === true) {
        IMUI.closeDrawer();
      } else {
        const params = {
          render: contact => {
            return <group-drawer contact={contact}></group-drawer>;
          }
        };
        params.offsetY = 1;
        IMUI.openDrawer(params);
      }
    },
    initMultiMenu() {
      //Initialize the multi-select tool
      var dom = document.createElement("div");
      dom.setAttribute("class", "multi");
      dom.setAttribute("style", "display:none");
      dom.innerHTML =
        '<div class="multi-select"><div class="multi-title"><span">selected：<span id="checkMessage">0</span> Message</span></div><div class="multi-main"><div class="btn-group"><div class="multi-icon pointer"  onClick="mergeForward()"><i class="el-icon-position"></i></div><p>Merge</p></div><div class="btn-group"><div class="multi-icon pointer" onClick="oneByoneForward()"><i class="el-icon-position"></i></div><p>One by one</p></div><div class="btn-group"><div class="multi-icon pointer" onclick="multiDeleteContact()"><i class="el-icon-delete"></i></div><p >batch deletion</p></div><div class="btn-group"><div class="multi-icon pointer" onClick="closeMulti()"><i class="el-icon-close" ></i></div><p >closure</p></div></div></div>';
      const that = this;
      window.closeMulti = function() {
        that.closeMulti();
      };
      window.mergeForward = function() {
        that.mergeForward();
      };
      window.oneByoneForward = function() {
        that.oneByoneForward();
      };
      window.multiDeleteContact = function() {
        that.multiDeleteContact();
      };
      document.querySelector(".lemon-editor").appendChild(dom);
    },
    closeMulti() {
      $(".lemon-editor")
        .find("*")
        .each(function(i, o) {
          if ($(o).hasClass("lemon-editor__tool")) $(this).show();
          if ($(o).hasClass("lemon-editor__inner")) $(this).show();
          if ($(o).hasClass("lemon-editor__footer")) $(this).show();
          if ($(o).hasClass("multi")) $(this).hide();
        });
      $(".lemon-container")
        .find("*")
        .each(function(i, o) {
          if (
            $(o).hasClass("lemon-message-text") ||
            $(o).hasClass("lemon-message-file") ||
            $(o).hasClass("lemon-message-image") ||
            $(o).hasClass("lemon-message-forward") ||
            $(o).hasClass("lemon-message-video")
          ) {
            $(this).css("border", "");
            $(this).css("margin-top", "");
          }
        });
      $(".lemon-container")
        .find("*")
        .each(function(i, o) {
          if (
            ($(o).hasClass("lemon-message-text") ||
              $(o).hasClass("lemon-message-file") ||
              $(o).hasClass("lemon-message-image") ||
              $(o).hasClass("lemon-message-forward") ||
              $(o).hasClass("lemon-message-video")) &&
            !$(o).hasClass("lemon-message--reverse")
          ) {
            $(this).css("padding-left", "");
          }
        });
      this.multiMessage = [];
      this.multi = false;
      $("#checkMessage").html(0);
    },
    messageInitEvent(data, IMUI) {
      this.user = data.user_info;
      this.friend_group = data.friend_group;
      //Initialize the contact (use lastContentRender to convert the image file type)
      for (let i = 0; i < data.user_contact.length; i++) {
        if (
          data.user_contact[i].lastContent != "" &&
          data.user_contact[i].lastContentType != ""
        ) {
          data.user_contact[i].lastContent = IMUI.lastContentRender({
            type: data.user_contact[i].lastContentType,
            content: data.user_contact[i].lastContent
          });
        }
      }
      for (let i = 0; i < data.user_group.length; i++) {
        if (
          data.user_group[i].lastContent != "" &&
          data.user_group[i].lastContentType != ""
        ) {
          data.user_group[i].lastContent = IMUI.lastContentRender({
            type: data.user_group[i].lastContentType,
            content: data.user_group[i].lastContent
          });
        }
      }
      let contact = data.user_contact.concat(data.user_group);
      //Initialize user
      IMUI.initContacts(contact);
      //Automatically navigate to the latest news
      IMUI.messageViewToBottom();
    },
    messageFriendHistoryEvent(data, IMUI) {
      this.messages = data;
      this.next(this.messages.friend_history_message, true);
    },
    messageGroupHistoryEvent(data, IMUI) {
      this.messages = data;
      this.next(this.messages.group_history_message, true);
    },
    friendWithdrawMessageEvent(data, IMUI) {
      let message = data.message;
      const appendMessag = {
        id: generateRandId(),
        type: "event",
        content: '"' + message.fromUser.displayName + '" With a message',
        toContactId: message.fromUser.id,
        sendTime: getTime()
      };
      IMUI.removeMessage(message.id);
      IMUI.appendMessage(appendMessag, true);
    },
    friendOnlineMessageEvent(data, IMUI) {
      //Determine whether to display message notifications
      if (
        this.settingDialogData.friendOnlineNotice &&
        !data.message.is_reconnection
      ) {
        this.$notify.warning({
          title: 'Your friend "' + data.message.user_info.desc + '" Have been launched',
          duration: 2000,
          position: "bottom-right",
          offset: 100,
          message: "From the system notification"
        });
      }
      //Play incoming message audio
      if (
        this.settingDialogData.friendOnlineNoticeTone &&
        !data.message.is_reconnection
      ) {
        this.playAudio("friendOnlineTone.mp3");
      }
      IMUI.updateContact({
        id: data.message.uid,
        status: data.message.online_status
      });
    },
    friendOfflineMessageEvent(data, IMUI) {
      IMUI.updateContact({
        id: data.message.uid,
        status: data.message.online_status
      });
    },
    createGroupEvent(data, IMUI) {
      let contact = data.message.group_info;
      IMUI.appendContact(contact);
    },
    editGroupEvent(data, IMUI) {
      //Determine whether to create a group
      let groupInfo = data.message.group_info;
      IMUI.updateContact({
        id: data.message.toContactId,
        avatar: groupInfo.avatar,
        displayName: groupInfo.group_name,
        introduction: groupInfo.introduction,
        size: groupInfo.size,
        validation: groupInfo.validation
      });
      IMUI.appendMessage(data.message, true);
    },
    newMemberJoinGroupEvent(data, IMUI) {
      let contact = data.message.group_info;
      IMUI.appendContact(contact);
    },
    groupMemberExitEvent(data, IMUI) {
      IMUI.appendMessage(data.message, true);
      IMUI.updateContact({
        id: data.message.toContactId,
        group_member: data.message.group_member,
        member_total: data.message.member_total
      });
      if (this.user.id == data.message.uid) {
        IMUI.removeContact(data.message.toContactId);
      }
    },
    deleteGroupMemberEvent(data, IMUI) {
      IMUI.appendMessage(data.message, true);
      IMUI.updateContact({
        id: data.message.toContactId,
        group_member: data.message.group_member,
        member_total: data.message.member_total
      });
      if (this.user.id == data.message.uid) {
        IMUI.removeContact(data.message.toContactId);
        this.$confirm(
          'You have been removed "' + data.message.displayName + '" group chat',
          "hint",
          {
            confirmButtonText: "Sure",
            cancelButtonText: "Cancel",
            type: "warning"
          }
        );
      }
    },
    deleteGroup(data, IMUI) {
      IMUI.removeContact(data.message.toContactId);
      if (this.user.id != data.message.uid) {
        this.$confirm(data.message.content, "hint", {
          confirmButtonText: "Sure",
          cancelButtonText: "Cancel",
          type: "warning"
        });
      }
    },
    changeGroupMemberLevel(data, IMUI) {
      IMUI.appendMessage(data.message, true);
      IMUI.updateContact({
        id: data.message.toContactId,
        group_member: data.message.group_member,
        member_total: data.message.member_total
      });
      if (data.message.uid == this.user.id) {
        IMUI.updateContact({
          id: data.message.toContactId,
          level: data.message.level
        });
      }
    },
    changeGroupAvatar(data, IMUI) {
      IMUI.updateContact({
        id: data.message.toContactId,
        avatar: data.message.avatar
      });
    },
    getSendMessage(data, IMUI) {
      IMUI.appendMessage(data.message, true);
      //Determine whether to display message notifications
      if (this.settingDialogData.messagePagePrompt) {
        this.$notify.warning({
          title: "You have a new news",
          duration: 2000,
          position: "bottom-right",
          offset: 100,
          message: '来自："' + data.message.fromUser.displayName + '"'
        });
      }
      //Play receiving information audio
      if (this.settingDialogData.messageTone) {
        this.playAudio(this.settingDialogData.messageToneType);
      }
      IMUI.messageViewToBottom();
    },
    handlePullMessages(contact, next) {
      const that = this;
      let uri =
        contact.is_group == 0 ? "/friend/pull_message" : "/group/pull_message";
      let data = {
        message: {
          contact_id: contact.id,
          user_id: this.user.id
        },
        uri: uri
      };
      this.socket.send(JSON.stringify(data));
      this.next = next;
    },
    handleSend(message, next, file) {
      const { IMUI } = this.$refs;
      //Execution to the next message will stop the circle, if the interface call fails, you can modify the status of the message next({status:'failed'});
      //Call your message sending business interface
      //First judge whether it is an image upload, here is mainly for pasting the image and cannot rewrite the component

      message.content = message.content.replace(
        /^(?:[\n\r]*)|(?:[\n\r]*)$/g,
        ""
      );
      if (message.content.indexOf("blob:") != -1) {
        fileByBase64(file, base64 => {
          let params = {
            savePath: "chat/group",
            file: base64
          };
          uploadPicByBase64(params)
            .then(response => {
              if (response.code == 200) {
                message.content = response.data.url;
                let uri =
                  typeof message.toContactId == "number"
                    ? "/friend/send_message"
                    : "/group/send_message";
                this.send(message, uri);
                next();
              }
            })
            .catch(() => {
              next({ status: "failed" });
            });
        });
      } else {
        let uri =
          typeof message.toContactId == "number"
            ? "/friend/send_message"
            : "/group/send_message";
        IMUI.setEditorValue("");
        this.send(message, uri);
        next();
      }
    },
    handleChangeContact(contact, instance) {
      instance.updateContact({
        id: contact.id,
        unread: 0
      });
      this.historyMessageDialogData.contact_id = contact.id;
      instance.closeDrawer();
      instance.messageViewToBottom();
      this.closeMulti();
    },
    handleMessageClick(event, key, Message, instance) {
      if (Message.type == "image") {
        this.imageSrc = Message.content;
        while (this.srcList.length > 0) {
          this.srcList.pop();
        }
        for (let i = 0; i < instance.getCurrentMessages().length; i++) {
          if (instance.getCurrentMessages()[i].type == "image")
            this.srcList.push(instance.getCurrentMessages()[i].content);
        }
        this.$refs.preview.clickHandler();
      }
    },
    handleCreateGroup(instance) {
      this.createGroupDialogData.visible = true;
      this.createGroupDialogData.contacts = instance.contacts.filter(function(
        item
      ) {
        if (item.is_group != 1) return item;
      });
      this.createGroupDialogData.creator = instance.user;
    },
    handleOpenGroupTool(type, contact) {
      if (type == "group_file") this.groupTool.groupFileDialogVisible = true;
      if (type == "group_notice")
        this.groupTool.groupNoticeDialogVisible = true;
      if (type == "group_album") this.groupTool.groupAlbumDialogVisible = true;
      if (type == "group_invite")
        this.groupTool.groupInviteDialogVisible = true;
      if (type == "group_member_manage")
        this.groupTool.groupMemberManageDialogVisible = true;
      if (type == "group_edit") this.groupTool.groupEditDialogVisible = true;
      if (type == "group_exit") {
        this.$confirm("Confirm that exiting the group, the operation is irreversible, do you continue?", "hint", {
          confirmButtonText: "Sure",
          cancelButtonText: "Cancel",
          type: "warning"
        })
          .then(() => {
            let message = {
              group_id: contact.id,
              uid: this.user.id
            };
            this.send(message, "/group/exit_group", "POST");
          })
          .catch(() => {});
      }
      if (type == "group_delete") {
        this.$confirm("Confirm the dissolution of the group, the operation is irreversible, do you continue? ", "Reminder", {
          confirmButtonText: "Sure",
          cancelButtonText: "Cancel",
          type: "warning"
        })
          .then(() => {
            let message = {
              group_id: contact.id,
              uid: this.user.id
            };
            this.send(message, "/group/delete_group", "POST");
          })
          .catch(() => {
            this.msgError("The operation failed, please try it out");
          });
      }
      this.groupTool.contact = contact;
      this.groupTool.type = type;
      this.groupTool.user = this.user;
      this.$refs["groupToolRef"].init();
    },
    composeValue(type, row) {
      return {
        command: type,
        contact: row
      };
    },
    handleCommand(command) {
      this.handleOpenGroupTool(command.command, command.contact);
    },
    sendEditGroup(group) {
      group.uid = this.user.id;
      this.send(group, "/group/edit_group", "POST");
      this.msgSuccess("Modify group announcement success");
    },
    sendCreateGroup(group) {
      group.creator = this.createGroupDialogData.creator;
      this.send(group, "/group/create_group", "POST");
    },
    sendInviteGroupMember(group, newJoinGroupMember) {
      const { IMUI } = this.$refs;
      let newGroup = JSON.parse(JSON.stringify(group));
      newGroup.newJoinGroupMember = newJoinGroupMember;
      this.send(newGroup, "/group/invite_group_member", "POST");
    },
    sendDeleteGroupMember(group) {
      this.send(group, "/group/delete_group_member", "POST");
      this.msgSuccess("Delete the successful team");
    },
    sendChangeGroupLevel(group) {
      this.send(group, "/group/change_group_member_level", "POST");
      this.msgSuccess("Change the team level successful");
    },
    beforeFileUpload(file, dataObj, type) {
      const { IMUI } = this.$refs;
      const message = {
        id: dataObj.messageId,
        status: "going",
        type: type,
        sendTime: Date.parse(new Date()),
        content: "",
        fileSize: file.size,
        fileName: file.name,
        fileExt: "",
        toContactId: IMUI.getCurrentContact().id,
        fromUser: {
          id: this.user.id,
          displayName: this.user.displayName,
          avatar: this.user.avatar
        }
      };
      this.$set(this.messagesToBeSend, message.id, message);
      this.$set(this.fileIdToMessageId, file.uid, message.id);
      IMUI.appendMessage(message, true);
    },
    afterFileUpload(res, file) {
      const { IMUI } = this.$refs;
      if (res.code != 200) {
        this.$message({
          showClose: true,
          message: res.msg,
          type: "error"
        });
        IMUI.updateMessage({
          id: this.fileIdToMessageId[file.uid],
          status: "failed"
        });
      } else {
        IMUI.updateMessage({
          id: res.data.messageId,
          content: res.data.url,
          fileExt: res.data.fileExt,
          status: "succeed"
        });
        let messageId = res.data.messageId;
        let uri =
          typeof this.messagesToBeSend[messageId].toContactId == "number"
            ? "/friend/send_message"
            : "/group/send_message";
        this.send(this.messagesToBeSend[messageId], uri);
      }
      delete this.messagesToBeSend[res.data.messageId];
      delete this.fileIdToMessageId[file.uid];
    },
    querySearch(queryString, cb) {
      const { IMUI } = this.$refs;
      var contacts = IMUI.getContacts();

      var results = queryString
        ? contacts.filter(this.createFilter(queryString))
        : contacts;
      // Call the callback to return the data of the suggestion list
      cb(results);
    },
    createFilter(queryString) {
      return contact => {
        return (
          contact.displayName
            .toLowerCase()
            .indexOf(queryString.toLowerCase()) === 0
        );
      };
    },
    handleSelect(item) {
      const { IMUI } = this.$refs;
      IMUI.changeContact(item.id);
    },
    handleChangeMenu() {
      const { IMUI } = this.$refs;
      IMUI.closeDrawer();
      this.closeMulti();
    },
    mergeForward() {
      const { IMUI } = this.$refs;
      //If the selected message is more than two, it will be displayed
      if (this.multiMessage.length >= 2) {
        for (let i = 0; i < this.multiMessage.length; i++) {
          if (this.multiMessage[i].type == "forward") {
            this.$notify({
              title: "Message forwarding",
              message: "The session record does not support merging forwarding",
              type: "warning",
              offset: 100
            });
            return;
          }
        }
        this.forwardTool.dialogVisible = true;
        this.forwardTool.contact = IMUI.getContacts();
        this.forwardTool.contactsSource = IMUI.getContacts();
        this.forwardTool.multiMessage = this.multiMessage;
        this.forwardTool.type = "mergeForward";
        this.forwardTool.user = this.user;
      }
    },
    oneByoneForward(message) {
      const { IMUI } = this.$refs;
      if (message != "" && message != undefined && message != null) {
        //If the selected message is more than two, it will be displayed
        this.forwardTool.dialogVisible = true;
        this.forwardTool.contact = IMUI.getContacts();
        this.forwardTool.contactsSource = IMUI.getContacts();
        this.forwardTool.multiMessage = [message];
        this.forwardTool.type = "oneByOneForward";
        this.forwardTool.user = this.user;
      } else {
        //If the selected message is more than two, it will be displayed
        if (this.multiMessage.length >= 2) {
          this.forwardTool.dialogVisible = true;
          this.forwardTool.contact = IMUI.getContacts();
          this.forwardTool.contactsSource = IMUI.getContacts();
          this.forwardTool.multiMessage = this.multiMessage;
          this.forwardTool.type = "oneByOneForward";
          this.forwardTool.user = this.user;
        }
      }
    },
    multiDeleteContact() {
      const { IMUI } = this.$refs;
      for (let i = 0; i < this.multiMessage.length; i++) {
        IMUI.removeMessage(this.multiMessage[i].id);
      }
      this.closeMulti();
    },
    insertContent(content) {
      if (!content) {
        //Returns if the inserted content is empty
        return;
      }
      let sel = null;
      if (document.selection) {
        //Below I e9
        sel = document.selection;
        sel.createRange().pasteHTML(content);
      } else {
        sel = window.getSelection();
        if (sel.rangeCount > 0) {
          let range = sel.getRangeAt(0); //Get selection range
          range.deleteContents(); //delete selected
          let el = document.createElement("div"); //Create an empty div shell
          el.innerHTML = content; //Set the div content to what we want to insert.
          let frag = document.createDocumentFragment(); //Create a blank document fragment for later insertion into the dom tree

          let node = el.firstChild;
          let lastNode = frag.appendChild(node);
          range.insertNode(frag); //Set the content of the selection range to the inserted content
          let contentRange = range.cloneRange(); //clone selection
          contentRange.setStartAfter(lastNode); //Set the cursor position to the end of the inserted content
          contentRange.collapse(true); //Move the cursor position to the end
          sel.removeAllRanges(); //remove all selections
          sel.addRange(contentRange); //Add modified selection
        }
      }
    },
    newFriendJoinMessage(data, IMUI) {
      IMUI.appendContact(data.message.contact);
    },
    friendDeleteMessage(data, IMUI) {
      IMUI.removeContact(data.message.contact_id);
    },
    sendLinkMessage(content) {
      const { IMUI } = this.$refs;
      const message = {
        id: generateUUID(),
        status: "succeed",
        type: "link",
        sendTime: Date.parse(new Date()),
        content: content,
        toContactId: IMUI.getCurrentContact().id,
        fromUser: {
          id: this.user.id,
          displayName: this.user.displayName,
          avatar: this.user.avatar
        }
      };
      let uri =
        typeof message.toContactId == "number"
          ? "/friend/send_message"
          : "/group/send_message";
      this.send(message, uri);
      IMUI.appendMessage(message, true);
      this.linkMessageDialogData.visible = false;
    }
  }
};
