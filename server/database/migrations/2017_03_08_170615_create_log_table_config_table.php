<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTableConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_table_config', function (Blueprint $table) {
            $table->increments('id')->comment("唯一id号");
            $table->integer('app_id')->comment("项目ID");
            $table->integer('log_type_id')->comment("日志类型ID");
            $table->text('field_config')->comment("字段");
            $table->text('partition')->comment("分区");
            $table->string('table_comment', 255)->nullable()->comment("注释");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_config');
    }
}
