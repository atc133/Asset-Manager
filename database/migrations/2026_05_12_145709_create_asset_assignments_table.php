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
        Schema::create('asset_assignments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

    $table->string('assignment_type');
    // employee, position, storage, repair, retired, lost

    $table->foreignId('employee_id')
        ->nullable()
        ->constrained('employees')
        ->nullOnDelete();

    $table->foreignId('position_id')
        ->nullable()
        ->constrained('positions')
        ->nullOnDelete();

    $table->foreignId('location_id')
        ->nullable()
        ->constrained('locations')
        ->nullOnDelete();

    $table->timestamp('assigned_from')->nullable();
    $table->timestamp('assigned_until')->nullable();

    $table->string('status')->default('active');
    // active, completed, cancelled

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->index(['asset_id', 'status']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
