<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->boolean('status')->default(1)->comment('1=Enabled,0=Disabled');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manufacturers');
    }
};