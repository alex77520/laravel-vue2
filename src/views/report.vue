<template>
<div>
  <Breadcrumb :show='true' breadcrumb="举报列表"></Breadcrumb>

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
  import ReportService from '../services/ReportService';
  import Paginate from '../components/paginate';
  import Breadcrumb from '../components/breadcrumb';
  import DataTableElement from '../components/dataTableElement';
  import ActBtn from '../components/scope/report/actBtn';
  const ReportServiceApi = new ReportService();
  export default {
    components: {
      Paginate,
      Breadcrumb,
      DataTableElement,
      ActBtn
    },
    methods: {
      dataReload(data) {
        this.loading = true;
        this.currentPage = data.currentPage;
        this.pageSize = data.pageSize;
        this.fetch(ReportServiceApi, this.fetchParams());
      },
      updateStatusRow(row, status) {
        var params = {id: row.id, status: status};
        ReportServiceApi.update(params).then(res => {
          if (res.ret === 0) {
            row.status = status;
            this.$notify({message: '修改成功', type: 'success'});
          } else {
            this.$notify({message: '修改失败', type: 'error'});
          }
        });
      }
    },
    mounted() {
      this.fetch(ReportServiceApi, this.fetchParams());
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
            {prop: 'report_content', label: '举报内容', width: '350', fixe: 'right', showOverflowTooltip: true},
            {prop: 'report_user_id', label: '被举报用户ID', width: '200', showOverflowTooltip: true},
            {prop: 'report_user_nick', label: '被举报用户名', width: '160'},
            {prop: 'type', label: '类型', width: '100'},
            {prop: 'content', label: '举报备注', width: '200', showOverflowTooltip: true},
            {prop: 'user_id', label: '举报人ID', width: '200', showOverflowTooltip: true},
            {prop: 'user_nick', label: '举报人用户名', width: '160'},
            {prop: 'created_at', label: '举报时间', width: '180'},
            {prop: '', label: '操作', width: '200', fixed: 'right', isScope: true, template: ActBtn}
          ]
        }
      };
    }
  };
</script>
