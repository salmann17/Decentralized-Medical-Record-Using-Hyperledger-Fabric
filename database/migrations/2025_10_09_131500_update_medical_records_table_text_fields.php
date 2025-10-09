<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Update field types from string to longText for better data storage
            $table->longText('diagnosis_desc')->change();
            $table->longText('treatment')->change();
            $table->longText('notes')->nullable()->change();
            
            // Add immutable status option
            DB::statement("ALTER TABLE medical_records MODIFY COLUMN status ENUM('draft', 'final', 'immutable') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Revert back to original string types
            $table->string('diagnosis_desc', 135)->change();
            $table->string('treatment', 135)->change();
            $table->string('notes', 135)->nullable()->change();
            
            // Revert status enum
            DB::statement("ALTER TABLE medical_records MODIFY COLUMN status ENUM('draft', 'final') NOT NULL");
        });
    }
};
