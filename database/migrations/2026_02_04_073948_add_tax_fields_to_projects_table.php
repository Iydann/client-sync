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
        Schema::table('projects', function (Blueprint $table) {
            // Tax configuration
            $table->boolean('include_tax')->default(true)->after('deadline')->comment('Whether contract_value includes tax');
            $table->decimal('ppn_rate', 5, 2)->default(11.00)->after('include_tax')->comment('PPN rate percentage');
            $table->decimal('pph_rate', 5, 2)->default(2.50)->after('ppn_rate')->comment('PPH rate percentage');
            
            // Tax amounts (calculated/stored)
            $table->decimal('ppn_amount', 15, 0)->default(0)->after('pph_rate')->comment('PPN amount in currency');
            $table->decimal('pph_amount', 15, 0)->default(0)->after('ppn_amount')->comment('PPH amount in currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'include_tax',
                'ppn_rate',
                'pph_rate',
                'ppn_amount',
                'pph_amount',
            ]);
        });
    }
};
