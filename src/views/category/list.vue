<template>
  <div>
    <Breadcrumb :show='true' breadcrumb="话题分类"></Breadcrumb>
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
  import TopicCategoryService from '../../services/TopicCategoryService';
  import Paginate from '../../components/paginate';
  import Breadcrumb from '../../components/breadcrumb';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import StatusTag from '../../components/scope/category/statusTag';
  import ActBtn from '../../components/scope/category/actBtn';
  const TopicCategoryServiceApi = new TopicCategoryService();

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
        this.fetch(TopicCategoryServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(TopicCategoryServiceApi, Object.assign(data, this.fetchParams()));
      },
      btnTrigger(callback) {
        this.disable = true;
        switch (callback) {
          case 'topicCategoryCreate':
            this.$router.push('/topicCategoryCreate');
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
        alert(status);
        var params = {ids: selectedIds, status: status};
        TopicCategoryServiceApi.update(params).then(res => {
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
        TopicCategoryServiceApi.update(params).then(res => {
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
      this.fetch(TopicCategoryServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        filters: [
          {type: 'select', placeholder: '请选择分类等级', class: 'filterClass', model: 'categoryLevel', options: [{value: 1, label: '一级分类'}, {value: 2, label: '二级分类'}]},
          {type: 'input', placeholder: '请输入分类名', class: 'filterClass', model: 'categoryName'},
          {type: 'select', placeholder: '请选择激活状态', class: 'filterClass', model: 'status', options: [{value: 1, label: '激活'}, {value: 2, label: '未激活'}]}
        ],
        helpButton: [
          {text: '新增', class: 'helpBtn', icon: 'plus', callback: 'topicCategoryCreate'},
          {text: '激活', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 1, bus: true},
          {text: '取消激活', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 2, bus: true},
          {text: '导出', class: 'helpBtn clear right', callback: 'export'}
        ],
        eleTable: {
          loadingText: '加载中',
          isBorder: true,
          style: 'width: 100%',
          isType: true,
          typeValue: 'selection',
          columns: [
            {prop: 'parent_cate_id', label: '父分类ID'},
            {prop: 'parent_cate_name', label: '父分类名', showOverflowTooltip: true},
            {prop: 'cate_id', label: '分类ID', showOverflowTooltip: true},
            {prop: 'cate_name', label: '分类名', showOverflowTooltip: true},
            {prop: 'status', label: '状态', isScope: true, template: StatusTag},
            {prop: 'sort', label: '排序'},
            {prop: 'updated_at', label: '操作时间'},
            {prop: '', label: '操作', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
