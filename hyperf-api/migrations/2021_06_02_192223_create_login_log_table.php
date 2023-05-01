<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('username', 255)->default('')->comment('user name');
            $table->string('login_ip', 50)->default('')->comment('Log in IP address');
            $table->string('login_address', 255)->default('')->comment('Login address');
            $table->string('login_browser', 255)->default('')->comment('Log in the browser');
            $table->string('os', 50)->default('')->comment('operating system');
            $table->string('response_result', 255)->default('')->comment('Return result');
            $table->string('response_code', 50)->default('')->comment('Return status code');
            $table->dateTime('login_date')->comment('Login time');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `login_log` comment'Login log'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_log');
    }
}
