import request from '@/utils/request'
export function getChatList(params) {
  return request({
    url: '/website/chat_module/web/list',
    method: 'get',
    params: params
  })
}

export function createChat(params) {
  return request({
    url: '/website/chat_module/web/store',
    method: 'post',
    data: params
  })
}

export function editChat(id) {
  return request({
    url: '/website/chat_module/web/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateChat(id, params) {
  return request({
    url: '/website/chat_module/web/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteChat(id) {
  return request({
    url: '/website/chat_module/web/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



