<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCtGroupChatHistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_group_chat_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('message_id', 50)->default('')->comment('Message ID');
            $table->integer('from_uid')->default('0')->comment('Before receiving friends');
            $table->string('to_group_id', 50)->default('0')->comment('Receiving group');
            $table->string('type', 50)->default('1')->comment('Message type 1：text, 2: image, 3:file, 4:event');
            $table->string('status', 50)->default('')->comment('Message sending status going,failed,succeed');
            $table->bigInteger('send_time')->default('0')->comment('The sending time is 13 digits of milliseconds');
            $table->text('content')->comment('消息内容');
            $table->integer('file_size')->default('0')->comment('File size');
            $table->string('file_name', 255)->default('')->comment('file name');
            $table->string('file_ext', 50)->default('')->comment('File extension');
            $table->tinyInteger('reception_state')->default('0')->comment('Accept Status 0 No Receive 1: Receive');
            $table->timestamps();
            $table->index('message_id', 'message_id_index');
            $table->index('from_uid', 'from_uid_index');
            $table->index('to_group_id', 'to_group_id_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `ct_group_chat_history` comment'Group chat record'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_group_chat_history');
    }
}
