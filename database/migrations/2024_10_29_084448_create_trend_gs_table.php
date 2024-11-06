<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('trend_gs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('dist_code')->nullable(); // item_code (string, unique)
            $table->string('chnl_code')->nullable(); // item_name (string, nullable)
            $table->string('region_name')->nullable(); // name_bars (string, nullable)
            $table->string('area_name')->nullable();
            $table->string('kode_cabang')->nullable(); 
            $table->string('branch_code')->nullable(); 
            $table->string('nama_cabang')->nullable(); 
            $table->string('parent_code')->nullable(); 
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
            $table->string('desember')->nullable(); 
            $table->string('beli_januari')->nullable();
            $table->string('beli_februari')->nullable();
            $table->string('beli_maret')->nullable();
            $table->string('beli_april')->nullable();
            $table->string('beli_mei')->nullable();
            $table->string('beli_juni')->nullable();
            $table->string('beli_juli')->nullable();
            $table->string('beli_agustus')->nullable();
            $table->string('beli_september')->nullable();
            $table->string('beli_oktober')->nullable();
            $table->string('beli_november')->nullable();
            $table->string('beli_desember')->nullable();
            $table->string('januari1')->nullable();
            $table->string('februari1')->nullable();
            $table->string('maret1')->nullable();
            $table->string('april1')->nullable();
            $table->string('mei1')->nullable();
            $table->string('juni1')->nullable();
            $table->string('juli1')->nullable();
            $table->string('agustus1')->nullable();
            $table->string('september1')->nullable();
            $table->string('oktober1')->nullable();
            $table->string('november1')->nullable();
            $table->string('desember1')->nullable();
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
        Schema::dropIfExists('trend_gs');
    }
};
