<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Employee;
use App\Models\User;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BakeshopDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo data for Bakeshop POS...');

        // Create Categories
        $categories = [
            ['name' => 'Breads', 'description' => 'Fresh baked breads'],
            ['name' => 'Pastries', 'description' => 'Sweet pastries and desserts'],
            ['name' => 'Cakes', 'description' => 'Birthday and celebration cakes'],
            ['name' => 'Beverages', 'description' => 'Coffee, tea, and cold drinks'],
            ['name' => 'Savory', 'description' => 'Sandwiches and savory items']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products
        $products = [
            ['name' => 'Pandesal', 'sku' => 'BRD001', 'category_id' => 1, 'price' => 3.00, 'unit' => 'pieces', 'shelf_life' => 2, 'description' => 'Fresh baked Filipino bread roll'],
            ['name' => 'Ensaymada', 'sku' => 'PST001', 'category_id' => 2, 'price' => 25.00, 'unit' => 'pieces', 'shelf_life' => 3, 'description' => 'Sweet brioche with cheese and sugar'],
            ['name' => 'Chocolate Cake Slice', 'sku' => 'CKE001', 'category_id' => 3, 'price' => 85.00, 'unit' => 'pieces', 'shelf_life' => 5, 'description' => 'Rich chocolate cake slice'],
            ['name' => 'Iced Coffee', 'sku' => 'BEV001', 'category_id' => 4, 'price' => 65.00, 'unit' => 'cups', 'shelf_life' => 1, 'description' => 'Cold brew iced coffee'],
            ['name' => 'Tuna Sandwich', 'sku' => 'SAV001', 'category_id' => 5, 'price' => 45.00, 'unit' => 'pieces', 'shelf_life' => 1, 'description' => 'Fresh tuna sandwich'],
            ['name' => 'Croissant', 'sku' => 'PST002', 'category_id' => 2, 'price' => 35.00, 'unit' => 'pieces', 'shelf_life' => 2, 'description' => 'Buttery flaky croissant'],
            ['name' => 'Americano', 'sku' => 'BEV002', 'category_id' => 4, 'price' => 55.00, 'unit' => 'cups', 'shelf_life' => 1, 'description' => 'Strong black coffee']
        ];

        foreach ($products as $product) {
            $prod = Product::create($product);
            
            // Create inventory stock snapshot
            $quantity = rand(10, 50);
            InventoryStock::create([
                'prod_id' => $prod->prod_id,
                'quantity' => $quantity,
                'reorder_level' => rand(5, 15)
            ]);

            // Create initial inventory movement
            InventoryMovement::create([
                'prod_id' => $prod->prod_id,
                'transaction_type' => 'stock_in',
                'quantity' => $quantity,
                'previous_stock' => 0,
                'current_stock' => $quantity,
                'notes' => 'Initial stock'
            ]);
        }

        // Create Employees
        $employees = [
            [
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'phone' => '09171234567',
                'address' => '123 Main St, Manila',
                'position' => 'admin',
                'hire_date' => '2023-01-15',
                'status' => 'active'
            ],
            [
                'first_name' => 'Juan',
                'middle_name' => 'Cruz',
                'last_name' => 'Reyes',
                'phone' => '09181234567',
                'address' => '456 Oak Ave, Quezon City',
                'position' => 'baker',
                'hire_date' => '2023-03-20',
                'status' => 'active'
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Ramos',
                'last_name' => 'Gonzales',
                'phone' => '09191234567',
                'address' => '789 Pine Rd, Makati',
                'position' => 'cashier',
                'hire_date' => '2023-06-10',
                'status' => 'active'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        // Create Users
        $adminEmployee = Employee::where('position', 'admin')->first();
        $bakerEmployee = Employee::where('position', 'baker')->first();
        $cashierEmployee = Employee::where('position', 'cashier')->first();

        User::create([
            'emp_id' => $adminEmployee->emp_id,
            'username' => 'admin',
            'email' => 'admin@seabakery.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'emp_id' => $bakerEmployee->emp_id,
            'username' => 'baker',
            'email' => 'baker@seabakery.com',
            'password' => Hash::make('baker123'),
            'role' => 'baker'
        ]);

        User::create([
            'emp_id' => $cashierEmployee->emp_id,
            'username' => 'cashier',
            'email' => 'cashier@seabakery.com',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier'
        ]);

        // Create Production Batches
        $productIds = Product::pluck('prod_id')->toArray();
        $bakerIds = Employee::where('position', 'baker')->pluck('emp_id')->toArray();

        for ($i = 1; $i <= 5; $i++) {
            $prodId = $productIds[array_rand($productIds)];
            $product = Product::find($prodId);
            $quantity = rand(20, 50);
            $productionDate = Carbon::today()->subDays(rand(0, 2));
            
            Production::create([
                'prod_id' => $prodId,
                'batch_number' => 'BATCH-' . $productionDate->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'quantity_produced' => $quantity,
                'production_date' => $productionDate,
                'expiration_date' => $productionDate->copy()->addDays($product->shelf_life ?? 2),
                'baker_id' => $bakerIds[array_rand($bakerIds)],
                'notes' => 'Production batch #' . $i,
                'status' => 'completed'
            ]);

            // Update inventory
            $stock = InventoryStock::where('prod_id', $prodId)->first();
            $prevStock = $stock->quantity;
            $stock->quantity += $quantity;
            $stock->save();

            InventoryMovement::create([
                'prod_id' => $prodId,
                'transaction_type' => 'stock_in',
                'quantity' => $quantity,
                'previous_stock' => $prevStock,
                'current_stock' => $stock->quantity,
                'notes' => 'Production batch #' . $i
            ]);
        }

        // Create Sales Orders
        $cashierIds = Employee::where('position', 'cashier')->pluck('emp_id')->toArray();

        for ($i = 1; $i <= 10; $i++) {
            $saleDate = Carbon::today()->subDays(rand(0, 3));
            $sale = Sale::create([
                'order_number' => 'SO-' . $saleDate->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'order_date' => $saleDate,
                'order_type' => ['dine_in', 'takeout'][rand(0, 1)],
                'cashier_id' => $cashierIds[array_rand($cashierIds)],
                'subtotal' => 0,
                'total_amount' => 0,
                'status' => 'completed'
            ]);

            // Add sale items
            $numItems = rand(1, 4);
            $total = 0;
            for ($j = 0; $j < $numItems; $j++) {
                $prodId = $productIds[array_rand($productIds)];
                $product = Product::find($prodId);
                $quantity = rand(1, 3);
                $itemTotal = $product->price * $quantity;
                
                SaleItem::create([
                    'so_id' => $sale->so_id,
                    'prod_id' => $prodId,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal
                ]);

                $total += $itemTotal;

                // Update inventory
                $stock = InventoryStock::where('prod_id', $prodId)->first();
                if ($stock && $stock->quantity >= $quantity) {
                    $prevStock = $stock->quantity;
                    $stock->quantity -= $quantity;
                    $stock->save();

                    InventoryMovement::create([
                        'prod_id' => $prodId,
                        'transaction_type' => 'stock_out',
                        'quantity' => $quantity,
                        'previous_stock' => $prevStock,
                        'current_stock' => $stock->quantity,
                        'notes' => 'Sale order ' . $sale->order_number
                    ]);
                }
            }

            // Update sale total
            $sale->subtotal = $total;
            $sale->total_amount = $total;
            $sale->save();
        }

        $this->command->info('✅ Bakeshop demo data created successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - ' . Category::count() . ' categories');
        $this->command->info('   - ' . Product::count() . ' products');
        $this->command->info('   - ' . Employee::count() . ' employees');
        $this->command->info('   - ' . User::count() . ' users');
        $this->command->info('   - ' . Production::count() . ' production batches');
        $this->command->info('   - ' . Sale::count() . ' sales orders');
        $this->command->info('   - ' . InventoryMovement::count() . ' inventory movements');
        $this->command->info('');
        $this->command->info('🔑 Login Credentials:');
        $this->command->info('   Admin:   admin / admin123');
        $this->command->info('   Baker:   baker / baker123');
        $this->command->info('   Cashier: cashier / cashier123');
    }
}