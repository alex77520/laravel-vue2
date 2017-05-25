<style>
  .filterClass {
    width: 200px;
    margin-bottom: 20px;
    margin-right: 10px;
  }

</style>
<template>
  <div>
    <template v-for="item in filters">
      <el-input
          v-if="item.type == 'input'"
          :class='item.class'
          :placeholder="item.placeholder"
          v-model="models[item.model]">
      </el-input>

      <el-select
          v-else-if="item.type == 'select'"
          :class='item.class'
          :placeholder="item.placeholder"
          v-model="models[item.model]">
        <el-option
            v-for="option in item.options"
            :label="option.label"
            :value="option.value">
        </el-option>
      </el-select>
    </template>

    <el-button @click="submit"  icon="search">查询</el-button>
    <el-button @click="reset">重置</el-button>

  </div>
</template>

<script type="text/babel">
  export default {
    props: [
      'filters'
    ],
    data: function() {
      return {
        models: {}
      };
    },
    mounted: function() {
      this.filters.forEach(field => this.$set(this.models, field.model, ''));
    },
    methods: {
      submit() {
        var models = this.models;
        var data = {};
        this.filters.forEach(function(field) {
          if (field.model in models && models[field.model] !== '') {
            data[field.model] = models[field.model];
          }
        });
        this.$emit('filter-submit', data);
      },
      reset() {
        this.filters.forEach(field => this.$set(this.models, field.model, ''));
      }
    }
  };
</script>
