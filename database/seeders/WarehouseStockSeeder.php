<?php
namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseStockSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products   = Product::all();

        if ($warehouses->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No warehouses or products found. Please run WarehouseSeeder and ProductSeeder first.');
            exit;
        }

        foreach ($warehouses as $warehouse) {
            $stockedProductCount = (int) round($products->count() * fake()->randomFloat(2, 0.6, 0.8));
            $stockedProducts     = $products->random($stockedProductCount);

            foreach ($stockedProducts as $product) {
                $price        = (float) $product->price; // Ensure price is cast to float
                $baseQuantity = $this->getBaseQuantityByPrice($price);
                $quantity     = fake()->numberBetween(
                    (int) ($baseQuantity * 0.5),
                    (int) ($baseQuantity * 2)
                );

                $threshold = (int) round($quantity * fake()->randomFloat(2, 0.1, 0.25));

                WarehouseStock::create(
                    [
                        'uuid'           => Str::uuid()->toString(),
                        'warehouse_uuid' => $warehouse->uuid,
                        'product_uuid'   => $product->uuid,
                        'quantity'       => $quantity,
                        'threshold'      => max(1, $threshold), // Ensure threshold is at least 1
                    ]
                );
            }
        }

        $this->command->info('Warehouse stock seeded successfully with Faker-generated data.');
    }

    private function getBaseQuantityByPrice(float $price): int
    {
        return match (true) {
            $price >= 200 => fake()->numberBetween(5, 20),
            $price >= 100 => fake()->numberBetween(15, 50),
            $price >= 50 => fake()->numberBetween(30, 100),
            $price >= 20 => fake()->numberBetween(50, 200),
            default => fake()->numberBetween(100, 500),
        };
    }
}
