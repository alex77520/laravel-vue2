<template>
<div>
  <Breadcrumb :show='true' breadcrumb="话题列表"></Breadcrumb>

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
  import TopicService from '../../services/TopicService';
  import Breadcrumb from '../../components/breadcrumb';
  import Paginate from '../../components/paginate';
  import DataTableFilter from '../../components/dataTableFilter';
  import HelpButton from '../../components/helpButton';
  import DataTableElement from '../../components/dataTableElement';
  import TopicIcon from '../../components/scope/topic/topicIcon';
  import StatusTag from '../../components/scope/topic/statusTag';
  import LibraryBtn from '../../components/scope/topic/libraryBtn';
  import ActBtn from '../../components/scope/topic/actBtn';
  const TopicServiceApi = new TopicService();

  export default {
    components: {
      Paginate,
      Breadcrumb,
      DataTableFilter,
      HelpButton,
      DataTableElement,
      TopicIcon,
      StatusTag,
      LibraryBtn,
      ActBtn
    },
    methods: {
      dataReload(data) {
        this.loading = true;
        this.currentPage = data.currentPage;
        this.pageSize = data.pageSize;
        this.fetch(TopicServiceApi, this.fetchParams());
      },
      filterSubmit(data) {
        this.loading = true;
        this.currentPage = 1;
        this.fetch(TopicServiceApi, Object.assign(data, this.fetchParams()));
      },
      btnTrigger(callback) {
        this.disable = true;
        switch (callback) {
          case 'topicCreate':
            this.$router.push('/topicCreate');
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
        TopicServiceApi.update(params).then(res => {
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
        var params = {ids: [row.id], status: row.status};
        TopicServiceApi.update(params).then(res => {
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
      this.fetch(TopicServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        disable: false,
        filters: [
          {type: 'input', placeholder: '请输入话题名', class: 'filterClass', model: 'title'},
          {type: 'input', placeholder: '请输入前段id', class: 'filterClass', model: 'alias_id'},
          {type: 'select', placeholder: '请选择激活状态', class: 'filterClass', model: 'status', options: [{value: '0', label: '激活'}, {value: '3', label: '未激活'}]}
        ],
        helpButton: [
          {text: '新增', class: 'helpBtn', icon: 'plus', callback: 'topicCreate'},
          {text: '激活', class: 'helpBtn clear', callback: 'updateStatusBatch', status: 3, bus: true},
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
            {prop: 'title', label: '话题名', width: '120'},
            {prop: 'cate_parent_name', label: '一级分类', width: '100'},
            {prop: 'alias_id', label: '前端话题id', width: '160'},
            {prop: 'topic_img', label: '话题图标', width: '100', isScope: true, template: TopicIcon},
            {prop: 'status', label: '状态', width: '80', isScope: true, template: StatusTag},
            {prop: 'sort', label: '排序', width: '80'},
            {prop: 'libraryStatus', label: '题库', width: '80', isScope: true, template: LibraryBtn},
            {prop: 'user_nick', label: '操作人', width: '120'},
            {prop: 'updated_at', label: '操作时间', width: '180'},
            {prop: '', label: '操作', width: '200', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
