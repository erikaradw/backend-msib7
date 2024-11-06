<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('stds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('yop'); // item_code (string, unique)
            $table->string('mop')->nullable(); // item_name (string, nullable)
            $table->string('distCode')->nullable(); // code_bars (string, nullable)
            $table->string('brchName')->nullable(); // mnft_code (string, nullable)
            $table->string('itemCode')->nullable(); // sales_item (string, nullable)
            $table->string('onHandUnit')->nullable(); // purch_item (string, nullable)
            // $table->string('sku')->nullable(); // return_item (boolean, nullable)
            // $table->string('brand')->nullable(); // uom1 (string, nullable)
            // $table->string('kategori')->nullable(); // uom2 (string, nullable)
            // $table->string('status_product')->nullable(); // uom3 (double, nullable)
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
        Schema::dropIfExists('stds');
    }
};
