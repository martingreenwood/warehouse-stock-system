<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(): View
    {
        $products = Product::select('uuid', 'title', 'price')
            ->whereHas('warehouseStock', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('title')
            ->get();

        return view('orders.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'product_uuid' => 'required|exists:products,uuid',
                'quantity'     => 'required|integer|min:1',
            ], [
                'product_uuid.required' => 'Please select a product.',
                'product_uuid.exists'   => 'Selected product does not exist.',
                'quantity.required'     => 'Quantity is required.',
                'quantity.min'          => 'Quantity must be at least 1.',
            ]
        );

        try {
            $product  = Product::where('uuid', $validated['product_uuid'])->firstOrFail();
            $quantity = (int) $validated['quantity'];

            $singleWarehouse = $this->findBestWarehouse($product, $quantity);

            $orderTotal = $product->price * $quantity;

            $order = Order::create(
                [
                    'uuid'   => Str::uuid()->toString(),
                    'status' => 'placed',
                    'total'  => $orderTotal,
                ]
            );

            if ($singleWarehouse) {
                $this->fulfillFromSingleWarehouse($order, $product, $quantity, $singleWarehouse);

                return redirect()->route('products.index')
                    ->with('success', "Order #{$order->uuid} created successfully! Total: £" . number_format($orderTotal, 2) . " (Fulfilled from {$singleWarehouse->warehouse->name})");
            } else {
                $fulfillmentResult = $this->fulfillFromMultipleWarehouses($order, $product, $quantity);

                if (! $fulfillmentResult['success']) {
                    $order->delete();

                    return redirect()->back()
                        ->withInput()
                        ->withErrors(
                            [
                                'stock_error' => $fulfillmentResult['error'],
                            ]
                        );
                }

                return redirect()->route('products.index')
                    ->with('success', "Order #{$order->uuid} created successfully! Total: £" . number_format($orderTotal, 2) . " (Split across {$fulfillmentResult['warehouse_count']} warehouses: {$fulfillmentResult['warehouse_names']})");
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create order. Please try again.']);
        }
    }

    private function findBestWarehouse(Product $product, int $quantity)
    {
        return $product->warehouseStock()
            ->with('warehouse')
            ->where('quantity', '>=', $quantity)
            ->orderBy('quantity', 'desc')
            ->first();
    }

    private function fulfillFromSingleWarehouse(Order $order, Product $product, int $quantity, $warehouse): void
    {
        OrderItem::create(
            [
                'uuid'         => Str::uuid()->toString(),
                'order_uuid'   => $order->uuid,
                'product_uuid' => $product->uuid,
                'price'        => $product->price,
                'quantity'     => $quantity,
                'total'        => $product->price * $quantity,
            ]
        );

        $warehouse->decrement('quantity', $quantity);
    }

    private function fulfillFromMultipleWarehouses(Order $order, Product $product, int $totalQuantity): array
    {
        $warehouses = $product->warehouseStock()
            ->with('warehouse')
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->get();

        if ($warehouses->isEmpty()) {
            return [
                'success' => false,
                'error'   => 'No stock available for this product in any warehouse.',
            ];
        }

        $totalAvailableStock = $warehouses->sum('quantity');
        if ($totalAvailableStock < $totalQuantity) {
            return [
                'success' => false,
                'error'   => "Insufficient total stock. Required: {$totalQuantity}, Available: {$totalAvailableStock}",
            ];
        }

        $remainingQuantity = $totalQuantity;
        $warehouseNames    = [];
        $warehouseCount    = 0;

        foreach ($warehouses as $warehouse) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $quantityFromThisWarehouse = min($warehouse->quantity, $remainingQuantity);

            OrderItem::create(
                [
                    'uuid'         => Str::uuid()->toString(),
                    'order_uuid'   => $order->uuid,
                    'product_uuid' => $product->uuid,
                    'price'        => $product->price,
                    'quantity'     => $quantityFromThisWarehouse,
                    'total'        => $product->price * $quantityFromThisWarehouse,
                ]
            );

            $warehouse->decrement('quantity', $quantityFromThisWarehouse);

            $remainingQuantity -= $quantityFromThisWarehouse;
            $warehouseNames[] = $warehouse->warehouse->name;
            $warehouseCount++;
        }

        return [
            'success'         => true,
            'warehouse_count' => $warehouseCount,
            'warehouse_names' => implode(', ', $warehouseNames),
        ];
    }
}
