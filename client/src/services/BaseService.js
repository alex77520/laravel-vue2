import axios from 'axios';

/**
 * 统一处理请求的状态码、错误、数据埋点
 */
export default class BaseService {
  constructor() {
    this.Request = axios.create({});
  }

  throwReqError(resp) {
    const error = new Error(resp.statusText);
    error.resp = resp;
    throw error;
  }

  checkStatus(resp) {
    if ((resp.status >= 200) && (resp.status < 300)) {
      return resp.data;
    }
    this.throwReqError(resp);
  }

  procReqError(err) {
    return err;
  }

  procRequest(req) {
    return req.then(this.checkStatus).catch(this.procReqError);
  }

  getApi(url, data) {
    return this.procRequest(this.Request.get(url, {
      params: data
    }));
  }

  postApi(url, data) {
    return this.procRequest(this.Request.post(url, data));
  }

  putApi(url, data) {
    return this.procRequest(this.Request.put(url, data));
  }

  deleteApi(url) {
    return this.procRequest(this.Request.delete(url));
  }
}
