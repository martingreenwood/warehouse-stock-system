<?php
namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders   = Order::all();
        $products = Product::all();

        if ($orders->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No orders or products found. Please run OrderSeeder and ProductSeeder first.');
            exit;
        }

        foreach ($orders as $order) {
            $orderTotal = 0;

            $itemCount = fake()->numberBetween(1, 5);

            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = fake()->numberBetween(1, 3);

                // Use the product's base price with optional small discount
                $basePrice          = (float) $product->price;
                $discountPercentage = fake()->optional(0.3)->randomFloat(2, 0.05, 0.15); // 30% chance of 5-15% discount
                $price              = $discountPercentage ? $basePrice * (1 - $discountPercentage) : $basePrice;

                $total = $price * $quantity;
                $orderTotal += $total;

                OrderItem::create(
                    [
                        'uuid'         => Str::uuid()->toString(),
                        'order_uuid'   => $order->uuid,
                        'product_uuid' => $product->uuid,
                        'price'        => round($price, 2),
                        'quantity'     => $quantity,
                        'total'        => round($total, 2),
                    ]
                );
            }

            $order->update(['total' => round($orderTotal, 2)]);
        }

        $this->command->info('OrderItems seeded successfully with Faker-generated data and calculated totals.');
    }
}
