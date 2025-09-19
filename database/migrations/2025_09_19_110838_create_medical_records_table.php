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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id('medicalrecord_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('doctor_id');
            $table->date('visit_date');
            $table->string('diagnosis_code', 45);
            $table->string('diagnosis_desc', 135);
            $table->string('treatment', 135);
            $table->string('notes', 135);
            $table->enum('status', ['draft', 'final', 'immutable']);
            $table->unsignedBigInteger('prescription_id');

            $table->foreign('patient_id')->references('patient_id')->on('patients');
            $table->foreign('hospital_id')->references('hospital_id')->on('hospitals');
            $table->foreign('doctor_id')->references('doctor_id')->on('doctors');
            $table->foreign('prescription_id')->references('prescription_id')->on('prescriptions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
