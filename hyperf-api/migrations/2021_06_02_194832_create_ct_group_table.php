<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCtGroupTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('group_id', 50)->comment('Group ID');
            $table->integer('uid')->default('0')->comment('Create group user ID');
            $table->string('group_name', 30)->default('0')->comment('Group name');
            $table->string('avatar', 255)->default('')->comment('avatar');
            $table->integer('size')->default('200')->comment('Group scale 200 500 1000');
            $table->text('introduction')->comment('Group introduction');
            $table->tinyInteger('validation')->default('1')->comment('Do you need to verify if you need to verify 0?');
            $table->timestamps();
            $table->index('uid', 'uid_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `ct_group` comment'群组表'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_group');
    }
}
