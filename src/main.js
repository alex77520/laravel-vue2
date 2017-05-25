import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './router/router';
import VueX from 'vuex';
import App from './app.vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';
import 'normalize.css';

Vue.use(ElementUI);
Vue.use(VueX);
Vue.use(VueRouter);

var bus = new Vue();
const router = new VueRouter({
  mode: 'hash',
  base: __dirname,
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || {
      x: 0,
      y: 0
    };
  }
});

Vue.mixin({
  methods: {
    fetchParams: function() {
      return {
        page: this.currentPage,
        pageSize: this.pageSize
      };
    },
    fetch: function(serverApi, params) {
      serverApi.fetch(params).then(res => {
        if (res.ret === 0) {
          this.tableData = res.data.tableData;
          this.total = res.data.total;
          this.pageSize = res.data.pageSize;
          this.loading = false;
        } else if (res.ret === 400) {
          this.$router.push('/login');
          this.$notify({message: res.msg, type: 'warning'});
        } else {
          this.$notify({message: '查询失败', type: 'error'});
        }
      });
    },
    update: function(serverApi, params, callbackParams, callback) {
      serverApi.update(params).then(res => {
        callback(res, params, callbackParams);
      });
    }
  },
  data: function() {
    return {
      tableData: [],
      loading: true,
      total: 30,
      pageSize: 30,
      page: 1,
      bus: bus
    };
  }
});

new Vue({
  router,
  ...App
}).$mount('#app');
