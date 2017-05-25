<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    methods: {
      trigger: function() {
        this.$parent.$router.push({name: 'libraryView', path: '/libraryView', params: {id: this.row.id}});
      }
    },
    computed: {
      statusText: function() {
        return parseInt(this.row.libraryStatus) === 0 ? '查看' : '添加';
      },
      tagType: function() {
        return parseInt(this.row.libraryStatus) === 0 ? 'info' : 'warning';
      }
    },
    template: '<el-button :type="tagType" size="small" @click="trigger()">{{ statusText }}</el-button>'
  };
</script>
