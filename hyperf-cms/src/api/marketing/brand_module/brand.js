import request from '@/utils/request'
export function getBrandList(params) {
  return request({
    url: '/marketing/brand_module/brand/list',
    method: 'get',
    params: params
  })
}

export function createBrand(params) {
  return request({
    url: '/marketing/brand_module/brand/store',
    method: 'post',
    data: params
  })
}

export function editBrand(id) {
  return request({
    url: '/marketing/brand_module/brand/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateBrand(id, params) {
  return request({
    url: '/marketing/brand_module/brand/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteBrand(id) {
  return request({
    url: '/marketing/brand_module/brand/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



