<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateBiliUpUserReportTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bili_up_user_report', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('time')->default('0')->comment('Timestamp');
            $table->string('mid', 255)->default('')->comment('User ID');
            $table->integer('following')->default('0')->comment('Pay attention');
            $table->integer('follower')->default('0')->comment('Number of fans');
            $table->integer('video_play')->default('0')->comment('Video playback');
            $table->integer('readling')->default('0')->comment('Number of reading');
            $table->integer('likes')->default('0')->comment('Praise');
            $table->integer('recharge_month')->default('0')->comment('Monthly charging number');
            $table->integer('recharge_total')->default('0')->comment('Total charging number');
            $table->primary(['time', 'mid']);
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `bili_up_user_report` comment'UpMain information data report'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bili_up_user_report');
    }
}
