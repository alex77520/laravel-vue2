import BaseService from './BaseService';

export default class MainService extends BaseService {
  getAuthList() {
    return this.getApi('/admin/getAuth');
  }
}
