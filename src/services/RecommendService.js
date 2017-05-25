import BaseService from './BaseService';

export default class RecommendService extends BaseService {
  fetch(params) {
    return this.getApi('/recommend/get', params);
  }
  update(params) {
    return this.postApi('/recommend/update', params);
  }

  create(params) {
    return this.getApi('/recommend/create', params);
  }
  store(params) {
    return this.postApi('/recommend/store', params);
  }
}
