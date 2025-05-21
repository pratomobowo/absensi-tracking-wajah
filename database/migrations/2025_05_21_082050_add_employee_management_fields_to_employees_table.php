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
        Schema::table('employees', function (Blueprint $table) {
            // Add foreign keys to link to new status and grade tables
            $table->foreignId('status_id')->nullable()->after('department_id')
                  ->constrained('employee_statuses')->nullOnDelete();
            $table->foreignId('grade_id')->nullable()->after('status_id')
                  ->constrained('employee_grades')->nullOnDelete();
            
            // Add additional personal info fields
            $table->string('nik')->nullable()->after('email'); // National ID number
            $table->date('birth_date')->nullable()->after('nik');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->string('birth_place')->nullable()->after('gender');
            $table->text('address')->nullable()->after('birth_place');
            
            // Add additional employment info
            $table->date('contract_start_date')->nullable()->after('joined_at');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->decimal('salary', 15, 2)->nullable()->after('contract_end_date');
            $table->string('bank_name')->nullable()->after('salary');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            
            // Emergency contact info
            $table->string('emergency_contact_name')->nullable()->after('bank_account_number');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            
            // Additional fields
            $table->text('education_background')->nullable()->after('emergency_contact_relationship');
            $table->text('skills')->nullable()->after('education_background');
            $table->text('notes')->nullable()->after('skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop emergency contact, additional fields
            $table->dropColumn([
                'notes', 'skills', 'education_background',
                'emergency_contact_relationship', 'emergency_contact_phone', 'emergency_contact_name'
            ]);
            
            // Drop employment info fields
            $table->dropColumn([
                'bank_account_number', 'bank_name', 'salary', 
                'contract_end_date', 'contract_start_date'
            ]);
            
            // Drop personal info fields
            $table->dropColumn([
                'address', 'birth_place', 'gender', 'birth_date', 'nik'
            ]);
            
            // Drop foreign keys
            $table->dropConstrainedForeignId('grade_id');
            $table->dropConstrainedForeignId('status_id');
        });
    }
};
