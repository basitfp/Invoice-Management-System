<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // ── Core ─────────────────────────────────────────
            $table->string('name');
            $table->string('item_code', 100)->nullable()->unique()
                  ->comment('SKU / barcode');
            $table->string('regional_name', 255)->nullable()
                  ->comment('Local / regional language name');
            $table->text('description')->nullable();

            // ── Classification ───────────────────────────────
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->foreign('manufacturer_id')
                  ->references('id')
                  ->on('manufacturers')
                  ->nullOnDelete();

            $table->string('item_class', 100)->nullable()
                  ->comment('e.g. Goods, Service, Consumable');
            $table->string('hsn_code', 50)->nullable()
                  ->comment('Harmonised System Nomenclature code');
            $table->string('unit', 50)->nullable()
                  ->comment('e.g. pcs, kg, litre, box');

            // ── Media ────────────────────────────────────────
            $table->string('image', 255)->nullable()
                  ->comment('Storage path to product image');

            // ── Inventory ────────────────────────────────────
            $table->integer('qty')->default(0);
            $table->integer('moq')->default(1)
                  ->comment('Minimum order quantity');
            $table->boolean('is_weighing_item')->default(false)
                  ->comment('True if sold by weight');

            // ── Pricing ──────────────────────────────────────
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);

            // ── Tax ──────────────────────────────────────────
            $table->unsignedTinyInteger('vat')->default(0)
                  ->comment('VAT rate: 0 or 20');
            $table->boolean('purchase_tax_inclusive')->default(false)
                  ->comment('Is purchase price tax-inclusive?');
            $table->boolean('sale_tax_inclusive')->default(false)
                  ->comment('Is selling price tax-inclusive?');
            $table->decimal('cess_percentage', 5, 2)->default(0)
                  ->comment('Cess / surcharge percentage');
            $table->decimal('additional_cess', 5, 2)->default(0)
                  ->comment('Additional cess amount or percentage');

            // ── Discount ─────────────────────────────────────
            $table->decimal('discount_percentage', 5, 2)->default(0)
                  ->comment('Default trade discount %');

            // ── Status ───────────────────────────────────────
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};