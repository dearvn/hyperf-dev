<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateBiliVideoReportTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bili_video_report', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('time')->default('0')->comment('Timestamp');
            $table->string('bvid', 255)->default('')->comment('Video ID');
            $table->string('mid', 255)->default('')->comment('User ID');
            $table->integer('view')->default('0')->comment('Video playback');
            $table->integer('danmaku')->default('0')->comment('Number of barrage');
            $table->integer('reply')->default('0')->comment('Number of comments');
            $table->integer('favorite')->default('0')->comment('Collecting number');
            $table->integer('coin')->default('0')->comment('Coin');
            $table->integer('likes')->default('0')->comment('Number of likes');
            $table->integer('dislike')->default('0')->comment('Stepping on');
            $table->primary(['time', 'bvid']);
            $table->index('mid', 'mid_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `bili_video_report` comment'Video report data table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bili_video_report');
    }
}
