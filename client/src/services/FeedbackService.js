import BaseService from './BaseService';

export default class FeedbackService extends BaseService {
  fetch(params) {
    return this.getApi('/feedback/get', params);
  }
}
