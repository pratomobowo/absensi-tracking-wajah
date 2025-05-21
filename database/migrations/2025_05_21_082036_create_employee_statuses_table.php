<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->default('#3b82f6'); // Default blue color for UI
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Seed with default statuses
        DB::table('employee_statuses')->insert([
            ['name' => 'Permanent', 'description' => 'Permanent employee', 'color' => '#10b981', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contract', 'description' => 'Contract employee', 'color' => '#f59e0b', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Probation', 'description' => 'Employee under probation period', 'color' => '#6366f1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intern', 'description' => 'Internship', 'color' => '#8b5cf6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Part-time', 'description' => 'Part-time employee', 'color' => '#ec4899', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_statuses');
    }
};
