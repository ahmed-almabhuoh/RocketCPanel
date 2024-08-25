<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('project_name', 30)->unique();
            $table->string('project_version', 50)->nullable();
            $table->text('project_description')->nullable();
            $table->string('public_key', 50)->unique();
            $table->string('secret_key');
            $table->boolean('is_limited_usage')->default(false);
            $table->unsignedBigInteger('limited_usage_times')->nullable()->default(100);
            $table->unsignedBigInteger('usage_times')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_integrations');
    }
};
