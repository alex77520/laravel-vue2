<template>
  <div class="layout-sider">
    <el-menu default-active="2" theme="dark" router>
      <el-menu-item v-for="appMenu in appMenus" :key="appMenu.auth" :index="appMenu.path">
        {{appMenu.menu}}
      </el-menu-item>
    </el-menu>
  </div>
</template>

<script type="text/babel">
export default {
  props: [
    'menus',
    'appName'
  ],
  data() {
    return {
      activeIndex: '1',
      appMenus: this.menus
    };
  },
  watch: {
    menus(val) {
      this.appMenus = val;
    }
  }
};
</script>
<style scoped>
.layout-sider {
  display: block;
  position: absolute;
  width: 250px;
  left: 0;
  top: 60px;
  bottom: 0;
  background: #2e363f;
}
</style>
