import request from '@/utils/request'
export function getThemeList(params) {
  return request({
    url: '/website/landingpage_module/theme/list',
    method: 'get',
    params: params
  })
}

export function createTheme(params) {
  return request({
    url: '/website/landingpage_module/theme/store',
    method: 'post',
    data: params
  })
}

export function editTheme(id) {
  return request({
    url: '/website/landingpage_module/theme/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateTheme(id, params) {
  return request({
    url: '/website/landingpage_module/theme/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteTheme(id) {
  return request({
    url: '/website/landingpage_module/theme/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



