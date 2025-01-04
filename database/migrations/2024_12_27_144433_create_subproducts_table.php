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
        Schema::create('subproducts', function (Blueprint $table) {
            $table->id(); // auto-incrementing ID column
            $table->string('code');
            $table->integer('pieces');
            $table->float('thickness');
            $table->float('length');
            $table->float('unit_weight');
            $table->float('total_weight');
            $table->decimal('sale_price', 10, 2); // Correct for monetary values
            $table->decimal('buy_price', 10, 2);  // Correct for monetary values
            $table->unsignedBigInteger('product_id'); // Use unsignedBigInteger for foreign key
            $table->unsignedBigInteger('color_id'); // Use unsignedBigInteger for foreign key

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subproducts');
    }
};
