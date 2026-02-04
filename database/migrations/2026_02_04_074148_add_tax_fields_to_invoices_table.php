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
        Schema::table('invoices', function (Blueprint $table) {
            // Tax snapshot fields (captured at invoice creation time)
            $table->decimal('ppn_rate', 5, 2)->nullable()->after('amount')->comment('PPN rate snapshot');
            $table->decimal('pph_rate', 5, 2)->nullable()->after('ppn_rate')->comment('PPH rate snapshot');
            $table->decimal('ppn_amount', 15, 0)->default(0)->after('pph_rate')->comment('PPN amount snapshot');
            $table->decimal('pph_amount', 15, 0)->default(0)->after('ppn_amount')->comment('PPH amount snapshot');
            $table->boolean('include_tax')->default(false)->after('pph_amount')->comment('Whether amount includes tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'ppn_rate',
                'pph_rate',
                'ppn_amount',
                'pph_amount',
                'include_tax',
            ]);
        });
    }
};
