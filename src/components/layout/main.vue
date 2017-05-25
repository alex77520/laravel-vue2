<template>
  <div id="main" class="layout-main">
    <LytHeader app-name="明朝互动"></LytHeader>
    <LytSider :menus="appMenus"></LytSider>
    <div class="layout-content">
      <slot></slot>
    </div>
    <LytFooter></LytFooter>
  </div>
</template>

<script type="text/babel">
import LytHeader from './header';
import LytSider from './sider';
import LytFooter from './footer';
import MainService from '../../services/MainService';
import {setStore} from '../../utils/LocalStorage';
const MainServiceApi = new MainService();
export default {
  name: 'Layout',
  data() {
    return {
      appMenus: []
    };
  },
  components: {
    LytHeader,
    LytFooter,
    LytSider
  },
  mounted() {
    MainServiceApi.getAuthList().then(res => {
      if (res.ret === 0) {
        this.appMenus = res.data.appMenus;
        setStore('aclList', res.data.permissionList);
      } else {
        this.$notify({message: res.msg});
      }
    });
  }
};
</script>

<style scoped>
.layout-content {
  background: none repeat scroll 0 0 #fff;
  position: absolute;
  top: 60px;
  left: 250px;
  right: 0;
  width: auto;
  padding: 20px;
  box-sizing: border-box;
}
</style>
