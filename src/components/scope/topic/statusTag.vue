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
        return parseInt(this.row.status) === 3 ? '未激活' : '激活';
      },
      tagType: function() {
        return parseInt(this.row.status) === 3 ? 'danger' : 'success';
      }
    },
    template: '<el-tag :type="tagType">{{ statusText }}</el-tag>'
  };
</script>
