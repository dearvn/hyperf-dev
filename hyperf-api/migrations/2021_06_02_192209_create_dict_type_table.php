<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateDictTypeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dict_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('dict_id')->comment('Dictionary main key');
            $table->string('dict_name', 100)->default('')->comment('Dictionary name');
            $table->string('dict_type', 100)->default('')->comment('Dictionary');
            $table->string('remark', 500)->default('')->comment('Remark');
            $table->tinyInteger('status')->default('0')->comment('Status (0 normal 1 stop)');
            $table->unique('dict_type', 'dict_type');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `dict_type` comment'Dictionary type table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dict_type');
    }
}
