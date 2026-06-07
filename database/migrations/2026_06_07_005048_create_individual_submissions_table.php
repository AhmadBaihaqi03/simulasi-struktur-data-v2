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
        Schema::create('individual_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individual_session_id')->constrained('individual_sessions')->onDelete('cascade');
            $table->string('student_name');
            $table->string('student_number');
            $table->string('class_name');
            $table->integer('total_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_submissions');
    }
};
