<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    data: function() {
      return {
      };
    },
    computed: {
      statusText: function() {
        return parseInt(this.row.status) === 1 ? '激活' : '未激活';
      },
      tagType: function() {
        return parseInt(this.row.status) === 1 ? 'success' : 'danger';
      }
    },
    template: '<el-tag :type="tagType">{{ statusText }}</el-tag>'
  };
</script>
