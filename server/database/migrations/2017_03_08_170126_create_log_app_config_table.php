<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAppConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_app_config', function (Blueprint $table) {
            $table->increments('id')->comment("唯一id号");
            $table->integer('app_id')->comment("项目ID");
            $table->string('app_name', 255)->comment("项目名称");
            $table->string('app_key', 255)->comment("名称Key");
            $table->integer('status')->comment("项目状态0禁用，1启用");
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
        Schema::dropIfExists('app_config');
    }
}
