import axios from "axios";
import { Message, MessageBox } from "element-ui";
import store from "../store";
import { getToken } from "@/utils/auth";
import { Loading } from "element-ui";
let loading; //Define the loading variable

function startLoading() {
  //Use the Element loading-start method
  loading = Loading.service({
    lock: true,
    text: "In desperate loading, please later ...",
    background: "rgba(0, 0, 0, 0.7)",
    spinner: "el-icon-loading"
  });
}

function endLoading() {
  //Use the Element loading-close method
  loading.close();
}
let needLoadingRequestCount = 0;
export function showFullScreenLoading() {
  if (needLoadingRequestCount === 0) {
    startLoading();
  }
  needLoadingRequestCount++;
}

export function tryHideFullScreenLoading() {
  if (needLoadingRequestCount <= 0) return;
  needLoadingRequestCount--;
  if (needLoadingRequestCount === 0) {
    endLoading();
  }
}

// Create an axios instance
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // Api's base url
  timeout: 1000 * 60 * 10 // request timeout
});

// Request interceptor
service.interceptors.request.use(
  config => {
    if (store.getters.token) {
      config.headers["Authorization"] = "Bearer " + getToken(); // Let each request carry a custom token Please modify it according to the actual situation
    }
    let url = config.url;
    if (url.indexOf("/laboratory/chat_module/upload_pic_by_base64") == -1)
      showFullScreenLoading();
    return config;
  },
  error => {
    // Do something with request error
    // console.log(error) // for debug
    Promise.reject(error);
  }
);

// Response interceptor
service.interceptors.response.use(
  response => {
    /**
     * If the code is not 200, it is throwing an error, which can be modified according to your own business
     */
    var token = response.headers.authorization;
    if (token) {
      // If there is a token in the header, then trigger the refreshToken method to replace the local token
      token = token.replace(/Bearer /g, "");
      store.dispatch("refreshToken", token);
    }

    //define return data
    const res = response.data;

    //Judgment status code (custom)
    if (res.code != 200 && res.code.toString().length > 2) {
      //Define the error code. The following error codes will not pop up and report an error
      const errorCode = [401, 1002, 1003];
      if (errorCode.indexOf(res.code) == -1) {
        res.msg = res.msg ? res.msg : res.data.msg;
        Message({
          message: res.msg,
          type: "error",
          duration: 2000
        });
      }
      if (res.code == 1002 || res.code == 401 || res.code == 1003) {
        MessageBox.confirm(
          "The login status has expired, you can stay on this page, or log in again",
          "Determine",
          {
            confirmButtonText: "Login",
            cancelButtonText: "Cancel",
            type: "warning"
          }
        ).then(() => {
          store.dispatch("FedLogOut").then(() => {
            // In order to re-instantiate the vue-router object to avoid bugs
            location.reload();
          });
        });
      }
      tryHideFullScreenLoading();
      return response.data;
    } else {
      //Judgment request method
      const MessageMethod = ["put", "delete", "post"];
      if (MessageMethod.indexOf(response.config.method) != -1) {
        if (res.msg != "" && res.msg != null && res.msg != undefined) {
          Message({
            message: res.msg,
            type: "success",
            duration: 2000
          });
        }
      }
      tryHideFullScreenLoading();
      return response.data;
    }
  },
  error => {
    console.log("err" + error); // for debug
    Message({
      message: error.message,
      type: "error",
      duration: 2000
    });
    tryHideFullScreenLoading();
    return Promise.reject(error);
  }
);

export default service;
