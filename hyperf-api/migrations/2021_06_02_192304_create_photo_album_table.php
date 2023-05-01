<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreatePhotoAlbumTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photo_album', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('album_name', 191)->default('')->comment('Album name');
            $table->string('album_desc', 500)->default('')->comment('album description');
            $table->string('album_cover', 500)->default('')->comment('Album cover map');
            $table->integer('album_type')->default('0')->comment('Album classification');
            $table->string('album_author', 255)->default('0')->comment('Album author');
            $table->integer('album_click_num')->default('0')->comment('Number of album views');
            $table->tinyInteger('album_status')->default('1')->comment('Album State 1: Enable 0: Disable');
            $table->string('album_question', 500)->default('1')->comment('Problem of visiting albums');
            $table->string('album_answer', 500)->default('1')->comment('Visit the password of the album');
            $table->integer('album_sort')->default('99')->comment('The smaller the number of album sorting');
            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `photo_album` comment'Album'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_album');
    }
}
