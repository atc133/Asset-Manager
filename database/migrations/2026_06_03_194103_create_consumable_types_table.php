<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('category')->nullable();

            $table->integer('minimum_stock')->default(0);
            $table->integer('current_stock')->default(0);

            $table->boolean('active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['name', 'category']);
            $table->index('category');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_types');
    }
};