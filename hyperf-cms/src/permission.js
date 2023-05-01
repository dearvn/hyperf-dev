import router from "./router";
import store from "./store";
import NProgress from "nprogress"; // Progress progress bar
import "nprogress/nprogress.css"; // Progress progress bar style
import { Message } from "element-ui";
import { Notification } from "element-ui";
import { getToken } from "@/utils/auth"; // Verification
import { asyncRouterMap } from "@/router";
const defaultSettings = require("./settings.js");

const whiteList = ["/login", "/register"]; // no redirect whitelist
router.beforeEach((to, from, next) => {
  //Determine whether to enable dynamic title
  if (store.state.setting.dynamicTitle && to.meta.title != undefined) {
    document.title = to.meta.title + " - " + defaultSettings.title;
  }

  //Initial configuration
  store.dispatch("InitialConfig").then();

  NProgress.start();
  if (getToken()) {
    if (to.path === "/login") {
      next({ path: "/" });
      NProgress.done(); // if current page is dashboard will not trigger afterEach hook, so manually handle it
    } else {
      if (store.getters.roles.length === 0) {
        store
          .dispatch("Initialization")
          .then(res => {
            // Pull user information
            if (res.code == 200 && store.state.setting.prompt) {
              Notification({
                title: "Welcome to Hyperf-CMS",
                message:
                  "If there is a jitter on the page, adjust the width screen of the browser. If you have errors during use or are optimized, you can click on the avatar to enter the system.",
                type: "success",
                offset: 100,
                duration: 5000
              });
            }
            const data = res.data;
            //Insert the access path field
            data.path = to.path;
            //generate route action
            store.dispatch("GenerateRoutes", { data }).then(accessRoutes => {
              //Dynamically add accessible routing table
              router.addRoutes(accessRoutes);
              // Add routing to vuex
              store.commit("SET_ROUTERS", accessRoutes);
              //The hack method ensures that addRoutes is complete, set the replace: true so the navigation will not leave a history record
              next({ ...to, replace: true });
            });
            //
            next();
          })
          .catch(err => {
            //Submit the vuex that logs out
            store.dispatch("FedLogOut").then(() => {
              Message.error(err || "Verification failed, please login again");
              next({ path: "/" });
            });
          });
      } else {
        next();
      }
    }
  } else {
    if (whiteList.indexOf(to.path) !== -1) {
      next();
    } else {
      next("/login");
      NProgress.done();
    }
  }
});

router.afterEach(to => {
  NProgress.done(); // end progress
});
