<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m__products', function (Blueprint $table) {
            $table->id();

            $table->string('brand_code')->nullable();
            $table->string('brand_name')->nullable();
            // $table->string('mb_code')->nullable();
            $table->string('parent_code')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('price_code')->nullable();
            $table->string('price')->nullable();
            $table->string('status_product')->nullable();


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
        Schema::dropIfExists('m__products');
    }
};
