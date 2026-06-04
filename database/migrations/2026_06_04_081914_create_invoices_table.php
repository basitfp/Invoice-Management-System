<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: add agent_id to invoices table.
 *
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Nullable so existing admin-created invoices are unaffected
            $table->foreignId('agent_id')
                  ->nullable()
                  ->after('customer_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'agent_id');
            $table->dropColumn('agent_id');
        });
    }
};
