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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('subproduct_id');
            $table->integer('sale_type_id')->nullable(); // 1: Finished Good, 2: Material
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);  // base_price + adjustment
            $table->decimal('total_price', 10, 2);
            $table->string('notes')->nullable();
            $table->string('title')->nullable();
            $table->date('order_date')->useCurrent();
            $table->date('delivery_date')->nullable();
            $table->integer('status')->default(1); // 1: Pending, 2: Completed, 3: Cancelled
            $table->integer('invoice_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
