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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id('idaudit');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('medicalrecord_id')->nullable();
            $table->enum('action', ['view', 'create', 'update']);
            $table->timestamp('timestamp')->useCurrent();
            $table->longText('blockchain_hash')->nullable();

            $table->foreign('patient_id')->references('idpatient')->on('patients');
            $table->foreign('doctor_id')->references('iddoctor')->on('doctors');
            $table->foreign('medicalrecord_id')->references('idmedicalrecord')->on('medical_records');

            $table->timestamps();
            $table->softDeletes();
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
