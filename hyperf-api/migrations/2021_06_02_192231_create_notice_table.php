<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('username', 255)->default('')->comment('user name');
            $table->integer('user_id')->default('0')->comment('User ID');
            $table->string('title', 50)->default('')->comment('title');
            $table->longText('content')->comment('content');
            $table->integer('public_time')->default('0')->comment('release time');
            $table->tinyInteger('status')->default('0')->comment('operating system');
            $table->timestamps();
            $table->index('public_time', 'notice_public_time_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `notice` comment'System notification form'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice');
    }
}
