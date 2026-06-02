<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reserved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('reserved_from')->nullable();

            $table->date('reserved_until')->nullable();

            $table->string('status')->default('active');
            // active, completed, cancelled

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['asset_id', 'status']);
            $table->index(['employee_id', 'status']);
            $table->index('reserved_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_reservations');
    }
};