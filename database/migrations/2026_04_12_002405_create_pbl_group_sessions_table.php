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
        Schema::create('pbl_group_sessions', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_code')->unique();
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->text('f1_context')->nullable();             // Scenario Narrative
            $table->json('f1_learning_objectives')->nullable(); // learning objectives
            $table->json('f3_questions')->nullable();           // Investigation Tasks
            $table->json('f5_questions')->nullable();           // Evaluation Questions 
            $table->text('f4_instruction')->nullable();         // Implementation Instruction
            $table->text('f4_question')->nullable();            // Descrition System Question
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbl_group_sessions');
    }
};
