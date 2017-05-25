<template>
  <div>
    <el-popover ref="popover1" placement="top" width="180" trigger="hover">
      <div class="block">
        <span class="wrapper">
          <el-button @click="update(1)" type="info" size="small">通过</el-button>
          <el-button @click="update(2)" type="warning" size="small">不通过</el-button>
        </span>
      </div>
    </el-popover>
    <el-button type="info" @click="edit()" size="small">编辑</el-button>
    <el-button type="text" v-popover:popover1 size="small">审核</el-button>
  </div>
</template>

<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    computed: {
      // statusText() {
      //   // var status = parseInt(this.row.status);
      //   return parseInt(this.row.status) === 0 ? '审核中' : '审核';
      // }
    },
    methods: {
      edit: function() {
        this.$parent.$router.push({name: 'questionReportEdit', path: '/questionReportEdit', params: {id: this.row.id}});
      },
      update: function(status) {
        this.$emit('update-status', {row: this.row, status: status});
      }
    }
  };
</script>
