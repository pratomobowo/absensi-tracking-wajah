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
        Schema::create('employee_grades', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->integer('level')->default(1); // Numeric level for hierarchy (higher = senior)
            $table->decimal('salary_min', 15, 2)->nullable(); // Minimum salary for this grade
            $table->decimal('salary_max', 15, 2)->nullable(); // Maximum salary for this grade
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Seed with default grades
        DB::table('employee_grades')->insert([
            [
                'name' => 'Entry Level', 
                'code' => 'E1', 
                'description' => 'Entry level staff', 
                'level' => 1,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Junior', 
                'code' => 'J1', 
                'description' => 'Junior staff with 1-2 years experience', 
                'level' => 2,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Intermediate', 
                'code' => 'I1', 
                'description' => 'Intermediate staff with 3-5 years experience', 
                'level' => 3,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Senior', 
                'code' => 'S1', 
                'description' => 'Senior staff with 5+ years experience', 
                'level' => 4,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Manager', 
                'code' => 'M1', 
                'description' => 'Managerial position', 
                'level' => 5,
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_grades');
    }
};
