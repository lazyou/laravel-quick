<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickOperationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_operation_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('操作人');
            $table->string('as')->nullable()->default('');
            $table->ipAddress('ip')->nullable();
            $table->integer('permission_id')->default(0)->comment('权限(菜单)');
            $table->string('url')->default('')->comment('请求url');
            $table->string('method')->default('')->comment('请求方法');
            $table->longText('body')->nullable()->comment('请求数据');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quick_operation_log');
    }
}
