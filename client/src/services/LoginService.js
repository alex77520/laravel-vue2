import BaseService from './BaseService';

export default class LoginService extends BaseService {
  login(params) {
    return this.postApi('/admin/login', params);
  }

  isLogin() {
    return this.getApi('/admin/isLogin');
  }

  logout() {
    return this.getApi('/admin/logout');
  }
}
