import BaseService from './BaseService';

export default class TopicService extends BaseService {
  fetch(params) {
    return this.getApi('/topic/get', params);
  }
  update(params) {
    return this.postApi('/topic/update', params);
  }
  // form(params) {
  //   return this.get('/topic/form', params);
  // }
  create(params) {
    return this.getApi('/topic/create', params);
  }
  store(params) {
    return this.postApi('/topic/store', params);
  }

  upload(params) {
    return this.postApi('/topic/upload', params);
  }

  edit(params) {
    return this.postApi('/topic/store', params);
  }
}
