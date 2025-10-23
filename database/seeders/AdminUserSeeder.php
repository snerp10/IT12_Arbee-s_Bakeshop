<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin employee record
        $adminEmployee = Employee::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => null,
            'address' => null,
            'hire_date' => now()->toDateString(),
            'position' => 'admin',
            'status' => 'active',
        ]);

        // Create admin user
        User::create([
            'emp_id' => $adminEmployee->emp_id,
            'username' => 'admin',
            'email' => 'admin@arbees.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        echo "Admin user created:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    }
}
