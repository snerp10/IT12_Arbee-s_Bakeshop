<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample employees first
        $employees = [
            [
                'first_name' => 'John',
                'middle_name' => 'A.',
                'last_name' => 'Doe',
                'phone' => '09123456789',
                'address' => '123 Main Street, City',
                'status' => 'active',
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
            ],
            [
                'first_name' => 'Jane',
                'middle_name' => null,
                'last_name' => 'Smith',
                'phone' => '09987654321',
                'address' => '456 Oak Avenue, City',
                'status' => 'active',
                'shift_start' => '06:00:00',
                'shift_end' => '14:00:00',
            ],
            [
                'first_name' => 'Mike',
                'middle_name' => 'B.',
                'last_name' => 'Johnson',
                'phone' => '09555123456',
                'address' => '789 Pine Road, City',
                'status' => 'active',
                'shift_start' => '14:00:00',
                'shift_end' => '22:00:00',
            ],
            [
                'first_name' => 'Sarah',
                'middle_name' => null,
                'last_name' => 'Williams',
                'phone' => '09777888999',
                'address' => '321 Elm Street, City',
                'status' => 'active',
                'shift_start' => '09:00:00',
                'shift_end' => '18:00:00',
            ],
            [
                'first_name' => 'David',
                'middle_name' => 'C.',
                'last_name' => 'Brown',
                'phone' => '09444555666',
                'address' => '654 Maple Lane, City',
                'status' => 'active',
                'shift_start' => '05:00:00',
                'shift_end' => '13:00:00',
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        // Get available employee IDs (that don't have users yet)
        $availableEmployeeIds = Employee::whereDoesntHave('user')->pluck('emp_id')->take(3);
        
        if ($availableEmployeeIds->count() < 3) {
            $this->command->info('Not enough available employees to create sample users.');
            return;
        }

        // Create sample users with different roles
        $users = [
            [
                'emp_id' => $availableEmployeeIds[0],
                'username' => 'manager_jane',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'status' => 'active',
            ],
            [
                'emp_id' => $availableEmployeeIds[1],
                'username' => 'baker_mike',
                'password' => Hash::make('password123'),
                'role' => 'baker',
                'status' => 'active',
            ],
            [
                'emp_id' => $availableEmployeeIds[2],
                'username' => 'cashier_sarah',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('Sample employees and users created successfully!');
        $this->command->info('Available for user creation:');
        $this->command->info('- John Doe (Employee ID: 1)');
        $this->command->info('- David Brown (Employee ID: 5)');
        $this->command->info('');
        $this->command->info('Created users:');
        $this->command->info('- manager_jane (Manager)');
        $this->command->info('- baker_mike (Baker)');
        $this->command->info('- cashier_sarah (Cashier)');
    }
}