import request from '@/utils/request'
export function getDomainList(params) {
  return request({
    url: '/website/domain_module/domain/list',
    method: 'get',
    params: params
  })
}

export function createDomain(params) {
  return request({
    url: '/website/domain_module/domain/store',
    method: 'post',
    data: params
  })
}

export function editDomain(id) {
  return request({
    url: '/website/domain_module/domain/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateDomain(id, params) {
  return request({
    url: '/website/domain_module/domain/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteDomain(id) {
  return request({
    url: '/website/domain_module/domain/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



