<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {

            $table->foreignId('brand_id')
                ->nullable()
                ->after('asset_type_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('asset_model_id')
                ->nullable()
                ->after('brand_id')
                ->constrained('asset_models')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {

            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('asset_model_id');
        });
    }
};