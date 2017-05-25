<template>
  <div v-loading="loading">
    <Breadcrumb :show='true' breadcrumb="题库更新"></Breadcrumb>
    <Form :show='true'
      :form='form'>
    </Form>
  </div>
</template>

<script type="text/babel">
  import LibraryService from '../../services/LibraryService';
  import Breadcrumb from '../../components/breadcrumb';
  import Form from '../../components/form';

  const LibraryServiceApi = new LibraryService();

  export default {
    components: {
      Breadcrumb,
      Form
    },
    methods: {
      submit(formModel) {
        LibraryServiceApi.store({formModel: formModel}).then(res => {
          if (res.ret === 0) {
            this.$notify({message: res.msg, type: 'success'});
            this.$router.push('/libraryList');
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
      LibraryServiceApi.create({id: this.form.formModel.id}).then(res => {
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
          formModel: {id: '', question: '', right_answer: '', total_answer: '', status: ''},
          ref: 'questionModel',
          labelWidth: '100px',
          columns: [
            {type: 'input', placeholder: '请输入问题', class: 'formWidth', label: '问题', model: 'question'},
            {type: 'input', placeholder: '请输入正确答案', class: 'formWidth', label: '正确答案', model: 'right_answer'},
            {type: 'textarea', placeholder: '请输入所有答案', class: 'formWidth', label: '所有答案', model: 'total_answer', minRows: 3, maxRows: 4},
            {type: 'select', placeholder: '请选择激活状态', class: 'formWidth', label: '状态', model: 'status', options: [{value: 1, label: '激活'}, {value: 2, label: '未激活'}]}
          ],
          buttons: {
            class: 'formBtn',
            options: [
              {type: 'primary', value: '更新', class: 'btnClass', event: 'submit'},
              {type: 'default', value: '重置', class: 'btnClass', event: 'reset'}
            ]
          },
          rules: {
            question: [
              {required: true, message: '请输入问题', trigger: 'blur'}
            ],
            right_answer: [
              {required: true, message: '请输入正确答案', trigger: 'blur'}
            ],
            total_answer: [
              {required: true, message: '请输入所有答案', trigger: 'blur'}
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
