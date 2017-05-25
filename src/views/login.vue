<template>
  <el-form :model="loginForm" :rules="rules" ref="loginForm" label-position="left" class="login-container">
    <h3 class="title">系统登录</h3>
    <el-form-item prop="account">
      <el-input type="text" v-model="loginForm.account" auto-complete="off" placeholder="账号"></el-input>
    </el-form-item>
    <el-form-item prop="password">
      <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="密码"></el-input>
    </el-form-item>
    <el-form-item v-if="checkCode">
      <el-col :span="12">
        <el-input type="code" v-model="loginForm.captach" auto-complete="off" placeholder="验证码"></el-input>
      </el-col>
      <el-col :span="12">
        <img class="captach" :src="captachSrc" title="看不清，点击刷新" @click="updateCaptach">
      </el-col>
    </el-form-item>
    <el-checkbox v-model="loginRemember">记住密码</el-checkbox>
    <el-form-item>
      <el-button id="login-btn" type="primary" @click.native.prevent="handleLogin" :loading="logining">登录</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import NProgress from 'nprogress';
import md5 from 'md5';
import {setMaxDigits, RSAKeyPair, encryptedString} from '../utils/Rsa.js';
import LoginService from '../services/LoginService';
import {setStore, getStore, removeStore} from '../utils/LocalStorage';
const loginServiceApi = new LoginService();
export default {
  data() {
    return {
      logining: false,
      checkCode: false,
      captachSrc: '/captach',
      loginForm: {
        account: '',
        password: '',
        captach: ''
      },
      rules: {
        account: [
          {
            required: true,
            message: '请输入账号',
            trigger: 'blur'
          }
        ],
        password: [
          {
            required: true,
            message: '请输入密码',
            trigger: 'blur'
          }
        ]
      },
      loginRemember: false
    };
  },
  mounted() {
    this.isLogin();
    this.initParam();
  },
  methods: {
    initParam() {
      if (localStorage.loginRemember === 'true') {
        this.loginRemember = true;
        this.loginForm.account = getStore('username');
        this.loginForm.password = getStore('password');
      } else {
        this.loginRemember = false;
      }
    },
    loginSuccess() {
      this.$router.push({
        path: '/welcome'
      });
    },
    isLogin() {
      loginServiceApi.isLogin().then(res => {
        if (res.ret === 0) {
          this.loginSuccess();
        } else {
          if (res.data.errcount > 3) {
            this.isShowCaptach(res.data.errcount);
          }
        }
      });
    },
    isShowCaptach(errorCount) {
      if (errorCount > 3) {
        this.checkCode = true;
      }
    },
    updateCaptach() {
      this.captachSrc = '/captach' + '?' + Math.random();
    },
    handleLogin() {
      var rsaN = 'D1F0795CC5A8D0BE60B83FF555F055CE7A44A0EC17BE365587A39696F76D4067BAE06D0026836E873CDAFC5409F1D53C6C931E6819D9B126516A5320CC30DB1B055B1AD9A2764814F8075659455A320006FD262F72B9AFE7B2778F2D20405CC9510BCE96137D56749BE169475DA6A6FAE4D955775988DFBE47A5E4B4E8494DD9C8658CE8A066D02B9C8165CD6E3C3CC803CA810B62529611A5AB87B9475322D490F9E47A1941BD8CEE395AACDC4A2E55964A03F7C575C036B79CD82490D404AE00F315A0700B8A1FE15C358FD071CDFB29761B78600ACD240DAFEB8516E1C8FF42C38C8E732DAC293FA202444FD218D5DC5DA5308D73363FCFD2EC1CF6D716EF';
      setMaxDigits(259); // 259 => n的十六进制位数/2+3
      var key = new RSAKeyPair('10001', '', rsaN); // 10001 => e的十六进制
      this.$refs.loginForm.validate((valid) => {
        if (valid) {
          this.logining = true;
          NProgress.start();
          let params = {
            username: this.loginForm.account,
            password: encryptedString(key, md5(this.loginForm.password)),
            captach: this.loginForm.captach
          };
          loginServiceApi.login(params).then(res => {
            this.logining = false;
            NProgress.done();
            if (res.ret !== 0) {
              this.isShowCaptach(res.data.errcount);
              this.$notify({
                title: '错误',
                message: res.msg,
                type: 'error'
              });
            } else {
              if (this.loginRemember) {
                setStore('loginRemember', true);
                setStore('username', this.loginForm.account);
                setStore('password', this.loginForm.password);
              } else {
                setStore('loginRemember', false);
                removeStore('username');
                removeStore('password');
              }
              setStore('user', res.data.username);
              this.loginSuccess();
            }
          });
        } else {
          this.$notify({
            title: '错误',
            message: '参数验证错误',
            type: 'error'
          });
        }
      });
    }
  }
};
</script>

<style type="scss" scoped>
.login-container {
  margin-bottom: 20px;
  background-color: #F9FAFC;
  margin: 180px auto;
  border: 2px solid #8492A6;
  width: 350px;
  padding: 35px 35px 15px 35px;
}

.login-container .title {
  margin: 0px auto 40px auto;
  text-align: center;
  color: #505458;
}

#login-btn {
  width: 100%;
}
</style>
