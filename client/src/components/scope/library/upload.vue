<style>
  .el-upload__tip {
    margin-top: 15px;
  }
</style>
<template>
  <el-dialog title="导入题库"
    v-model='dialogVisible'
    @close='close'>
    <el-upload
      class="upload-demo"
      action="/library/upload"
      :file-list="fileList"
      :on-success="handleSuccess">
      <el-button size="small" type="primary">点击上传并导入</el-button>
      <div slot="tip" class="el-upload__tip">上传题库，请使用csv文档</div>
    </el-upload>
    <div slot="footer">
      <el-button @click="close()">取 消</el-button>
      <el-button type="primary" @click="close()">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script type="text/babel">
  export default {
    props: [
      'dialogVisible'
    ],
    methods: {
      handleSuccess: function(response, file, fileList) {
        if (response.ret === 0) {
          this.$notify({type: 'success', message: 'import success'});
        } else {
          this.$notify({type: 'error', message: 'import failed'});
        }
      },
      close: function() {
        this.$parent.dialogVisible = false;
      }
    }
  };
</script>>
