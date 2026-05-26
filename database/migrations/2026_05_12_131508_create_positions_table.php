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
        Schema::create('positions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('location_id')->constrained()->cascadeOnDelete();
    $table->string('code');
    $table->string('floor')->nullable();
    $table->string('room')->nullable();
    $table->string('status')->default('active'); // active, inactive, empty
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->unique(['location_id', 'code']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
