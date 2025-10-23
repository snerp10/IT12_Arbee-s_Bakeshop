<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class TestNewSchemaSeeder extends Seeder
{
    public function run(): void
    {
        // Create employees
        $admin = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Admin',
            'email' => 'admin@seabakery.com',
            'phone' => '1234567890',
            'address' => '123 Admin St',
            'hire_date' => '2025-01-01',
            'position' => 'admin',
            'status' => 'active',
            'salary' => 5000.00,
        ]);

        $baker = Employee::create([
            'first_name' => 'Jane',
            'last_name' => 'Baker',
            'email' => 'baker@seabakery.com',
            'phone' => '1234567891',
            'address' => '123 Baker St',
            'hire_date' => '2025-01-01',
            'position' => 'baker',
            'status' => 'active',
            'salary' => 3000.00,
        ]);

        $cashier = Employee::create([
            'first_name' => 'Bob',
            'last_name' => 'Cashier',
            'email' => 'cashier@seabakery.com',
            'phone' => '1234567892',
            'address' => '123 Cashier St',
            'hire_date' => '2025-01-01',
            'position' => 'cashier',
            'status' => 'active',
            'salary' => 2500.00,
        ]);

        // Create users
        User::create([
            'emp_id' => $admin->emp_id,
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'emp_id' => $baker->emp_id,
            'username' => 'baker',
            'password' => Hash::make('password'),
            'role' => 'baker',
            'status' => 'active',
        ]);

        User::create([
            'emp_id' => $cashier->emp_id,
            'username' => 'cashier',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'status' => 'active',
        ]);

        // Create product categories
        $bread = Category::create([
            'name' => 'Bread',
            'description' => 'Fresh baked bread products',
            'status' => 'active',
        ]);

        $pastry = Category::create([
            'name' => 'Pastry',
            'description' => 'Sweet pastries and desserts',
            'status' => 'active',
        ]);

        // Create products
        Product::create([
            'category_id' => $bread->category_id,
            'name' => 'White Bread',
            'description' => 'Fresh white bread loaf',
            'price' => 3.50,
            'unit' => 'piece',
            'ingredients' => 'Flour, water, yeast, salt',
            'preparation_time' => 180,
            'status' => 'active',
        ]);

        Product::create([
            'category_id' => $pastry->category_id,
            'name' => 'Chocolate Croissant',
            'description' => 'Buttery croissant with chocolate filling',
            'price' => 2.75,
            'unit' => 'piece',
            'ingredients' => 'Flour, butter, chocolate, eggs',
            'preparation_time' => 120,
            'status' => 'active',
        ]);
    }
}
