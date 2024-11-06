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
            $table->string('dist_code')->nullable(); // item_code (string, unique)
            $table->string('chnl_code')->nullable(); // item_name (string, nullable)
            $table->string('region_name')->nullable(); // name_bars (string, nullable)
            $table->string('area_name')->nullable(); // mnft_code (string, nullable)
            $table->string('nama_cabang')->nullable(); // sales_item (string, nullable)
            $table->string('parent_code')->nullable(); // sales_item (string, nullable)
            $table->string('item_code')->nullable(); // purch_item (string, nullable)
            $table->string('item_name')->nullable(); // return_item (boolean, nullable)
            $table->string('brand_name')->nullable(); // uom1 (string, nullable)
            $table->string('kategori')->nullable(); // uom2 (string, nullable)
            $table->string('status_product')->nullable(); // uom3 (double, nullable)
            $table->string('tahun')->nullable(); // uom3 (double, nullable)
            $table->string('januari')->nullable(); // uom3 (double, nullable)
            $table->string('februari')->nullable(); // uom3 (double, nullable)
            $table->string('maret')->nullable(); // uom3 (double, nullable)
            $table->string('april')->nullable(); // uom3 (double, nullable)
            $table->string('mei')->nullable(); // uom3 (double, nullable)
            $table->string('juni')->nullable(); // uom3 (double, nullable)
            $table->string('juli')->nullable(); // uom3 (double, nullable)
            $table->string('agustus')->nullable(); // uom3 (double, nullable)
            $table->string('september')->nullable(); // uom3 (double, nullable)
            $table->string('oktober')->nullable(); // uom3 (double, nullable)
            $table->string('november')->nullable(); // uom3 (double, nullable)
            $table->string('desember')->nullable(); // uom3 (double, nullable)
            $table->string('unit12')->nullable();
            $table->string('value12')->nullable();
            $table->string('unit9')->nullable();
            $table->string('value9')->nullable();
            $table->string('unit6')->nullable();
            $table->string('value6')->nullable();
            $table->string('unit3')->nullable();
            $table->string('value3')->nullable();
            $table->string('beli_januari')->nullable();
            $table->string('januari1')->nullable();
            $table->string('beli_februari')->nullable();
            $table->string('februari1')->nullable();
            $table->string('beli_maret')->nullable();
            $table->string('maret1')->nullable();
            $table->string('beli_april')->nullable();
            $table->string('april1')->nullable();
            $table->string('beli_mei')->nullable();
            $table->string('mei1')->nullable();
            $table->string('beli_juni')->nullable();
            $table->string('juni1')->nullable();
            $table->string('beli_juli')->nullable();
            $table->string('juli1')->nullable();
            $table->string('beli_agustus')->nullable();
            $table->string('agustus1')->nullable();
            $table->string('beli_september')->nullable();
            $table->string('september1')->nullable();
            $table->string('beli_oktober')->nullable();
            $table->string('oktober1')->nullable();
            $table->string('beli_november')->nullable();
            $table->string('november1')->nullable();
            $table->string('beli_desember')->nullable();
            $table->string('desember1')->nullable();
            $table->string('doi3bulan')->nullable(); // Tambahkan kolom doi3bulan
            $table->string('status_trend')->nullable(); // Tambahkan kolom status_trend
            $table->string('delta')->nullable(); // Tambahkan kolom delta
            $table->string('pic')->nullable(); // Tambahkan kolom pic
            $table->string('average_sales')->nullable(); // Tambahkan kolom average_sales
            $table->string('purchase_suggestion')->nullable(); // Ubah kolom PURCHASE_SUGESTION menjadi purchase_suggestion
            $table->string('purchase_value')->nullable(); // Ubah kolom PURCHASE_VALUE menjadi purchase_value
            // $table->double('uom4')->nullable(); // uom4 (double, nullable)
            // $table->integer('obj_type')->nullable(); // obj_type (int, nullable)
            // $table->boolean('flag_active')->default(true)->nullable(); // flag_active (boolean, default: true, nullable)

            $table->string('created_by')->nullable(); // created_by (string, nullable)
            $table->string('updated_by')->nullable(); // updated_by (string, nullable)
            $table->string('deleted_by')->nullable(); // deleted_by (string, nullable)
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
