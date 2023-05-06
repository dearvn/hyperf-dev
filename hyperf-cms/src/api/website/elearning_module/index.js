import request from '@/utils/request'
export function getCourseList(params) {
  return request({
    url: '/website/elearning_module/course/list',
    method: 'get',
    params: params
  })
}

export function createCourse(params) {
  return request({
    url: '/website/elearning_module/course/store',
    method: 'post',
    data: params
  })
}

export function editCourse(id) {
  return request({
    url: '/website/elearning_module/course/edit/' + id,
    method: 'get',
    params: ''
  })
}

export function updateCourse(id, params) {
  return request({
    url: '/website/elearning_module/course/update' + '/' + id,
    method: 'put',
    data: params
  })
}

export function deleteCourse(id) {
  return request({
    url: '/website/elearning_module/course/destroy' + '/' + id,
    method: 'delete',
    data: id,
  })
}



