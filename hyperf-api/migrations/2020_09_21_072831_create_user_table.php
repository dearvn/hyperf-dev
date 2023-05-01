<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username', '25')->comment('username');
            $table->string('password', '100')->comment('account password');
            $table->string('desc', '255')->default('')->comment('Description, remark');
            $table->tinyinteger('status')->default(1)->comment('Account Status 0-Disable, 1-Enable');
            $table->string('avatar', '255')->default('')->comment('profile picture');
            $table->string('mobile', '15')->default('')->comment('phone number');
            $table->string('last_ip', '15')->default('')->comment('Last login IP');
            $table->tinyInteger('sex')->default(0)->comment('Gender');
            $table->string('creater', '100')->default('')->comment('creator');
            $table->string('email', '100')->default('')->comment('email address');
            $table->string('address', '255')->default('')->comment('Shipping address');
            $table->integer('last_login')->default(0)->comment('Last Login Time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
