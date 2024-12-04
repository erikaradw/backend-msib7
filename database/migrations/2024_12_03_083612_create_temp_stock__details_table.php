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
        Schema::create('temp_stock__details', function (Blueprint $table) {
            $table->id();
            $table->string('tahun')->nullable();
            $table->string('bulan')->nullable();
            $table->string('dist_code')->nullable();
            $table->string('kode_cabang')->nullable();
            $table->string('brch_name')->nullable();
            $table->string('item_code')->nullable();
            $table->string('on_hand_unit')->nullable();
            $table->boolean('data_baru')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_stock__details');
    }
};
