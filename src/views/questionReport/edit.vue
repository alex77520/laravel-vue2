<template>
<div>
  <question-report-form :show=true></question-report-form>
</div>
</template>

<script type="text/babel">
  import QuestionReportForm from './_form';
  export default {
    components: {
      QuestionReportForm
    }
  };
</script>
