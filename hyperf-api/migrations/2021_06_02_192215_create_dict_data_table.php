<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateDictDataTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dict_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('dict_code')->comment('Dictionary');
            $table->integer('dict_sort')->default('0')->comment('Dictionary');
            $table->string('dict_label', 100)->default('')->comment('Dictionary label');
            $table->string('dict_value', 100)->default('')->comment('Dictionary key value');
            $table->string('dict_type', 100)->default('')->comment('Dictionary');
            $table->string('css_class', 100)->default('')->comment('Style attributes (other style extensions)');
            $table->string('list_class', 100)->default('')->comment('Table back display style');
            $table->tinyInteger('is_default')->default('1')->comment('Whether to default (y is n or no)');
            $table->tinyInteger('status')->default('1')->comment('Status (0 normal 1 stop)');
            $table->string('remark', 500)->comment('Remark');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `advice` comment'字典数据表'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dict_data');
    }
}
