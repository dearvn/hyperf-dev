<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateOperateLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operate_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('action', '255')->default('')->comment('operate');
            $table->text('data')->comment('Request parameters');
            $table->string('username', '100')->default('')->comment('Operator account');
            $table->string('operator', '100')->default('')->comment('Description of the operator');
            $table->string('response_result', '1000')->default('')->comment('Response result');
            $table->string('response_code', '50')->default('')->comment('Response status code');
            $table->string('target_class', '50')->default('')->comment('Target class');
            $table->string('target_method', '50')->default('')->comment('Target');
            $table->string('request_ip', '50')->default('')->comment('Request IP');
            $table->string('request_method', '50')->default('')->comment('Method of requesting');
            $table->string('target_url', '100')->default('')->comment('Target routing');
            $table->integer('uid')->default(0)->comment('Operator ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operate_log');
    }
}
