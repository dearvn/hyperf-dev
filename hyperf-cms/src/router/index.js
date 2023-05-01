import Vue from "vue";
import Router from "vue-router";

/**
 * Push method of rewriting the route level
 */
const routerPush = Router.prototype.push;
Router.prototype.push = function push(location) {
  return routerPush.call(this, location).catch(error => error);
};
import Layout from "../views/layout/Layout";

Vue.use(Router);

/**
 * Note: Routing configuration items
 *
 * hidden: true //When set to true, the route will not appear in the sidebar such as 401, login and other pages, or such as some edit pages /edit/1
 * alwaysShow: true //When you have more than one route declared by children under a route, it will automatically become a nested mode -such as a component page
 *                                //When there is only one, that sub-route will be displayed in the sidebar as the root route--such as the boot page
 *                                //If you want to display your root route regardless of the number of children declared under the route
 *                                //You can set alwaysShow: true so that it will ignore the previously defined rules and always show the root route
 * redirect: noRedirect //When noRedirect is set, the route cannot be clicked in the breadcrumb navigation
 * name:'router-name' //Set the name of the route, it must be filled in otherwise there will be various problems when using <keep-alive>
 * meta : {
    noCache: true //If set to true, it will not be cached by <keep-alive> (default false)
    title: 'title' //Set the name of the route displayed in the sidebar and breadcrumbs
    icon: 'svg-name' //Set the icon of this route, corresponding to the path src/assets/icons/svg
    breadcrumb: false //If set to false, it will not be displayed in breadcrumb breadcrumbs
  }
 */
export const constantRouterMap = [
  {
    path: "/login",
    component: () => import("@/views/login/index"),
    hidden: true
  },
  {
    path: "/register",
    component: () => import("@/views/login/register"),
    hidden: true
  },

  {
    path: "",
    component: Layout,
    redirect: "/home",
    children: [
      {
        path: "home",
        name: "home",
        component: () => import("@/views/home/index"),
        meta: { title: "Dash Board", icon: "home" }
      },
      {
        path: "navigation",
        name: "navigation",
        component: () => import("@/views/common/navigation"),
        meta: { title: "Home", icon: "home" }
      },
      {
        path: "profile",
        component: () => import("@/views/setting/user_module/user/profile/index"),
        name: "Profile",
        meta: { title: "Profile", icon: "user" }
      },
      {
        path: "401",
        name: "401",
        meta: { title: "404 Not Found", icon: "home" },
        component: () => import("@/views/error/401"),
        hidden: true
      },

      {
        path: "404",
        name: "404",
        meta: { title: "404 Not Found", icon: "home" },
        component: () => import("@/views/error/404"),
        hidden: true
      },
    ]
  }
];

export default new Router({
  // mode: "history", //backend support can be opened
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});
