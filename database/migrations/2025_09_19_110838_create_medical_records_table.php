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
            $table->id('idmedicalrecord');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('admin_id');
            $table->date('visit_date');
            $table->string('blood_pressure', 45);
            $table->integer('heart_rate');
            $table->decimal('temperature', 4, 1);
            $table->integer('respiratory_rate');

            $table->longText('chief_complaint')->nullable();
            $table->longText('history_present_illness')->nullable();
            $table->longText('physical_examination')->nullable();

            $table->string('diagnosis_code', 45);
            $table->string('diagnosis_desc', 135);
            $table->string('treatment', 135);
            $table->string('notes', 135)->nullable();
            $table->enum('status', ['draft', 'final']);
            $table->integer('version');

            $table->foreign('patient_id')->references('idpatient')->on('patients');
            $table->foreign('doctor_id')->references('iddoctor')->on('doctors');
            $table->foreign('admin_id')->references('idadmin')->on('admins');

            $table->timestamps();
            $table->softDeletes();
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
