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
        Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->string('asset_tag')->unique();
    $table->foreignId('asset_type_id')->constrained()->cascadeOnDelete();

    $table->string('brand')->nullable();
    $table->string('model')->nullable();
    $table->string('serial_number')->nullable();

    $table->string('status')->default('available'); 
    // available, assigned, in_storage, in_repair, damaged, lost, retired

    $table->string('condition')->default('good');
    // new, good, used, needs_check, damaged, broken, missing_serial

    $table->foreignId('current_location_id')->nullable()->constrained('locations')->nullOnDelete();
    $table->foreignId('current_position_id')->nullable()->constrained('positions')->nullOnDelete();
    $table->foreignId('current_employee_id')->nullable()->constrained('employees')->nullOnDelete();

    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('serial_number');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
