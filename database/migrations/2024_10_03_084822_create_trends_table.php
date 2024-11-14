<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('trends', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('dist_code')->nullable(); 
            $table->string('chnl_code')->nullable(); 
            $table->string('region_name')->nullable();
            $table->string('area_name')->nullable(); 
            $table->string('nama_cabang')->nullable(); 
            $table->string('parent_code')->nullable(); 
            $table->string('item_code')->nullable(); 
            $table->string('item_name')->nullable(); 
            $table->string('brand_name')->nullable(); 
            $table->string('kategori')->nullable(); 
            $table->string('status_product')->nullable(); 
            $table->string('tahun')->nullable(); 
            $table->integer('month_1')->nullable();
            $table->integer('month_2')->nullable();
            $table->integer('month_3')->nullable();
            $table->integer('month_4')->nullable();
            $table->integer('month_5')->nullable();
            $table->integer('month_6')->nullable();
            $table->integer('month_7')->nullable();
            $table->integer('month_8')->nullable();
            $table->integer('month_9')->nullable();
            $table->integer('month_10')->nullable();
            $table->integer('month_11')->nullable();
            $table->integer('month_12')->nullable();
            $table->integer('yearly_average_unit')->nullable();
            $table->integer('yearly_average_value')->nullable();
            $table->integer('average_9_month_unit')->nullable();
            $table->integer('average_9_month_value')->nullable();
            $table->integer('average_6_month_unit')->nullable();
            $table->integer('average_6_month_value')->nullable();
            $table->integer('average_3_month_unit')->nullable();
            $table->integer('average_3_month_value')->nullable();
            $table->integer('average_sales')->nullable();
            $table->integer('purchase_suggestion')->nullable();
            $table->integer('purchase_value')->nullable();
            $table->integer('stock_on_hand_unit')->nullable();
            $table->integer('doi_3_month')->nullable();
            $table->string('status_trend')->nullable();
            $table->decimal('delta', 8, 2)->nullable();
            $table->integer('qty_po')->nullable();
            $table->integer('qty_sc_reg')->nullable();
            $table->decimal('service_level', 5, 2)->nullable();
            $table->string('pic')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trends');
    }
};
