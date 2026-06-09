<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->after('invoice_date')->nullable();
        });

        // MySQL needs an explicit enum-to-varchar conversion; SQLite stores enums as strings already.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status VARCHAR(50) DEFAULT 'unpaid'");
        }

        // Now safe to update invalid old enum values to 'unpaid'
        DB::table('invoices')
            ->whereNotIn('status', ['draft', 'paid', 'unpaid', 'due'])
            ->update(['status' => 'unpaid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });

        // Optional: Convert back to original enum
        DB::table('invoices')
            ->whereNotIn('status', ['draft', 'sent', 'paid', 'cancelled'])
            ->update(['status' => 'draft']);
            
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status ENUM('draft', 'sent', 'paid', 'cancelled') DEFAULT 'draft'");
        }
    }
};
