<template>
  <el-col :span="24" class="header">
    <el-col :span="20" class="logo">
      <img src="../../assets/images/logo.png" />
      <span>{{appName}}</span>
    </el-col>
    <el-col :span="4" class="header-right">
      <span id="userinfo">{{sysUserName}}</span>
      <el-button id="logout-btn" type="text" @click.native="logout">退出</el-button>
    </el-col>
  </el-col>
</template>

<script type="text/babel">
import LoginService from '../../services/LoginService';
import {getStore} from '../../utils/LocalStorage';
const loginServiceApi = new LoginService();
export default {
  props: [
    'appName'
  ],
  data() {
    return {
      sysUserName: 'mingchao'
    };
  },
  mounted() {
    this.sysUserName = getStore('user') || '';
  },
  methods: {
    logout: function() {
      loginServiceApi.logout().then(res => {
        if (res.ret === 0) {
          sessionStorage.clear();
          this.$router.push({
            path: '/login'
          });
        }
      });
    }
  }
};
</script>

<style type="scss" scoped>
.header {
  height: 60px;
  line-height: 60px;
  background: #1f2d3d;
  color: #c0ccda;
}

.header-right {
  text-align: right;
  padding-right: 35px;
}

#userinfo {
  margin-right: 20px;
}

.logo {
  font-size: 24px;
}

.logo img {
  width: 40px;
  float: left;
  margin: 10px 10px 10px 18px;
}

#logout-btn {
  color: #c0ccda;
}
</style>
