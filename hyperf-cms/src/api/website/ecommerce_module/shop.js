import request from '@/utils/request'
export function getShopList(params) {
  return request({
    url: '/website/shop_module/store/list',
    method: 'get',
    params: params
  })
}

export function createShop(params) {
  return request({
    url: '/website/shop_module/store/store',
    method: 'post',
    data: params
  })
}

export function editShop(id) {
  return request({
    url: '/website/shop_module/store/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateShop(id, params) {
  return request({
    url: '/website/shop_module/store/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteShop(id) {
  return request({
    url: '/website/shop_module/store/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



