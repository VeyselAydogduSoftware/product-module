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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');
            $table->longText('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('price_sale', 10, 2)->nullable();
            $table->tinyInteger('price_sale_type')->comment('1:percent, 2:amount')->nullable();
            $table->bigInteger('quantity')->comment('stok modülü yazılarak stok takibi yapılabilir')->nullable();
            $table->unsignedBigInteger('history_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
