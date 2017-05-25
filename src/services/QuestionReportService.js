import BaseService from './BaseService';

export default class QuestionReportService extends BaseService {
  fetch(params) {
    return this.getApi('/questionReport/get', params);
  }
  update(params) {
    return this.postApi('/questionReport/update', params);
  }

  create(params) {
    return this.getApi('/questionReport/create', params);
  }
  store(params) {
    return this.postApi('/questionReport/store', params);
  }
}
