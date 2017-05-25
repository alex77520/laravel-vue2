<template>
  <div>
    <Breadcrumb :show='true' breadcrumb="反馈列表"></Breadcrumb>

     <Data-Table-Element
      :show="true"
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
  import FeedbackService from '../services/FeedbackService';
  import Paginate from '../components/paginate';
  import Breadcrumb from '../components/breadcrumb';
  import DataTableElement from '../components/dataTableElement';

  const FeedbackServiceApi = new FeedbackService();
  export default {
    components: {
      Paginate,
      Breadcrumb,
      DataTableElement
    },
    methods: {
      dataReload(data) {
        this.loading = true;
        this.currentPage = data.currentPage;
        this.pageSize = data.pageSize;
        this.fetch(FeedbackServiceApi, this.fetchParams());
      }
    },
    mounted() {
      this.fetch(FeedbackServiceApi, this.fetchParams());
    },
    data: function() {
      return {
        tableData: [],
        eleTable: {
          loadingText: '加载中',
          isBorder: true,
          style: 'width: 100%',
          isType: false,
          columns: [
            {prop: 'content', label: '反馈内容', width: '400', showOverflowTooltip: true},
            {prop: 'user_id', label: '用户ID', width: '350', showOverflowTooltip: true},
            {prop: 'user_name', label: '用户名', width: '160', showOverflowTooltip: true},
            {prop: 'connect_phone', label: '联系方式', width: '320'},
            {prop: 'created_at', label: '反馈时间', width: '200'}
          ]
        }

      };
    }
  };
</script>
