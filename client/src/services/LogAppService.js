import BaseService from './BaseService';

export default class LogAppService extends BaseService {
  getLogAppList() {
    return this.getApi('/logApp');
  }

  addLogApp(data) {
    return this.postApi('/logApp/', data);
  }

  updateLogApp(id, data) {
    return this.putApi('/logApp/' + id, data);
  }

  deleteLogApp(id) {
    return this.deleteApi('/logApp/' + id);
  }
}
