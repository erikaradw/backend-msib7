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
        Schema::create('m_price', function (Blueprint $table) {
            $table->id(); 
            $table->string('price_code')->nullable(); 
            $table->string('price_name')->nullable(); 
            // $table->integer('obj_type')->nullable(); 
            $table->boolean('flag_active')->default(true); 
            $table->string('created_by')->nullable(); 
            $table->string('updated_by')->nullable(); 
            $table->string('deleted_by')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_price');
    }
};
