<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCtFriendChatHistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_friend_chat_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('message_id', 50)->default('')->comment('Message ID');
            $table->string('type', 50)->default('1')->comment('Message type 1：text, 2: image, 3:file, 4:event');
            $table->string('status', 50)->default('')->comment('Message sending status going,failed,succeed');
            $table->bigInteger('send_time')->default('0')->comment('The sending time is 13 digits of milliseconds');
            $table->text('content')->comment('Message content');
            $table->integer('file_size')->default('0')->comment('File size');
            $table->string('file_name', 255)->default('')->comment('file name');
            $table->integer('to_uid')->default('0')->comment('Before receiving friends');
            $table->integer('from_uid')->default('0')->comment('sender');
            $table->tinyInteger('reception_state')->default('0')->comment('Accept Status 0 No Receive 1: Receive');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `ct_friend_chat_history` comment'好友聊天记录'");//表注释一定加上前缀
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_friend_chat_history');
    }
}
