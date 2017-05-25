<template>
  <div v-loading="loading">
    <Breadcrumb :show='true' breadcrumb="热议配置更新"></Breadcrumb>
    <Form :show='true'
      :form='form'>
    </Form>
  </div>
</template>

<script type="text/babel">
  import RecommendService from '../../services/RecommendService';
  import Breadcrumb from '../../components/breadcrumb';
  import Form from '../../components/form';
  const RecommendServiceApi = new RecommendService();

  export default {
    components: {
      Breadcrumb,
      Form
    },
    methods: {
      submit(formModel) {
        RecommendServiceApi.store({formModel: formModel}).then(res => {
          if (res.ret === 0) {
            this.$notify({message: res.msg, type: 'success'});
            this.$router.push('/recommendList');
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
      RecommendServiceApi.create({id: this.form.formModel.id}).then(res => {
        if (res.ret === 0) {
          this.form.formModel = res.data.formModel;
          this.loading = false;
        } else {
          this.$notify({message: 'retrieve data failed', type: 'error'});
          this.loading = false;
        }
      });
    },
    data: function() {
      return {
        loading: false,
        form: {
          formModel: {id: '', recommend_id: '', sort: '', type: '', status: ''},
          ref: 'recommendForm',
          labelWidth: '100px',
          columns: [
            {type: 'input', placeholder: '请输入热议ID', class: 'formWidth', label: '热议ID', model: 'recommend_id'},
            {type: 'input', placeholder: '请输入排序', class: 'formWidth', label: '排序', model: 'sort'},
            {type: 'select', placeholder: '请选择热议类别', class: 'formWidth', label: '热议类别', model: 'type', options: [{value: '1', label: '话题'}, {value: '2', label: '讨论'}]},
            {type: 'select', placeholder: '请选择激活状态', class: 'formWidth', label: '状态', model: 'status', options: [{value: '1', label: '激活'}, {value: '0', label: '未激活'}]}
          ],
          buttons: {
            class: 'formBtn',
            options: [
              {type: 'primary', value: '添加', class: 'btnClass', event: 'submit'},
              {type: 'default', value: '重置', class: 'btnClass', event: 'reset'}
            ]
          },
          rules: {
            recommend_id: [
              {required: true, message: '请输入热议ID', trigger: 'blur'}
            ],
            sort: [
              {required: true, message: '请输入排序', trigger: 'blur'}
            ],
            type: [
              {required: true, message: '请选择热议类别', trigger: 'blur'}
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
