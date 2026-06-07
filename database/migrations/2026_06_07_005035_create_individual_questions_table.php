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
        Schema::create('individual_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individual_session_id')->constrained('individual_sessions')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'drag_drop', 'checkbox', 'grouping']);
            $table->text('question_text');
            $table->json('options');
            $table->json('correct_answer');
            $table->integer('points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_questions');
    }
};
