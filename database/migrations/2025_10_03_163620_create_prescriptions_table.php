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
            $table->longText('instructions');

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
