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
        Schema::create('m__customers', function (Blueprint $table) {
            $table->id();
            $table->string('dist_code')->nullable();
            $table->string('region_name')->nullable();
            $table->string('area_code')->nullable();
            $table->string('kode_cabang')->nullable();
            $table->string('cust_code')->nullable();
            $table->string('cust_name')->nullable();
            $table->string('chnl_code')->nullable();
            $table->string('item_code')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
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
        Schema::dropIfExists('m__customers');
    }
};
