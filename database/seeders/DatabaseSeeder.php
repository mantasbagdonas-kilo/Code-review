<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);

        Product::create(['name' => 'Mechanical Keyboard', 'price' => 89.99, 'stock' => 25, 'active' => true]);
        Product::create(['name' => 'USB-C Cable', 'price' => 9.50, 'stock' => 200, 'active' => true]);
        Product::create(['name' => '27" Monitor', 'price' => 219.00, 'stock' => 8, 'active' => true]);
        Product::create(['name' => 'Discontinued Mouse', 'price' => 14.00, 'stock' => 5, 'active' => false]);
    }
}
