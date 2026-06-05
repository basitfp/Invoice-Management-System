<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('company', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('tax_reg_number', 50)->nullable()->unique();
            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin_code', 20)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }

};