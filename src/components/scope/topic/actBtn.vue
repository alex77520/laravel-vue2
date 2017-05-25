<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    computed: {
      statusText: function() {
        return parseInt(this.row.status) === 3 ? '激活' : '取消激活';
      }
    },
    methods: {
      edit: function() {
        this.$parent.$router.push({name: 'topicEdit', path: '/topicEdit', params: {id: this.row.id}});
      },
      update: function() {
        var updateStatus = parseInt(this.row.status) === 3 ? 1 : 3;
        this.$emit('update-status', {row: this.row, status: updateStatus});
      }
    },
    template: '<div class="block"><el-button type="info" @click="edit()" size="small">编辑</el-button>' +
    '<el-button type="info" size="small" @click="update()">{{ statusText }}</el-button></div>'
  };
</script>
