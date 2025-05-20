<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'HR Department'
            ],
            [
                'name' => 'Information Technology',
                'description' => 'IT Department'
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance Department'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing Department'
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations Department'
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
