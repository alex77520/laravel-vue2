<template>
  <div v-loading="loading">
    <Breadcrumb :show='true' :breadcrumb="bread"></Breadcrumb>
    <Form :show='true'
      :form='form'>
    </Form>
  </div>
</template>

<script type="text/babel">
  import Breadcrumb from '../../components/breadcrumb';
  import Form from '../../components/form';
  import TopicService from '../../services/TopicService';
  const TopicServiceApi = new TopicService();

  export default {
    props: [
      'bread',
      'type'
    ],
    components: {
      Breadcrumb,
      Form
    },
    methods: {
      submit(formModel) {
        TopicServiceApi.store({formModel: formModel}).then(res => {
          if (res.ret === 0) {
            this.$notify({message: res.msg, type: 'success'});
            this.$router.push('/topicList');
            return true;
          } else {
            this.$notify({message: res.msg, type: 'error'});
            return false;
          }
        });
      }
    },
    mounted() {
      if (typeof this.$route.params.id !== 'undefined') {
        this.form.formModel.id = this.$route.params.id;
        this.loading = true;
      }
      TopicServiceApi.create({id: this.form.formModel.id}).then(res => {
        if (res.ret === 0) {
          this.form.columns[3].options = res.data.options;
          this.form.formModel = res.data.formModel;
          if (typeof this.$route.params.id !== 'undefined') {
            this.form.formModel.created_at = new Date(this.form.formModel.created_at);
          }
          this.loading = false;
        } else {
          this.$notify({message: 'retrieve data failed', type: 'error'});
          this.loading = false;
        }
      });
    },
    data: function() {
      return {
        loading: true,
        form: {
          formModel: {id: '', title: '', alias_id: '', created_at: '', description: '', status: '', categoryOptions: [], icon: '', sort: ''},
          ref: 'questionModel',
          labelWidth: '100px',
          columns: [
            {type: 'input', placeholder: '请输入话题名', class: 'formWidth', label: '话题名', model: 'title'},
            {type: 'input', placeholder: '请输入话题前端ID', class: 'formWidth', label: '前端ID', model: 'alias_id'},
            {type: 'datetime', placeholder: '请输入上线时间', class: 'formWidth', label: '上线时间', model: 'created_at', datetype: 'date', dateformat: 'yyyy-MM-dd'},
            {type: 'cascader', placeholder: '请选择分类', class: 'formWidth', label: '话题分类', model: 'categoryOptions', trigger: 'hover', options: []},
            {type: 'upload', class: 'upload-demo', label: '话题图标', model: 'icon', action: '/topic/upload', fileList: [], listType: 'picture'},
            {type: 'textarea', placeholder: '请输入话题描述', class: 'formWidth', label: '话题描述', model: 'description', minRows: 3, maxRows: 4},
            {type: 'select', placeholder: '请选择激活状态', class: 'formWidth', label: '状态', model: 'status', options: [{value: '1', label: '激活'}, {value: '3', label: '未激活'}]},
            {type: 'input', placeholder: '请输入排序值，大则优先', class: 'formWidth', label: '排序', model: 'sort'}
          ],
          buttons: {
            class: 'formBtn',
            options: [
              {type: 'primary', value: this.type, class: 'btnClass', event: 'submit'},
              {type: 'default', value: '重置', class: 'btnClass', event: 'reset'}
            ]
          },
          rules: {
            title: [
              {required: true, message: '请输入话题名称', trigger: 'blur'},
              {min: 1, max: 7, message: '长度在 1 到 7 个字符', trigger: 'blur'}
            ],
            alias_id: [
              {required: true, message: '请输入前端ID', trigger: 'blur'}
            ],
            sort: [
              {required: true, message: '请输入排序值', trigger: 'blur'}
            ],
            created_at: [
              {type: 'date', required: true, message: '请选择日期', trigger: 'change'}
            ],
            description: [
              {required: true, message: '请填写话题描述', trigger: 'blur'}
            ],
            status: [
              {required: true, message: '请选择是否激活', trigger: 'blur'}
            ]
          }
        }
      };
    }
  };
</script>
