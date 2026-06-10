<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumable_transactions', function (Blueprint $table) {
            $table->string('assignment_type')->nullable()->after('type');
            $table->foreignId('employee_id')->nullable()->after('assignment_type')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('employee_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consumable_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('employee_id');
            $table->dropColumn('assignment_type');
        });
    }
};