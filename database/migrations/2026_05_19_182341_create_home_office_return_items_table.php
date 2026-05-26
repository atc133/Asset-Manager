<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_office_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('home_office_return_case_id')
                ->constrained('home_office_return_cases')
                ->cascadeOnDelete();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->foreignId('replacement_asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            $table->string('status')->default('pending');
            // pending, returned, damaged, missing, replaced, not_required

            $table->timestamp('returned_at')->nullable();

            $table->string('condition_on_return')->nullable();
            // new, good, used, needs_check, damaged, broken, missing_serial

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['home_office_return_case_id', 'asset_id'], 'ho_return_case_asset_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_office_return_items');
    }
};