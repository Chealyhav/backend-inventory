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
            $table->string('code')->unique();
            $table->integer('pieces')->nullable();
            $table->float('thickness')->nullable();
            $table->float('length')->nullable();
            $table->float('unit_weight')->nullable();
            $table->float('total_weight')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('buy_price', 10, 2)->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('currentStock')->default(0);
            $table->decimal('stockOut')->default(0);
            $table->decimal('stockIn')->default(0);

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');

            $table->boolean('status')->default(1);
            $table->boolean('remark')->default(1); // 1 = PreOrder, 2 = InStock
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null'); // Only this line
            $table->softDeletes();
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
