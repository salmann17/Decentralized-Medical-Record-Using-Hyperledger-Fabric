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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id('iditem');
            $table->unsignedBigInteger('prescription_id');
            $table->string('name', 135); // Nama obat
            $table->string('dosage', 45); // Dosis (misal: 125mg, 500mg)
            $table->string('frequency', 45); // Frekuensi (misal: 3x sehari)
            $table->string('duration', 45); // Durasi (misal: 5 hari)
            $table->longText('notes')->nullable(); // Catatan tambahan (nullable)

            $table->foreign('prescription_id')->references('idprescription')->on('prescriptions');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
