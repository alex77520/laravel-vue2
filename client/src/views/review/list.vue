<template>
  <div>
    <Breadcrumb :show='true' breadcrumb="内容审核"></Breadcrumb>
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
  import ReviewService from '../../services/ReviewService';
  import Paginate from '../../components/paginate';
  import Breadcrumb from '../../components/breadcrumb';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import StatusTag from '../../components/scope/review/statusTag';
  import ActBtn from '../../components/scope/review/actBtn';
  const ReviewServiceApi = new ReviewService();
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
        this.fetch(ReviewServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(ReviewServiceApi, Object.assign(data, this.fetchParams()));
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
        ReviewServiceApi.update(params).then(res => {
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
        ReviewServiceApi.update(params).then(res => {
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
      this.fetch(ReviewServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        filters: [
          {type: 'select', placeholder: '请选择审核类别', class: 'filterClass', model: 'type', options: [{value: '1', label: '讨论'}, {value: '2', label: '观点'}]},
          {type: 'input', placeholder: '请输入审核内容ID', class: 'filterClass', model: 'review_id'},
          {type: 'select', placeholder: '请选择审核状态', class: 'filterClass', model: 'status', options: [{value: '0', label: '审核中'}, {value: '1', label: '审核通过'}, {value: '2', label: '审核未通过'}]}
        ],
        helpButton: [
          {text: '审核通过', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 1, bus: true},
          {text: '审核不通过', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 2, bus: true},
          {text: '导出', class: 'helpBtn clear right', callback: 'export'}
        ],
        eleTable: {
          loadingText: '加载中',
          isBorder: true,
          style: 'width: 100%',
          isType: true,
          typeValue: 'selection',
          columns: [
            {prop: 'displayType', label: '审核类别', width: '100', showOverflowTooltip: true, fixed: 'left'},
            {prop: 'review_id', label: '审核ID', width: '180', showOverflowTooltip: true},
            {prop: 'user_id', label: '用户ID', width: '150', showOverflowTooltip: true},
            {prop: 'user_nick', label: '用户名', width: '120', showOverflowTooltip: true},
            {prop: 'title', label: '讨论标题', width: '180', showOverflowTooltip: true},
            {prop: 'description', label: '内容', width: '300', showOverflowTooltip: true},
            {prop: 'status', label: '审核状态', width: '100', isScope: true, template: StatusTag},
            {prop: 'view_a', label: 'A观点', width: '180', showOverflowTooltip: true},
            {prop: 'view_b', label: 'B观点', width: '180', showOverflowTooltip: true},
            {prop: 'created_at', label: '创建时间', width: '180'},
            {prop: '', label: '操作', width: '120', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
