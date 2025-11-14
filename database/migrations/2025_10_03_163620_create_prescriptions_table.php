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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id('idprescription');
            $table->unsignedBigInteger('medicalrecord_id');
            $table->enum('type', ['single', 'compound']);
            $table->string('name', 135); 
            $table->string('dosage', 45); 
            $table->string('frequency', 45); 
            $table->string('duration', 45); 
            $table->longText('description');

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
        Schema::dropIfExists('prescriptions');
    }
};
