const getters = {
  //Application related status
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  maintain_switch: state => state.app.maintain_switch,
  simple_maintain_switch: state => state.app.simple_maintain_switch,
  register_switch: state => state.app.register_switch,

  //user related status
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  roles: state => state.user.roles,
  userId: state => state.user.userId,

  //Menu permission related status
  routers: state => state.permission.routers,
  asyncRouter: state => state.permission.asyncRouter,
  addRouters: state => state.permission.addRouters,
  currentModule: state => state.permission.currentModule,
  menuHeader: state => state.permission.menuHeader,
  menuList: state => state.permission.menuList,
  menuLeft: state => state.permission.menuLeft,
  permissions: state => state.permission.permission,
  permissionInfo: state => state.permission.permissionInfo,

  //Menu window related status
  allViews: state => state.tagsViews.allViews,
  cachedViews: state => state.tagsView.cachedViews,
  allViewsNames: state => state.tagsViews.allViewsNames,
  
  //subject related state
  theme: state => state.theme.theme,
  
  
}
export default getters
