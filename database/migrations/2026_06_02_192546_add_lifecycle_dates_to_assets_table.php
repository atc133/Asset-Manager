<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {

            $table->date('received_at')->nullable()->after('serial_number');

            $table->date('warranty_until')->nullable()->after('received_at');

            $table->date('expected_replacement_at')
                ->nullable()
                ->after('warranty_until');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {

            $table->dropColumn([
                'received_at',
                'warranty_until',
                'expected_replacement_at',
            ]);
        });
    }
};