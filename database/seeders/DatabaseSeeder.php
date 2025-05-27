<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create(
            [
                'name'  => 'Test User',
                'email' => 'test@example.com',
            ]
        );

        // Seed the warehouse system data in correct order
        $this->call(
            [
                ProductSeeder::class,
                WarehouseSeeder::class,
                WarehouseStockSeeder::class,
                OrderSeeder::class,
                OrderItemSeeder::class,
            ]
        );

        $this->command->info('Database seeded successfully with Faker-generated data.');
    }
}
