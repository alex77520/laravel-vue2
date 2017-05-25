<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    computed: {
      statusText: function() {
        var status = parseInt(this.row.status);
        return status === 0 ? '未处理' : status === 1 ? '审核通过' : '审核未通过';
      },
      tagType: function() {
        var status = parseInt(this.row.status);
        return status === 0 ? 'warning' : status === 1 ? 'success' : 'danger';
      }
    },
    template: '<el-tag :type="tagType">{{ statusText }}</el-tag>'
  };
</script>
