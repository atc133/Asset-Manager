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
        Schema::create('maintenance_cases', function (Blueprint $table) {
    $table->id();

    $table->foreignId('asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

    $table->foreignId('handled_by_user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->string('issue');
    $table->text('description')->nullable();
    $table->text('action_taken')->nullable();

    $table->string('status')->default('open');
    // open, in_progress, fixed, cannot_fix, retired
    
    $table->timestamp('opened_at')->nullable();
    $table->timestamp('closed_at')->nullable();

    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_cases');
    }
};
