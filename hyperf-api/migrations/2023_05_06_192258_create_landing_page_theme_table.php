<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLandingPageThemeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landingpage_themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description', 200);
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `landingpage_themes` comment'landingpage_themes'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landingpage_themes');
    }
}
