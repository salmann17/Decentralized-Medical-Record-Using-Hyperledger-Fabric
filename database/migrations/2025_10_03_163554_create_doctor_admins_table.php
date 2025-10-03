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
        Schema::create('doctors_admins', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('admin_id');

            $table->primary(['doctor_id', 'admin_id']);

            $table->foreign('doctor_id')->references('iddoctor')->on('doctors')->onDelete('cascade');
            $table->foreign('admin_id')->references('idadmin')->on('admins')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_admins');
    }
};
