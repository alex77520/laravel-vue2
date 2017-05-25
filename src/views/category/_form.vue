<style>
  .breadClass {
    margin-top:10px;
    margin-bottom: 40px;
  }
</style>
<template>
  <div v-loading="loading">
    <div class="breadClass">
      <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/welcom' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>话题分类创建</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <el-form :model="topicCategoryForm" :rules="rules" ref="topicCategoryForm" label-width="100px">
      <el-form-item label="分类id" prop="cate_id" required>
        <el-input v-model="topicCategoryForm.cate_id" style="width:300px"></el-input>
      </el-form-item>
      <el-form-item label="分类名" prop="cate_name" required>
        <el-input v-model="topicCategoryForm.cate_name" style="width:300px"></el-input>
      </el-form-item>
      <el-form-item label="上线时间" prop="created_at" required>
        <el-date-picker
          v-model="topicCategoryForm.created_at"
          type="date"
          format="yyyy-MM-dd"
          placeholder="选择日期时间">
        </el-date-picker>
      </el-form-item>
      <el-form-item label="分类等级" prop="category_level" required>
        <el-select v-model="topicCategoryForm.category_level" clearable placeholder="请选择分类等级">
          <el-option
            v-for="item in categoryLevelOptions"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item v-if="this.topicCategoryForm.category_level != 1" label="所属分类" prop="parent_id" required>
        <el-select v-model="topicCategoryForm.parent_id" clearable placeholder="请选择所属等级">
          <el-option
            v-for="item in topCategoryOptions"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="排序" prop="sort" required>
        <el-input v-model="topicCategoryForm.sort" style="width:300px"></el-input>
      </el-form-item>
      <el-form-item label="激活" prop='status'>
        <el-radio class="radio" v-model="topicCategoryForm.status" label="1">激活</el-radio>
        <el-radio class="radio" v-model="topicCategoryForm.status" label="2">未激活</el-radio>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('topicCategoryForm')"><label v-if="topicCategoryForm.id">更新</label><label v-else>创建</label></el-button>
        <el-button @click="resetForm('topicCategoryForm')">重置</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script type="text/babel">
  import TopicCategoryService from '../../services/TopicCategoryService';
  const TopicCategoryServiceApi = new TopicCategoryService();

  export default {
    methods: {
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            TopicCategoryServiceApi.store({topicCategoryForm: this.topicCategoryForm}).then(res => {
              if (res.ret === 0) {
                console.log(res);
                this.$notify({message: res.msg, type: 'success'});
                this.$router.push('/topicCategory');
                return true;
              } else {
                this.$notify({message: res.msg, type: 'error'});
                return false;
              }
            });
          } else {
            this.$notify({message: '输入参数不合法！！', type: 'warn'});
            return false;
          }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      },
      handleCategoryChange(value) {
        // console.log(this.topicCategoryForm.categoryOptions);
      },
      handleRemove(file, fileList) {
        // console.log(file, fileList);
      },
      handlePreview(file) {
        // console.log(file);
      },
      handleSuccess: function(response, file, fileList) {
        // console.log(response);
        if (response.ret === 0) {
          // console.log(response);
          this.topicCategoryForm.topicImg = response.data.url;
        } else {
          // this.$notify()
        }
      }
    },
    mounted() {
      if (typeof this.$route.params.id !== 'undefined') {
        this.topicCategoryForm.id = this.$route.params.id;
        this.loading = true;
      }
      TopicCategoryServiceApi.create({id: this.topicCategoryForm.id}).then(res => {
        if (res.ret === 0) {
          console.log(res);
          this.topCategoryOptions = res.data.topCategoryOptions;
          this.topicCategoryForm = res.data.topicCategoryForm;
          if (typeof this.$route.params.id !== 'undefined') {
            this.topicCategoryForm.created_at = new Date(this.topicCategoryForm.created_at);
          }
          // this.topicCategoryForm.created_at = new Date(this.topicCategoryForm.created_at);
          this.loading = false;
        } else {
          this.$notify({message: 'retrieve data failed', type: 'error'});
          this.loading = false;
        }
      });
    },
    data: function() {
      return {
        topCategoryOptions: '',
        topicCategoryForm: {
          id: '',
          cate_id: '',
          cate_name: '',
          category_level: '',
          parent_id: '',
          created_at: '',
          status: '',
          sort: ''
        },
        options: [],
        loading: false,
        rules: {
          cate_id: [
            {required: true, message: '请输入分类ID', trigger: 'blur'}
          ],
          cate_name: [
            {required: true, message: '请输入分类名称', trigger: 'blur'},
            {min: 1, max: 12, message: '长度在 1 到 12 个字符', trigger: 'blur'}
          ],
          // category_level: [
          //   {required: true, message: '请选择分类等级', trigger: 'blur'}
          // ],
          // parent_id: [
          //   {required: true, message: '请选择父分类', trigger: 'blur'}
          // ],
          created_at: [
            {type: 'date', required: true, message: '请选择日期', trigger: 'change'}
          ],
          status: [
            {required: true, message: '请选择是否激活', trigger: 'blur'}
          ],
          sort: [
            {required: true, message: '请输入排序值', trigger: 'blur'}
          ]
        },
        categoryLevelOptions: [{value: 1, label: '一级分类'}, {value: 2, label: '二级分类'}]
      };
    }
  };
</script>
