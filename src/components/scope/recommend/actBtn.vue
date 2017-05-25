<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    computed: {
      statusText: function() {
        return parseInt(this.row.status) === 0 ? '激活' : '取消激活';
      }
    },
    methods: {
      // edit: function() {
      //   this.$parent.$router.push({name: 'recommendEdit', path: '/recommendEdit', params: {id: this.row.id}});
      // },
      update: function() {
        var updateStatus = parseInt(this.row.status) === 1 ? 0 : 1;
        this.$emit('update-status', {row: this.row, status: updateStatus});
      }
    },
    template: '<el-button type="info" size="small" @click="update()">{{ statusText }}</el-button></div>'
  };
</script>
