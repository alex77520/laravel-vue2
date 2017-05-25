<style>
  .formWidth {
    width: 300px;
    padding-bottom: 5px;
  }

  .btnClass {
    margin-right: 20px;
  }
  .formBtn {
    margin-left: 100px;
  }
</style>

<template>
  <el-form :model="form.formModel" :rules="form.rules" :ref="form.ref" :label-width="form.labelWidth">
      <el-form-item  v-for="item in form.columns" :label="item.label" :prop="item.model">
      <el-input
        v-if="item.type == 'input'"
        :class='item.class'
        :disabled='item.disabled'
        :placeholder="item.placeholder"
        v-model="form.formModel[item.model]">
      </el-input>

      <el-input
        v-else-if="item.type == 'textarea'"
        type="textarea"
        :class='item.class'
        :prop="item.model"
        :autosize="{ minRows: item.minRows, maxRows: item.maxRows}"
        :placeholder="item.placeholder"
        v-model="form.formModel[item.model]">
      </el-input>

      <el-select
        v-else-if="item.type == 'select'"
        :class='item.class'
        :prop="item.model"
        :placeholder="item.placeholder"
        v-model="form.formModel[item.model]">
        <el-option
          v-for="option in item.options"
          :label="option.label"
          :value="option.value">
        </el-option>
      </el-select>

      <el-cascader
        v-else-if="item.type == 'cascader'"
        :expand-trigger="item.trigger"
        :options="item.options"
        v-model="form.formModel[item.model]">
      </el-cascader>

      <el-upload
        v-else-if="item.type == 'upload'"
        :class="item.class"
        :action="item.action"
        :file-list="item.fileList"
        :list-type="item.listType"
        :on-preview="handlePreview"
        :on-success="handleSuccess"
        :on-remove="handleRemove">
        <el-button size="small" type="primary">点击上传</el-button>
        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
      </el-upload>

      <el-date-picker
        v-else-if="item.type == 'datetime'"
        v-model="form.formModel[item.model]"
        :class="item.class"
        :type="item.datetype"
        :format="item.dateformat"
        :placeholder="item.placeholder">
      </el-date-picker>

    </el-form-item>
    <div :class="form.buttons.class">
      <span v-for="item in form.buttons.options">
        <el-button :type='item.type':class='item.class' @click="trigger(item.event)"> {{ item.value }}</el-button>
      </span>
    </div>

  </el-form>
</template>

<script type="text/babel">
  export default {
    props: [
      'form'
    ],
    methods: {
      submit() {
        this.$refs[this.form.ref].validate((valid) => {
          if (valid) {
            this.$parent.submit(this.form.formModel);
          } else {
            this.$notify({message: '输入参数不合法！！', type: 'warn'});
            return false;
          }
        });
      },
      reset() {
        this.$refs[this.form.ref].resetFields();
      },
      trigger(event) {
        if (event === 'reset') {
          this.reset();
          // this.form.columns.forEach(field => this.$set(this.form.formModel, field.model, ''));
        } else if (event === 'submit') {
          this.submit();
        } else {
          this.$notify({type: 'error', message: 'undefined event!'});
          return false;
        }
      },
      handleRemove(file, fileList) {
        console.log(file, fileList);
      },
      handlePreview(file) {
        console.log(file);
      },
      handleSuccess: function(response, file, fileList) {
        if (response.ret === 0) {
          this.form.formModel.icon = response.data.url;
          // this.topicForm.topicImg = response.data.url;
        } else {
          // this.$notify()
        }
      }
    }
  };
</script>
