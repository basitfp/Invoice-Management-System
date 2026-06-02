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

        $table->string('name');
        $table->text('description')->nullable();

        // FK
        $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

        $table->integer('qty')->default(0);

        $table->decimal('purchase_price', 10, 2);
        $table->decimal('selling_price', 10, 2);

        // VAT: 0 or 20
        $table->unsignedTinyInteger('vat')->default(0);

        $table->integer('moq')->default(1); // minimum order quantity

        $table->boolean('status')->default(1); // enable/disable

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
