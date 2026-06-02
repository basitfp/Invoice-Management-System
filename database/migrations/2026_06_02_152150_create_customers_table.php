<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            $table->enum('customer_type', ['regular', 'business'])->default('regular');
            
            $table->text('address')->nullable();
            
            $table->boolean('vat_registered')->default(false);
            $table->string('vat_number')->nullable();
            
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes(); // Optional but recommended
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};