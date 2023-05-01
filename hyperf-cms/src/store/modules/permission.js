import { asyncRouterMap } from "@/router";
import { arrayLookup } from "@/utils/functions";
import { getRouters } from "@/api/auth/login";
import Layout from "@/views/layout/Layout";
import router from "@/router";
import Vue from "vue";

function hasPermission(permission, route) {
  if (route.name) {
    return permission.indexOf(route.name) >= 0;
  } else {
    return true;
  }
}

const permission = {
  state: {
    routers: [],
    currentModule: "",
    menuHeader: [],
    permission: [],
    permissionInfo: [],
    menuList: [],
    menuLeft: []
  },
  mutations: {
    //routing
    SET_ROUTERS: (state, routers) => {
      state.routers = routers;
    },
    //Current module (refer to the top menu bar)
    SET_CURRENT_MODULE: (state, currentModule) => {
      state.currentModule = currentModule;
    },
    //User rights
    SET_PERMISSIONS: (state, permissions) => {
      state.permissions = permissions;
    },
    //User permission information
    SET_PERMISSIONS_INFO: (state, permissionInfo) => {
      state.permissionInfo = permissionInfo;
    },
    //top menu bar
    SET_MENU_HEADER: (state, menuHeader) => {
      state.menuHeader = menuHeader;
    },
    //All menu list
    SET_MENU_LIST: (state, menuList) => {
      state.menuList = menuList;
    },
    //left menu list
    SET_MENU_LEFT: (state, menuLeft) => {
      state.menuLeft = menuLeft;
    }
  },
  actions: {
    GenerateRoutes({ commit, rootState }, data) {
      return new Promise(resolve => {
        const menuList = data.data.menu_list;
        const menuHeader = data.data.menu_header;
        const permission = data.data.permission;
        const permission_info = data.data.permission_info;
        const role = data.data.role_info;
        const superAdmin = role.indexOf("super_admin") >= 0 ? true : false;
        const moduleMenuList = "";

        //Initialize the default selected module
        commit("SET_CURRENT_MODULE", "home");
        //Head Menu Navigation
        commit("SET_MENU_HEADER", menuHeader);
        //menu list
        commit("SET_MENU_LIST", menuList);
        //permission list
        commit("SET_PERMISSIONS", permission);
        //permission information
        commit("SET_PERMISSIONS_INFO", permission_info);
        //left menu list

        //Judging if the top navigation bar is not enabled, use the default menu
        if (!this.state.setting.topNav) {
          commit("SET_MENU_LEFT", menuList);
        }

        //Cycle through the left submenu bar in the header menu bar
        for (let i = 0; i < permission_info.length; i++) {
          if (permission_info[i].url == data.data.path) {
            var string = permission_info[i].name.indexOf("/");
            commit(
              "SET_CURRENT_MODULE",
              permission_info[i].name.substring(0, string)
            );
          }
        }

        // Request routing data from the backend
        getRouters().then(res => {
          const rdata = JSON.parse(JSON.stringify(res.data));
          const rewriteRoutes = filterAsyncRouter(rdata, true);
          rewriteRoutes.push({ path: "*", redirect: "/404", hidden: true });
          resolve(rewriteRoutes);
        });
      });
    }
  }
};

// Traverse the routing string from the background and convert it to a component object
function filterAsyncRouter(asyncRouterMap, isRewrite = false) {
  return asyncRouterMap.filter(route => {
    if (isRewrite && route.children) {
      route.children = filterChildren(route.children);
    }
    if (route.component) {
      // Layout ParentView component special handling
      if (route.component === "Layout") {
        route.component = Layout;
      } else if (route.component === "ParentView") {
        route.component = ParentView;
      } else {
        route.component = loadView(route.component);
      }
    }
    if (route.path[0] != "/") {
      route.path = "/" + route.path;
    }
    if (route.children != null && route.children && route.children.length) {
      route.children = filterAsyncRouter(route.children, route, isRewrite);
    }
    return true;
  });
}

function filterChildren(childrenMap) {
  var children = [];
  childrenMap.forEach((el, index) => {
    if (el.children && el.children.length) {
      if (el.component === "ParentView") {
        el.children.forEach(c => {
          c.path = el.path + "/" + c.path;
          if (c.children && c.children.length) {
            children = children.concat(filterChildren(c.children, c));
            return;
          }
          children.push(c);
        });
        return;
      }
    }
    children = children.concat(el);
  });
  return children;
}

export const loadView = view => {
  // Routing lazy loading
  return resolve => require([`@/views/${view}`], resolve);
};

export default permission;
