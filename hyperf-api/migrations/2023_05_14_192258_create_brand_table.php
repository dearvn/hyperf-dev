<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateBrandTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_brands', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('brand_name');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to_email');
            $table->string('brand_logo')->nullable();
            $table->string('smtp_provider')->nullable();
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_ssl')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('custom_domain_protocol')->nullable();
            $table->string('custom_domain')->nullable();
            $table->boolean('enable_custom_domain')->nullable();
            $table->string('recaptcha_sitekey')->nullable();
            $table->string('recaptcha_secretkey')->nullable();
            $table->string('gdpr_options')->nullable();
            $table->tinyInteger('opens_tracking')->nullable();
            $table->tinyInteger('clicks_tracking')->nullable();
            $table->string('query_string')->nullable();
            $table->string('test_email_prefix')->nullable();
            $table->string('allowed_attachments')->nullable();
            $table->string('sort_by')->nullable();
            $table->string('brand_report_rows')->nullable();
            $table->string('hide_hidden_lists')->nullable();
            $table->string('login_email')->nullable();
            $table->string('generate_password')->nullable();
            $table->string('language')->nullable();
            $table->string('client_privileges')->nullable();
            $table->string('notify_campaign_sent')->nullable();
            $table->string('currency')->nullable();
            $table->string('delivery_fee')->nullable();
            $table->string('cost_per_recipient')->nullable();
            $table->string('choose_limit')->nullable();
            $table->string('monthly_limit')->nullable();
            $table->string('current_limit')->nullable();
            $table->string('reset_on_day')->nullable();

            $table->timestamps();
        });
        \Hyperf\DbConnection\Db::statement("ALTER TABLE `marketing_brands` comment'marketing_brands'");//Table comments must be added with prefixes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_brands');
    }
}
