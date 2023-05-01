<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTimedTaskLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timed_task_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('task_id')->comment('Task ID');
            $table->string('task_name', 255)->default('')->comment('Task name');
            $table->string('task', 255)->default('')->comment('Task');
            $table->integer('execute_time')->comment('execution time');
            $table->text('error_log')->comment('Error message');
            $table->tinyInteger('result')->default('0')->comment('Execute results 1: Success 0: Failure');
            $table->index('task_id', 'task_id_index');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `timed_task_log` comment'Mission log'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timed_task_log');
    }
}
