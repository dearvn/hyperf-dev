<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTimedTaskTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timed_task', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 255)->default('')->comment('Timing task name');
            $table->string('params', 500)->default('')->comment('As a parameter');
            $table->string('task', 255)->default('')->comment('task task name');
            $table->string('execute_time', 255)->default('')->comment('execution time');
            $table->string('next_execute_time', 255)->default('')->comment('The next execution time');
            $table->string('desc', 255)->default('')->comment('Remark information');
            $table->integer('times')->default('0')->comment('Number of executions');
            $table->tinyInteger('status')->default('0')->comment('1: Enable 0: Disable');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `timed_task` comment'Timing task table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timed_task');
    }
}
