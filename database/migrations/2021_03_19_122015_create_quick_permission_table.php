<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_permission', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('parent_id')->default(0);
            $table->string('name')->default('')->nullable();
            $table->string('url')->default('')->nullable();
            $table->string('icon')->default('')->nullable();
            $table->string('as')->default('')->nullable()->index();
            $table->string('controller')->default('')->nullable();
            $table->integer('type')->default(0);
            $table->integer('deep')->default(0);
            $table->integer('sort')->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('quick_permission');
    }
}
