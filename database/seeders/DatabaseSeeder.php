<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the BakeshopDemoSeeder instead of default factory
        $this->call([
            \Database\Seeders\BakeshopDemoSeeder::class,
        ]);
    }
}
