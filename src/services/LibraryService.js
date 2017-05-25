import BaseService from './BaseService';

export default class LibraryService extends BaseService {
  fetch(params) {
    return this.getApi('/library/get', params);
  }
  update(params) {
    return this.postApi('/library/update', params);
  }
  create(params) {
    return this.getApi('/library/create', params);
  }

  store(params) {
    return this.postApi('/library/store', params);
  }

  // upload(params) {
  //   return this.postApi('/library/upload', params);
  // }
}
