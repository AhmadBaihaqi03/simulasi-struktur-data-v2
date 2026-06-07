<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE individual_questions MODIFY type ENUM('multiple_choice', 'drag_drop', 'checkbox', 'grouping') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE individual_questions MODIFY type ENUM('multiple_choice', 'drag_drop', 'checkbox') NOT NULL");
    }
};