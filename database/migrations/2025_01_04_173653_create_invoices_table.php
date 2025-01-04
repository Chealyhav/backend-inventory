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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->date('invoice_date');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('augmented_price', 10, 2)->default(0);
            $table->decimal('kg_price', 10, 2)->default(0);
            $table->decimal('kg', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->string('service_name')->nullable();
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method')->default('cash');
            $table->string('notes')->nullable();
            // User tracking
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
        Schema::dropIfExists('invoices');
    }
};
