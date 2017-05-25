<template>
<div>
  <Breadcrumb :show='true' breadcrumb="错题举报列表"></Breadcrumb>

  <Data-Table-Filter
    :show="true"
    :filters="filters"
    @filter-submit="filterSubmit">
  </Data-Table-Filter>

  <Help-Button
    :show="true"
    :bus="bus"
    :disable="disable"
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
  import QuestionReportService from '../../services/QuestionReportService';
  import Breadcrumb from '../../components/breadcrumb';
  import Paginate from '../../components/paginate';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import StatusTag from '../../components/scope/questionReport/statusTag';
  import ActBtn from '../../components/scope/questionReport/actBtn';
  const QuestionReportServiceApi = new QuestionReportService();

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
        this.fetch(QuestionReportServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(QuestionReportServiceApi, Object.assign(data, this.fetchParams()));
      },
      btnTrigger(callback) {
        this.disable = true;
        switch (callback) {
          case '':
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
        QuestionReportServiceApi.update(params).then(res => {
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
        QuestionReportServiceApi.update(params).then(res => {
          if (res.ret === 0) {
            row.status = res.data.updateStatus;
            this.$notify({message: 'update Success', type: 'success'});
          } else {
            this.$notify({message: 'update failed', type: 'error'});
          }
        });
      }
    },
    mounted: function() {
      this.fetch(QuestionReportServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        disable: false,
        filters: [
          {type: 'input', placeholder: '请输入问题', class: 'filterClass', model: 'question'},
          {type: 'input', placeholder: '请输入答案关键词', class: 'filterClass', model: 'answer'},
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
            {prop: 'topic_title', label: '所属话题', width: '100', fixed: 'left'},
            {prop: 'topic_id', label: '话题ID', width: '120'},
            {prop: 'question_id', label: '题目ID', width: '160'},
            {prop: 'question', label: '问题', width: '200', showOverflowTooltip: true},
            {prop: 'right_answer', label: '正确答案', width: '150', showOverflowTooltip: true},
            {prop: 'total_answer', label: '全部答案', width: '250', showOverflowTooltip: true},
            {prop: 'status', label: '审核状态', width: '100', isScope: true, template: StatusTag},
            {prop: 'reason', label: '原因', width: '120', showOverflowTooltip: true},
            {prop: 'user_id', label: '举报人ID', width: '150', showOverflowTooltip: true},
            {prop: 'user_nick', label: '举报人', width: '120'},
            {prop: 'created_at', label: '举报时间', width: '180'},
            {prop: '', label: '操作', width: '200', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
