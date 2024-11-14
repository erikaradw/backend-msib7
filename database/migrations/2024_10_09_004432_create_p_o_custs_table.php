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
        Schema::create('p_o_custs', function (Blueprint $table) {
            $table->id();
            $table->string('dist_code')->nullable();
            $table->string('tgl_order')->nullable();
            $table->string('mtg_code')->nullable();
            $table->string('qty_sc_reg')->nullable();
            $table->string('qty_po')->nullable();
            $table->string('branch_code')->nullable();
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
        Schema::dropIfExists('p_o_custs');
    }
};
