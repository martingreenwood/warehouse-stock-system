<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $predefinedProducts = [
            [
                'uuid'        => Str::uuid()->toString(),
                'title'       => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life.',
                'price'       => 149.99,
            ],
            [
                'uuid'        => Str::uuid()->toString(),
                'title'       => 'Smartphone Case - Clear',
                'description' => 'Transparent protective case compatible with most smartphone models.',
                'price'       => 24.99,
            ],
            [
                'uuid'        => Str::uuid()->toString(),
                'title'       => 'USB-C Charging Cable',
                'description' => 'Fast charging USB-C cable, 6 feet long with reinforced connectors.',
                'price'       => 19.99,
            ],
            [
                'uuid'        => Str::uuid()->toString(),
                'title'       => 'Portable Power Bank',
                'description' => '20,000mAh portable charger with dual USB ports and LED display.',
                'price'       => 39.99,
            ],
            [
                'uuid'        => Str::uuid()->toString(),
                'title'       => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life.',
                'price'       => 29.99,
            ],
        ];

        foreach ($predefinedProducts as $product) {
            Product::create($product);
        }

        for ($i = 0; $i < 15; $i++) {
            Product::create(
                [
                    'uuid'        => Str::uuid()->toString(),
                    'title'       => fake()->words(fake()->numberBetween(2, 4), true),
                    'description' => fake()->sentence(fake()->numberBetween(8, 15)),
                    'price'       => fake()->randomFloat(2, 9.99, 299.99),
                ]
            );
        }

        $this->command->info('Products seeded successfully with predefined and Faker-generated data.');
    }
}
