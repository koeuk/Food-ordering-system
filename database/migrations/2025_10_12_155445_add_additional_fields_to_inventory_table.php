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
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('minimum_stock');
            $table->string('location')->nullable()->after('unit');
            $table->date('expiry_date')->nullable()->after('location');
            $table->text('notes')->nullable()->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn(['unit', 'location', 'expiry_date', 'notes']);
        });
    }
};
