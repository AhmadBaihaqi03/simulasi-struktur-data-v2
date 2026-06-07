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
        Schema::create('group_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pbl_group_session_id')->constrained('pbl_group_sessions')->onDelete('cascade');
            $table->string('group_name');
            $table->json('student_data');
            $table->string('class_name')->nullable();       
            $table->json('f3_answers')->nullable();             
            $table->text('f4_link')->nullable();                
            $table->text('f4_answers')->nullable();             
            $table->json('f5_answers')->nullable();             
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();;
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_answers');
    }
};
