<template>
  <div>
    <el-popover ref="popover1" placement="top" width="180" trigger="hover">
      <div class="block">
        <span class="wrapper">
          <el-button @click="update(1)" type="info" size="small">已阅(不处理)</el-button>
          <el-button @click="confirm()" type="warning" size="small">已阅(删帖)</el-button>
        </span>
      </div>
    </el-popover>
    <el-button type="text" v-popover:popover1 size="small">{{ statusText }}</el-button>
  </div>
</template>

<script type="text/babel">
  export default {
    props: [
      'row'
    ],
    computed: {
      statusText() {
        var status = parseInt(this.row.status);
        return status === 0 ? '未处理' : (status === 1 ? '已阅（不处理）' : '已阅（删帖）');
      }
    },
    methods: {
      confirm() {
        this.$confirm('确认删除该贴 ?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.update(2);
        });
      },
      update: function(status) {
        this.$emit('update-status', {row: this.row, status: status});
      }
    }
  };
</script>
