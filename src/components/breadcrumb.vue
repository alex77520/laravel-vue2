<style>

  .breadClass {
    margin-top:10px;
    margin-bottom: 40px;
  }
</style>

<template>
  <div class="breadClass">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/welcome' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item> {{ breadcrumb }} </el-breadcrumb-item>
    </el-breadcrumb>
  </div>
</template>


<script type="text/babel">
  export default {
    props: [
      'breadcrumb'
    ]
  };
</script>
