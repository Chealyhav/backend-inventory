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
            $table->integer('pieces')->nullable();
            $table->float('thickness')->nullable();
            $table->float('length')->nullable();
            $table->float('unit_weight')->nullable();
            $table->float('total_weight')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable(); // Correct for monetary values
            $table->decimal('buy_price', 10, 2)->nullable();  // Correct for monetary values
            $table->unsignedBigInteger('product_id'); // Use unsignedBigInteger for foreign key
            $table->unsignedBigInteger('color_id'); // Use unsignedBigInteger for foreign key
            $table->string('status')->default('active');
            $table->decimal('discount', 10, 2)->default(0);
            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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
