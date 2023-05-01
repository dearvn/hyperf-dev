import { login, logout, initialization } from "@/api/auth/login";
import { getToken, setToken, removeToken } from "@/utils/auth";
import router from "@/router";
import store from "@/store";

const user = {
  state: {
    token: getToken(),
    name: "",
    avatar: "",
    roles: [],
    userId: ""
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token;
    },
    SET_NAME: (state, name) => {
      state.name = name;
    },
    SET_AVATAR: (state, avatar) => {
      state.avatar = avatar;
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles;
    },
    SET_USERID: (state, id) => {
      state.userId = id;
    },
    REFRESH_TOEKN(state, token) {
      state.token = token;
    }
  },

  actions: {
    // Log in
    Login({ commit }, userInfo) {
      const username = userInfo.username.trim();
      return new Promise((resolve, reject) => {
        login(username, userInfo.password, userInfo.captcha, userInfo.code_key)
          .then(response => {
            //If the login return data is empty, return false directly
            if (response.data.length == 0) {
              resolve(response);
              return response;
            }
            const data = response.data;
            const tokenStr = data.access_token;
            setToken(tokenStr);
            commit("SET_TOKEN", tokenStr);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },

    // Initialize user information
    Initialization({ commit, state }) {
      return new Promise((resolve, reject) => {
        initialization()
          .then(response => {
            const data = response.data;
            if (data.role_info && data.role_info.length > 0) {
              // Verify that the returned roles is a non-empty array
              commit("SET_ROLES", data.role_info);
            } else {
              reject("getInfo: role_info must be a non-null array !");
            }
            commit("SET_NAME", data.user_info.desc);
            commit("SET_AVATAR", data.user_info.avatar);
            commit("SET_USERID", data.user_info.id);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },

    // Sign out
    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout(state.token)
          .then(() => {
            commit("SET_TOKEN", "");
            commit("SET_ROLES", []);
            removeToken();
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },

    // front end logout
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit("SET_TOKEN", "");
        removeToken();
        resolve();
      });
    },
    // Save the refreshed token locally
    refreshToken({ commit }, token) {
      return new Promise(function(resolve, reject) {
        setToken(token);
        commit("REFRESH_TOEKN", token);
      });
    }
  }
};

export default user;
