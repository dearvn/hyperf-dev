<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateBiliVideoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bili_video', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('bvid', 255)->default('')->comment('Video ID')->primary();
            $table->string('mid', 255)->default('')->comment('User ID');
            $table->string('owner', 1000)->default('')->comment('Owner');
            $table->string('cover', 255)->default('')->comment('Video cover map');
            $table->string('title', 255)->default('')->comment('title');
            $table->integer('public_time')->default('0')->comment('Video release time');
            $table->string('desc', 500)->default('')->comment('Video description');
            $table->integer('duration')->default('0')->comment('Video time(s)');
            $table->integer('view')->default('0')->comment('Video playback');
            $table->integer('danmaku')->default('0')->comment('Number of barrage');
            $table->integer('reply')->default('0')->comment('Number of comments');
            $table->integer('favorite')->default('0')->comment('Collecting number');
            $table->integer('coin')->default('0')->comment('Coin');
            $table->integer('likes')->default('0')->comment('Number of likes');
            $table->integer('dislike')->default('0')->comment('Stepping on');
            $table->tinyInteger('timed_status')->default('0')->comment('Timing task status');
            $table->index('mid', 'mid_index');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `bili_video` comment'Video data table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bili_video');
    }
}
