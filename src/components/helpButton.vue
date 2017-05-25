<style>
    .clear {
        margin: 0;
    }

    .right {
        float: right;
    }

    .helpBtn {
        margin-bottom: 10px;
    }

</style>
<template>
    <div>
       <template v-for="item in helpButtons">
           <el-button
               v-once
               :class="item.class"
               :disabled="disable"
               :icon="item.icon"
               @click="trigger(item.callback, item.bus, item.status);">
               {{ item.text }}
           </el-button>
       </template>
    </div>
</template>

<script type="text/babel">
    export default {
      props: [
        'helpButtons',
        'disable'
      ],
      data: function() {
        return {
        };
      },
      methods: {
        trigger(callback, bus, status) {
          if (bus === true) {
            this.bus.$emit(callback, status);
          } else {
            this.$emit('btn-trigger', callback);
          }
        }
      }
    };
</script>
