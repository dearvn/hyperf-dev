<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCtGroupRelationTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_group_relation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('uid')->default('0')->comment('User ID');
            $table->string('group_id', 50)->default('0')->comment('Group ID');
            $table->tinyInteger('level')->default('2')->comment('Level: 0 group owner, 1 administrator, 2 members');
            $table->timestamps();
            $table->primary(['uid', 'group_id'], 'uid_group_id_key');
            $table->index('uid', 'uid_index');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `ct_group_relation` comment'Group-user association table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_group_relation');
    }
}
