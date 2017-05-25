<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTypeConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_type_config', function (Blueprint $table) {
            $table->increments('id')->comment("唯一id号");
            $table->integer('app_id')->comment("项目ID");
            $table->string('log_type', 255)->comment("日志类型");
            $table->string('version', 255)->default("")->comment("版本");
            $table->string('database_name', 255)->default("")->comment("数据库");
            $table->string('table_name', 255)->default("")->comment("表名");
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
        Schema::dropIfExists('log_type_config');
    }
}
