import BaseService from './BaseService';

export default class TopicCategoryService extends BaseService {
  fetch(params) {
    return this.getApi('/topicCategory/get', params);
  }
  update(params) {
    return this.postApi('/topicCategory/update', params);
  }
  // form(params) {
  //   return this.get('/topic/form', params);
  // }
  create(params) {
    return this.getApi('/topicCategory/create', params);
  }
  store(params) {
    return this.postApi('/topicCategory/store', params);
  }

  // upload(params) {
  //   return this.postApi('/topicCategory/upload', params);
  // }

  // edit(params) {
  //   return this.postApi('/topicCategory/store', params);
  // }
}
