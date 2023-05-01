export default {
  methods: {
    init: function(socket) {
      // Instantiate the socket
      this.socket = socket;
      // Listen for socket connections
      this.socket.onopen = this.open;
      // Listen for socket error messages
      this.socket.onerror = this.error;
      // Listen to socket messages
      this.socket.onmessage = this.onmessage;
      // Listening socket is closed
      this.socket.onclose = this.close;
    },
    open: function() {},
    error: function() {
      console.log("connection error");
    },
    onmessage: function(msg) {
      const { IMUI } = this.$refs;
      let data = JSON.parse(msg.data);
      switch (data.event) {
        case "init":
          this.messageInitEvent(data, IMUI);
          break;
        case "friend_history_message":
          this.messageFriendHistoryEvent(data, IMUI);
          break;
        case "group_history_message":
          this.messageGroupHistoryEvent(data, IMUI);
          break;
        case "friend_withdraw_message":
          this.friendWithdrawMessageEvent(data, IMUI);
          break;
        case "friend_online_message":
          this.friendOnlineMessageEvent(data, IMUI);
          break;
        case "friend_offline_message":
          this.friendOfflineMessageEvent(data, IMUI);
          break;
        case "group_withdraw_message":
          this.groupWithdrawMessageEvent(data, IMUI);
          break;
        case "create_group":
          this.createGroupEvent(data, IMUI);
          break;
        case "edit_group":
          this.editGroupEvent(data, IMUI);
          break;
        case "new_member_join_group":
          this.newMemberJoinGroupEvent(data, IMUI);
          break;
        case "group_member_exit":
          this.groupMemberExitEvent(data, IMUI);
          break;
        case "delete_group_member":
          this.deleteGroupMemberEvent(data, IMUI);
          break;
        case "change_group_member_level":
          this.changeGroupMemberLevel(data, IMUI);
          break;
        case "delete_group":
          this.deleteGroup(data, IMUI);
          break;
        case "change_group_avatar":
          this.changeGroupAvatar(data, IMUI);
          break;
        case "new_friend_join_message":
          this.newFriendJoinMessage(data, IMUI);
          break;
        case "friend_delete_message":
          this.friendDeleteMessage(data, IMUI);
          break;
        default:
          this.getSendMessage(data, IMUI);
          break;
      }
    },
    send: function(message, uri, method = "GET") {
      let data = {
        message: message,
        uri: uri,
        method: method
      };
      this.socket.send(JSON.stringify(data));
    },
    close: function() {
      console.log("Connect to close, is being re -connected ...");
      setTimeout(() => {
        this.socket = new WebSocket(this.path + "?is_reconnection=true", [
          this.$store.getters.token
        ]);
        this.init(this.socket);
      }, 2000);
    }
  }
};
