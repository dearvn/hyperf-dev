<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddFieldCampaignTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->text('plain_text')->nullable();
            $table->text('html_text')->nullable();
            $table->json('list_ids')->nullable();
            $table->json('segment_ids')->nullable();
            $table->json('exclude_list_ids')->nullable();

            $table->json('exclude_segments_ids')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('query_string')->nullable();
            $table->integer('track_opens')->nullable();
            $table->integer('track_clicks')->nullable();
            $table->integer('send_campaign')->nullable();
            $table->dateTime('schedule_date_time')->nullable();
            $table->dateTimeTz('schedule_timezone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
