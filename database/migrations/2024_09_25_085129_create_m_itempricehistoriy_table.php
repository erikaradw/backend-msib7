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
        Schema::create('m_itempricehistory', function (Blueprint $table) {
            $table->id();
            $table->integer('yop')->nullable();
            $table->integer('mop')->nullable();
            $table->string('price_code')->nullable();
            $table->string('item_code')->nullable();
            $table->string('mtg_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('price')->nullable();
            
                        
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
        Schema::dropIfExists('m_itempricehistory');
    }
};