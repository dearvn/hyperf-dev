<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddFieldTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Friends chat watch increase file suffix
        Schema::table('ct_friend_chat_history', function (Blueprint $table) {
            $table->string('file_ext', 255)->default('')->comment('File extension')->after('file_name');
        });
        //Increasing field of the friend group
        Schema::table('ct_friend_group', function (Blueprint $table) {
            $table->integer('sort')->default(99)->comment('The sequence of grouping is from small to large in order')->after('friend_group_name');
        });
        //Friends relationship table increase field
        Schema::table('ct_friend_relation', function (Blueprint $table) {
            $table->string('friend_remark', 255)->default('')->comment('Friends Note')->after('friend_id');
            $table->integer('friend_group')->default(0)->comment('Friends group')->after('friend_remark');
            $table->tinyInteger('is_up')->default(0)->comment('Whether to set the top 1: Yes 0: No')->after('friend_group');
            $table->tinyInteger('is_not_disturb')->default(0)->comment('Whether the news is free of disturbance')->after('is_up');
        });
        //Friends relationship table increase field
        Schema::table('ct_group_relation', function (Blueprint $table) {
            $table->tinyInteger('is_up')->default(0)->comment('Whether to set the top 1: Yes 0: No')->after('group_id');
            $table->tinyInteger('is_not_disturb')->default(0)->comment('Whether the news is free of disturbance')->after('is_up');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
