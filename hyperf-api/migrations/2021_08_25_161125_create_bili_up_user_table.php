<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateBiliUpUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bili_up_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('mid', 255)->default('')->comment('User ID')->primary();
            $table->string('name', 255)->default('')->comment('name');
            $table->string('sex', 50)->default('unknown')->comment('gender');
            $table->string('sign', 500)->default('unknown')->comment('sign');
            $table->string('face', 255)->default('unknown')->comment('avatar');
            $table->tinyInteger('level')->default('0')->comment('level');
            $table->string('top_photo', 255)->default('')->comment('Head Figure');
            $table->string('live_room_info', 1000)->default('')->comment('Live room information');
            $table->string('birthday', 100)->default('')->comment('Birthday');
            $table->integer('following')->default('0')->comment('Pay attention');
            $table->integer('follower')->default('0')->comment('Number of fans');
            $table->integer('video_play')->default('0')->comment('Video playback');
            $table->integer('readling')->default('0')->comment('Number of reading');
            $table->integer('likes')->default('0')->comment('Praise');
            $table->integer('recharge_month')->default('0')->comment('Monthly charging number');
            $table->integer('recharge_total')->default('0')->comment('Total charging number');
            $table->tinyInteger('timed_status')->default('0')->comment('Timing task status');
            $table->index('name', 'name_index');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `bili_up_user` comment'UP main information table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bili_up_user');
    }
}
