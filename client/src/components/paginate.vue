<style>
  .pageClass {
    text-align:center;
    margin-top: 20px;
  }

</style>

<template>
    <div class="block pageClass">
    <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="currentPage"
        :page-sizes="pageSizeSelector"
        :page-size="1"
        layout="total, sizes, prev, pager, next"
        :total="total">
    </el-pagination>
    </div>
</template>

<script type="text/babel">
  export default {
    props: [
      'total',
      'pageSize'
    ],
    data: function() {
      return {
        currentPage: 1,
        pageSizeSelector: ['30', '50', '100']
      };
    },
    methods: {
      handleCurrentChange(val) {
        this.$emit('handle-current-change', {currentPage: val, pageSize: this.pageSize});
      },
      handleSizeChange(val) {
        this.currentPage = 1;
        this.$emit('handle-size-change', {currentPage: this.currentPage, pageSize: val});
      }
    }
  };
</script>
