import request from '@/utils/request'
export function getWebsiteList(params) {
  return request({
    url: '/website/website_module/configurator/list',
    method: 'get',
    params: params
  })
}

export function createWebsite(params) {
  return request({
    url: '/website/website_module/configurator/store',
    method: 'post',
    data: params
  })
}

export function editWebsite(id) {
  return request({
    url: '/website/website_module/configurator/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateWebsite(id, params) {
  return request({
    url: '/website/website_module/configurator/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteWebsite(id) {
  return request({
    url: '/website/website_module/configurator/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



