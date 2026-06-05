<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // -- Core --
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('customer_type', ['individual', 'company'])->default('individual');

            // -- Personal --
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birthdate')->nullable();

            // -- Billing address --
            $table->text('address')->nullable();

            // -- Shipping / extended address --
            $table->string('shipping_address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin_code', 20)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('landmark', 255)->nullable();

            // -- Area FK --
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')
                  ->references('id')
                  ->on('areas')
                  ->nullOnDelete();

            // -- Credit terms --
            $table->unsignedSmallInteger('credit_days')->nullable()
                  ->comment('Payment due days e.g. 30, 60, 90');
            $table->decimal('credit_limit', 10, 2)->nullable()
                  ->comment('Maximum credit amount allowed');

            // -- Tax --
            $table->boolean('vat_registered')->default(false);
            $table->string('vat_number', 50)->nullable();

            // -- Status --
            $table->boolean('status')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};