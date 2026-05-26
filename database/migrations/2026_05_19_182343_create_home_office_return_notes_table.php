<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_office_return_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('home_office_return_case_id')
                ->constrained('home_office_return_cases')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('contact_type')->default('note');
            // note, phone, email, sms, in_person, other

            $table->text('note');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_office_return_notes');
    }
};