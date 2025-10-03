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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('image')->nullable();
            $table->string('source_url')->nullable();
            $table->date('poll_date')->nullable();
            $table->unsignedInteger('yes_votes')->default(0);
            $table->unsignedInteger('no_votes')->default(0);
            $table->unsignedInteger('no_opinion_votes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
