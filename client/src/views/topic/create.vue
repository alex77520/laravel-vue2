<template>
<div>
  <topic-form :show=true :bread="bread" :type='type'></topic-form>
</div>
</template>

<script type="text/babel">
  import TopicForm from './_form';
  export default {
    components: {
      TopicForm
    },
    data: function() {
      return {
        bread: '话题创建',
        type: '创建'
      };
    }
  };
</script>
