<template>
  <LytMain>
    <router-view></router-view>
  </LytMain>
</template>

<script type="text/babel">
  export default {
    name: 'index',
    components: {
      LytMain: require('../components/layout/main')
    }
  };
</script>
