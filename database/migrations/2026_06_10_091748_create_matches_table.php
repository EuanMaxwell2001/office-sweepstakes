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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('espn_id')->nullable()->unique();
            $table->string('home_team');
            $table->string('away_team');
            $table->string('home_team_abbr', 10)->nullable();
            $table->string('away_team_abbr', 10)->nullable();
            $table->string('home_flag')->nullable();
            $table->string('away_flag')->nullable();
            $table->tinyInteger('home_score')->nullable();
            $table->tinyInteger('away_score')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, live, finished
            $table->string('status_detail')->nullable();
            $table->timestamp('match_date')->nullable();
            $table->string('stage')->nullable(); // Group Stage, Round of 16, etc.
            $table->string('group_name')->nullable();
            $table->string('venue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
