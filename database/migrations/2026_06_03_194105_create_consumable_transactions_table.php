<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consumable_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');
            // stock_in, stock_out, adjustment

            $table->integer('quantity');

            $table->integer('stock_before')->default(0);
            $table->integer('stock_after')->default(0);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['consumable_type_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_transactions');
    }
};