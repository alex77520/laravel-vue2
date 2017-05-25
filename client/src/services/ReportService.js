import BaseService from './BaseService';

export default class ReportService extends BaseService {
  fetch(params) {
    return this.getApi('/report/get', params);
  }

  update(params) {
    return this.postApi('/report/post', params);
  }
}
