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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->id('audit_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medicalrecord_id')->nullable();
            $table->enum('action', ['view', 'create']);
            $table->timestamp('timestamp')->useCurrent();
            $table->longText('blockchain_hash');

            $table->foreign('users_id')->references('idusers')->on('users');
            $table->foreign('patient_id')->references('patient_id')->on('patients');
            $table->foreign('medicalrecord_id')->references('medicalrecord_id')->on('medical_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail');
    }
};
