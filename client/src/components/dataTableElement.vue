<template>
    <el-table
        v-loading="loading"
        :element-loading-text='eleTable.loadingText'
        :data="tableData"
        :border="eleTable.isBorder"
        :style="eleTable.style"
        @selection-change="handleSelectionChange">
        <el-table-column
            v-if="eleTable.isType"
            fixed
            :type="eleTable.typeValue"
            width="55">
        </el-table-column>
        <el-table-column
            v-for="item in eleTable.columns"
            show-overflow-tooltip="true"
            :fixed="item.fixed"
            :width="item.width"
            :prop="item.prop"
            :label="item.label">
            <template scope="scope">
                <div class="block" v-if="item.isScope">
                    <component v-bind:is="item.template" :row="scope.row" @update-status="updateStatusRow"></component>
                </div>
                <div v-else>
                    {{ scope.row[item.prop] }}
                </div>
            </template>
            <template scope="scope" v-else>
                {{ scope.row[item.prop] }}
            </template>
        </el-table-column>
    </el-table>
</template>

<script type="text/babel">
    export default {
      props: [
        'eleTable',
        'tableData',
        'loading',
        'bus'
      ],
      data: function() {
        return {
          multipleSelection: []
        };
      },
      methods: {
        handleSelectionChange(val) {
          this.multipleSelection = val;
        },
        updateStatusRow(data) {
          this.$parent.updateStatusRow(data.row, data.status);
        }
      },
      mounted: function() {
        this.bus.$on('updateStatusBatch', function(status) {
          if (this.multipleSelection.length < 1) {
            this.$notify({message: '请先勾选', type: 'warning'});
          } else {
            this.$parent.updateStatusBatch(this.multipleSelection, status);
          }
        }.bind(this));
      }
    };
</script>
