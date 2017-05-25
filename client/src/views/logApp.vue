<template>
  <div class="log-app">
    <el-form :inline="true" :model="formInline">
      <el-form-item label="项目id">
        <el-input v-model="addLogAppModel.app_id"></el-input>
      </el-form-item>
      <el-form-item label="项目名">
        <el-input v-model="addLogAppModel.app_name"></el-input>
      </el-form-item>
       <el-form-item label="项目key">
        <el-input v-model="addLogAppModel.app_key"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="addLogApp(addLogAppModel)">新增</el-button>
      </el-form-item>
    </el-form>
    <el-table :data="logAppLists" border style="width: 100%">
      <el-table-column prop="app_id" label="项目id" width="180"></el-table-column>
      <el-table-column prop="app_name" label="项目名" width="180"></el-table-column>
      <el-table-column prop="app_key" label="项目key"></el-table-column>
      <el-table-column fixed="right" label="操作" width="200">
        <template scope="scope">
          <el-button @click="handleEdit(scope.$index, scope.row)" size="small">
            编辑
          </el-button>
          <el-button v-if="scope.row.status" @click="handelRemove(scope.$index, scope.row)" type="danger" size="small">
            禁用
          </el-button>
          <el-button v-else @click="handelActive(scope.$index, scope.row)" type="success" size="small">
            启用
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog title="项目配置" v-model="editLogAppDialog">
      <el-form :model="logAppModel">
        <el-form-item label="项目id" :label-width="formLabelWidth">
          <el-input v-model="logAppModel.app_id" :disabled="true"></el-input>
        </el-form-item>
        <el-form-item label="项目名" :label-width="formLabelWidth">
          <el-input v-model="logAppModel.app_name"></el-input>
        </el-form-item>
        <el-form-item label="项目key" :label-width="formLabelWidth">
          <el-input v-model="logAppModel.app_key"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editLogAppDialog = false">取 消</el-button>
        <el-button type="primary" @click="updateLogApp(logAppModel)">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script type="text/babel">
import LytMain from '../components/layout/main';
import LogAppService from '../services/LogAppService';
const LogAppServiceApi = new LogAppService();
export default {
  components: {
    LytMain
  },
  data() {
    return {
      logAppLists: [],
      addLogAppModel: {
        app_id: '',
        app_name: '',
        app_key: ''
      },
      logAppModel: {
        app_id: '',
        app_name: '',
        app_key: ''
      },
      editLogAppDialog: false
    };
  },
  mounted() {
    this.getLogAppList();
  },
  methods: {
    getLogAppList() {
      LogAppServiceApi.getLogAppList().then(res => {
        if (res.ret === 0) {
          this.logAppLists = res.data;
        } else {
          this.logAppLists = [];
        }
      });
    },
    handleEdit(index, row) {
      this.logAppModel = JSON.parse(JSON.stringify(row));
      this.editLogAppDialog = true;
    },
    handelRemove(index, row) {
      this.removeRow = row;
      this.$confirm('禁用项目' + row.app_name + ', 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        var logApp = {
          status: 0
        };
        LogAppServiceApi.updateLogApp(this.removeRow.id, logApp).then(res => {
          this.$notify({message: res.msg});
          if (res.ret === 0) {
            this.getLogAppList();
          }
        });
      });
    },
    handelActive(index, row) {
      this.activeRow = row;
      this.$confirm('启用项目' + row.app_name + ', 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        var logApp = {
          status: 1
        };
        LogAppServiceApi.updateLogApp(this.activeRow.id, logApp).then(res => {
          this.$notify({message: res.msg});
          if (res.ret === 0) {
            this.getLogAppList();
          }
        });
      });
    },
    addLogApp(logApp) {
      LogAppServiceApi.addLogApp(logApp).then(res => {
        this.$notify({message: res.msg});
        if (res.ret === 0) {
          this.getLogAppList();
          this.addLogAppModel = {};
        }
      });
    },
    updateLogApp(logApp) {
      var data = {
        app_name: logApp.app_name,
        app_key: logApp.app_key
      };
      LogAppServiceApi.updateLogApp(logApp.id, data).then(res => {
        this.$notify({message: res.msg});
        if (res.ret === 0) {
          this.editLogAppDialog = false;
          this.getLogAppList();
        }
      });
    }
  }
};
</script>
