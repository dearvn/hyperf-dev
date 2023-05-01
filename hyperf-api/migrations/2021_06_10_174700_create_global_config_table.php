<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateGlobalConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_config', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('key_name', 255)->default('')->comment('keyName');
            $table->string('name', 255)->default('')->comment('name');
            $table->string('type', 50)->default('text')->comment('type text html json boolean');
            $table->string('remark', 1000)->default('')->comment('Remark');
            $table->text('data')->comment('data');
            $table->timestamps();
            $table->unique('key_name', 'key_name_unique');
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `global_config` comment'Parameter configuration table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_config');
    }
}
