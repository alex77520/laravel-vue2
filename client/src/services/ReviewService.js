import BaseService from './BaseService';

export default class ReviewService extends BaseService {
  fetch(params) {
    return this.getApi('/review/get', params);
  }
  update(params) {
    return this.postApi('/review/update', params);
  }
}
