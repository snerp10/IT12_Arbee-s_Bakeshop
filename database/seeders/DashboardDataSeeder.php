<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Production;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\Purchase;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing products and employees
        $products = Product::where('status', 'active')->get();
        $employees = Employee::where('status', 'active')->get();

        if ($products->count() > 0 && $employees->count() > 0) {
            // Create some production records for today
            for ($i = 1; $i <= 5; $i++) {
                Production::create([
                    'product_id' => $products->random()->id,
                    'batch_number' => 'BATCH-' . Carbon::today()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'quantity_produced' => rand(10, 50),
                    'production_date' => Carbon::today(),
                    'emp_id' => $employees->random()->emp_id,
                    'notes' => 'Daily production batch #' . $i,
                    'status' => 'completed'
                ]);
            }

            // Create some sales for today
            for ($i = 1; $i <= 10; $i++) {
                Sale::create([
                    'customer_name' => 'Customer ' . $i,
                    'customer_phone' => '09' . rand(100000000, 999999999),
                    'total_amount' => rand(100, 1000),
                    'payment_method' => ['cash', 'card', 'digital'][rand(0, 2)],
                    'status' => 'completed',
                    'served_by' => $employees->random()->emp_id,
                    'created_at' => Carbon::today()->addHours(rand(8, 18))
                ]);
            }

            // Create some purchase orders with pending status
            for ($i = 1; $i <= 3; $i++) {
                Purchase::create([
                    'supplier_id' => 1, // Assuming supplier with ID 1 exists
                    'total_amount' => rand(1000, 5000),
                    'status' => 'pending',
                    'order_date' => Carbon::today(),
                    'expected_delivery' => Carbon::today()->addDays(rand(1, 7)),
                    'notes' => 'Purchase order #' . $i . ' for restocking'
                ]);
            }
        }

        $this->command->info('Dashboard sample data created successfully!');
    }
}