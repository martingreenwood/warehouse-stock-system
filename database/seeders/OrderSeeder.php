<?php
namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['placed', 'dispatched', 'cancelled'];

        for ($i = 0; $i < 20; $i++) {
            Order::create(
                [
                    'uuid'       => Str::uuid()->toString(),
                    'status'     => fake()->randomElement($statuses),
                    'total'      => 0.00,
                    'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Orders seeded successfully with Faker-generated data.');
    }
}
