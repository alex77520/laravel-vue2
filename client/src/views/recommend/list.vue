<template>
  <div>
    <Breadcrumb :show='true' breadcrumb="热议配置"></Breadcrumb>
    <Data-Table-Filter
      :show="true"
      :filters="filters"
      @filter-submit="filterSubmit">
    </Data-Table-Filter>

    <Help-Button
      :show="true"
      :bus="bus"
      :help-buttons="helpButton"
      @btn-trigger="btnTrigger">
    </Help-Button>

    <Data-Table-Element
      :show="true"
      :bus="bus"
      :loading="loading"
      :ele-table="eleTable"
      :table-data="tableData">
    </Data-Table-Element>

    <Paginate
      :show=true
      :total="total"
      :pageSize="pageSize"
      @handle-size-change="dataReload"
      @handle-current-change="dataReload">
    </Paginate>
  </div>
</template>

<script type="text/babel">
  import RecommendService from '../../services/RecommendService';
  import Paginate from '../../components/paginate';
  import Breadcrumb from '../../components/breadcrumb';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import StatusTag from '../../components/scope/recommend/statusTag';
  import ActBtn from '../../components/scope/recommend/actBtn';
  const RecommendServiceApi = new RecommendService();
  export default {
    components: {
      Paginate,
      Breadcrumb,
      DataTableFilter,
      HelpButton,
      DataTableElement,
      StatusTag,
      ActBtn
    },
    methods: {
      dataReload(data) {
        this.loading = true;
        this.currentPage = data.currentPage;
        this.pageSize = data.pageSize;
        this.fetch(RecommendServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(RecommendServiceApi, Object.assign(data, this.fetchParams()));
      },
      btnTrigger(callback) {
        this.disable = true;
        switch (callback) {
          case 'recommendCreate':
            this.$router.push('/recommendCreate');
            break;
          default:
            break;
        }
      },
      updateStatusBatch(selections, status) {
        var selectedIds = [];
        selections.forEach(function(element, index) {
          selectedIds.push(element.id);
        });
        var params = {ids: selectedIds, status: status};
        RecommendServiceApi.update(params).then(res => {
          if (res.ret === 0) {  // 全部更新成功
            selections.forEach(item => this.$set(item, 'status', res.data.updateStatus));
            this.$notify({message: '更新成功', type: 'success'});
          } else if (res.ret === 1) { // 全部更新失败
            this.$notify({message: '更新失败', type: 'error'});
          } else if (res.ret === 2) { // 部分更新成功
            selections.forEach(item => {
              if (res.data.successedIds.includes(parseInt(item.id))) {
                this.$set(item, 'status', res.data.updateStatus);
              }
            });
            this.$notify({message: '部分更新成功', type: 'warning'});
          }
        });
      },
      updateStatusRow(row, status) {
        var params = {ids: [row.id], status: status};
        RecommendServiceApi.update(params).then(res => {
          if (res.ret === 0) {
            row.status = res.data.updateStatus;
            this.$notify({message: 'update Success', type: 'success'});
          } else {
            this.$notify({message: 'update failed', type: 'error'});
          }
        });
      }
    },
    mounted() {
      this.fetch(RecommendServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        filters: [
          {type: 'select', placeholder: '请选择热议类别', class: 'filterClass', model: 'type', options: [{value: 1, label: '话题'}, {value: 2, label: '讨论'}]},
          {type: 'input', placeholder: '请输入话题或讨论ID', class: 'filterClass', model: 'recommend_id'},
          {type: 'select', placeholder: '请选择激活状态', class: 'filterClass', model: 'status', options: [{value: 1, label: '激活'}, {value: 0, label: '未激活'}]}
        ],
        helpButton: [
          {text: '新增', class: 'helpBtn', icon: 'plus', callback: 'recommendCreate'},
          {text: '激活', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 1, bus: true},
          {text: '取消激活', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 0, bus: true},
          {text: '导出', class: 'helpBtn clear right', callback: 'export'}
        ],
        eleTable: {
          loadingText: '加载中',
          isBorder: true,
          style: 'width: 100%',
          isType: true,
          typeValue: 'selection',
          columns: [
            {prop: 'recommentType', label: '热议类别', width: '100', showOverflowTooltip: true},
            {prop: 'recommend_id', label: '热议ID', width: '200', showOverflowTooltip: true},
            {prop: 'title', label: '标题', width: '200'},
            {prop: 'description', label: '描述', width: '320', showOverflowTooltip: true},
            {prop: 'status', label: '状态', width: '100', isScope: true, template: StatusTag},
            {prop: 'sort', label: '排序', width: '100'},
            {prop: 'updated_at', label: '编辑时间', width: '180'},
            {prop: '', label: '操作', width: '200', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
