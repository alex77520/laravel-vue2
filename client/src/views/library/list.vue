<template>
<div>
  <Breadcrumb :show='true' breadcrumb="题库列表"></Breadcrumb>

  <Upload :show="true" :dialogVisible="dialogVisible"></Upload>

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
  import LibraryService from '../../services/LibraryService';
  import Breadcrumb from '../../components/breadcrumb';
  import Paginate from '../../components/paginate';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import StatusTag from '../../components/scope/library/statusTag';
  import ActBtn from '../../components/scope/library/actBtn';
  import Upload from '../../components/scope/library/upload';
  const LibraryServiceApi = new LibraryService();

  export default {
    components: {
      Paginate,
      Breadcrumb,
      DataTableFilter,
      HelpButton,
      DataTableElement,
      StatusTag,
      ActBtn,
      Upload
    },
    methods: {
      dataReload(data) {
        this.loading = true;
        this.currentPage = data.currentPage;
        this.pageSize = data.pageSize;
        this.fetch(LibraryServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(LibraryServiceApi, Object.assign(data, this.fetchParams()));
      },
      btnTrigger(callback) {
        this.disable = true;
        switch (callback) {
          case 'libraryImport':
            this.dialogVisible = true;
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
        LibraryServiceApi.update(params).then(res => {
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
        LibraryServiceApi.update(params).then(res => {
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
      var data = {};
      if (this.$route.params.id !== 'undefined') {
        data['id'] = this.$route.params.id;
      }
      this.fetch(LibraryServiceApi, Object.assign(data, this.fetchParams()));
    },
    data: function() {
      return {
        dialogVisible: false,
        tableData: [],
        filters: [
          {type: 'input', placeholder: '请输入话题名', class: 'filterClass', model: 'title'},
          {type: 'input', placeholder: '请输入答案关键词', class: 'filterClass', model: 'answer'},
          {type: 'input', placeholder: '请输入问题', class: 'filterClass', model: 'question'},
          {type: 'select', placeholder: '请选择激活状态', class: 'filterClass', model: 'status', options: [{value: 1, label: '激活'}, {value: 2, label: '未激活'}]}
        ],
        helpButton: [
          {text: '导入', class: 'helpBtn', icon: 'plus', callback: 'libraryImport'},
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
            {prop: 'topic_title', label: '所属话题', width: '150'},
            {prop: 'question', label: '问题', width: '300', showOverflowTooltip: true},
            {prop: 'right_answer', label: '正确答案', width: '150', showOverflowTooltip: true},
            {prop: 'total_answer', label: '全部答案', width: '300', showOverflowTooltip: true},
            {prop: 'status', label: '状态', width: '80', isScope: true, template: StatusTag},
            {prop: 'right_rate', label: '正确率', width: '80'},
            {prop: '创建者', label: '操作人', width: '120'},
            {prop: 'updated_at', label: '操作时间', width: '180'},
            {prop: '', label: '操作', width: '200', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
