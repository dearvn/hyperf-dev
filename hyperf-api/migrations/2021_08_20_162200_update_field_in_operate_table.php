<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UpdateFieldInOperateTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Friends chat watch increase file suffix
        Schema::table('operate_log', function (Blueprint $table) {
            $table->string('target_class', 255)->change();
            $table->string('target_method', 255)->change();
            $table->string('request_method', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
