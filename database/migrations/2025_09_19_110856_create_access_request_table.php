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
        Schema::create('access_request', function (Blueprint $table) {
            $table->id('idrequest');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('admin_id');
            $table->enum('status', ['pending', 'approved', 'rejected', 'revoked']);
            $table->dateTime('requested_at');
            $table->dateTime('responded_at')->nullable();

            $table->foreign('patient_id')->references('idpatient')->on('patients');
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
        Schema::dropIfExists('access_request');
    }
};
