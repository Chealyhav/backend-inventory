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
            //invoice_number 0000 00 00 00 00
            $table->string('invoice_number')->unique();
            $table->integer('customer_id')->nullable();
            $table->decimal('updated_price', 10, 2)->default(0);
            $table->integer('sale_id');
            $table->decimal('total_price', 10, 2);
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->string('service_name')->nullable();
            $table->string('payment_method')->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->integer('payment_id')->nullable();

            $table->string('notes')->nullable();
            $table->date('invoice_date')->useCurrent();
            // User tracking
            $table->boolean('status')->default(1);

            
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();


            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
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
