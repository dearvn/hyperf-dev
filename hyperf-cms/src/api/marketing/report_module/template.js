import request from '@/utils/request'
export function getTemplateList(params) {
  return request({
    url: '/marketing/email_module/template/list',
    method: 'get',
    params: params
  })
}

export function createTemplate(params) {
  return request({
    url: '/marketing/email_module/template/store',
    method: 'post',
    data: params
  })
}

export function editTemplate(id) {
  return request({
    url: '/marketing/email_module/template/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateTemplate(id, params) {
  return request({
    url: '/marketing/email_module/template/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteTemplate(id) {
  return request({
    url: '/marketing/email_module/template/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



