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
        Schema::create('m__cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cabang')->nullable();
            $table->string('nama_cabang')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('dist_code')->nullable();
            $table->string('area_code')->nullable();
            $table->string('area_name')->nullable();
            $table->string('region_code')->nullable();
            $table->string('region_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m__cabangs');
    }
};
