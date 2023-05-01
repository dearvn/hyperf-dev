<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCtUserApplicationTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_user_application', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('uid')->default('0')->comment('User ID');
            $table->integer('receiver_id')->default('0')->comment('receiver');
            $table->string('group_id', 50)->default('')->comment('Friends group ID || Group');
            $table->tinyInteger('application_type')->default('1')->comment('Application type 1 friend 2 group');
            $table->tinyInteger('application_status')->default('0')->comment('Application Status 0 Create 1 Agree 2 Rejects');
            $table->string('application_reason', 255)->default('0')->comment('Cause');
            $table->tinyInteger('read_state')->default('0')->comment('Read Status 0 Read 1 1 Reading');
            $table->timestamps();
            $table->index('uid', 'uid_index');
            $table->index('receiver_id', 'receiver_id_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `ct_user_application` comment'Friends/group application form'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_user_application');
    }
}
