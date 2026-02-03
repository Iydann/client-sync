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
        if (! Schema::hasColumn('invoices', 'sent_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->timestamp('sent_at')->nullable()->after('due_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'sent_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('sent_at');
            });
        }
    }
};
