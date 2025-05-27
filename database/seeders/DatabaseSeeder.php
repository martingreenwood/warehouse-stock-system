<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

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
