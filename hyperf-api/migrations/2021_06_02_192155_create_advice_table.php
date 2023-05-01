<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAdviceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advice', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('User ID');
            $table->string('title', 50)->default('')->comment('title');
            $table->longText('content')->comment('content');
            $table->tinyInteger('status')->default('0')->comment('Status (0: To be resolved, 1: Solved, 2: Close)');
            $table->longText('reply')->comment('Recovery content');
            $table->tinyInteger('type')->default('0')->comment('Type (0: BUG, 1: Optimization, 2: Mixed');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `advice` comment'System Suggestion Table'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advice');
    }
}
