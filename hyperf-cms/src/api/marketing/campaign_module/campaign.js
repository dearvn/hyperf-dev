import request from '@/utils/request'
export function getCampaignList(params) {
  return request({
    url: '/marketing/campaign_module/campaign/list',
    method: 'get',
    params: params
  })
}

export function createCampaign(params) {
  return request({
    url: '/marketing/campaign_module/campaign/store',
    method: 'post',
    data: params
  })
}

export function editCampaign(id) {
  return request({
    url: '/marketing/campaign_module/campaign/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateCampaign(id, params) {
  return request({
    url: '/marketing/campaign_module/campaign/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteCampaign(id) {
  return request({
    url: '/marketing/campaign_module/campaign/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



