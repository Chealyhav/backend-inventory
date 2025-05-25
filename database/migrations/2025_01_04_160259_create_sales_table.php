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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->enum('sale_type', ['material', 'finished_good']);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->integer('order_id');
            $table->integer('customer_id')->nullable();
            $table->integer('sale_type_id');
            $table->timestamp('sale_date')->useCurrent();
            $table->boolean('status')->default(true);
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('sales');
    }
};
