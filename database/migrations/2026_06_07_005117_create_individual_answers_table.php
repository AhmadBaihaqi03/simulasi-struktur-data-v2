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
        Schema::create('individual_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individual_submission_id')->constrained('individual_submissions')->onDelete('cascade');
            $table->foreignId('individual_question_id')->constrained('individual_questions')->onDelete('cascade');
            $table->json('answer_given');
            $table->boolean('is_correct');
            $table->integer('score_earned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_answers');
    }
};
