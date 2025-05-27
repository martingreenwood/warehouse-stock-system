<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['warehouseStock.warehouse']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock_status')) {
            $stockStatus = $request->get('stock_status');

            switch ($stockStatus) {
                case 'low_stock':
                    $query->whereHas('warehouseStock', function ($q) {
                        $q->selectRaw('product_uuid, SUM(quantity) as total_quantity, SUM(threshold) as total_threshold')
                            ->groupBy('product_uuid')
                            ->havingRaw('total_quantity <= total_threshold');
                    });
                    break;

                case 'out_of_stock':
                    $query->whereDoesntHave('warehouseStock')
                        ->orWhereHas('warehouseStock', function ($q) {
                            $q->selectRaw('product_uuid, SUM(quantity) as total_quantity')
                                ->groupBy('product_uuid')
                                ->havingRaw('total_quantity = 0');
                        });
                    break;

                case 'in_stock':
                    $query->whereHas('warehouseStock', function ($q) {
                        $q->selectRaw('product_uuid, SUM(quantity) as total_quantity, SUM(threshold) as total_threshold')
                            ->groupBy('product_uuid')
                            ->havingRaw('total_quantity > total_threshold');
                    });
                    break;
            }
        }

        $sortBy        = $request->get('sort_by', 'title');
        $sortDirection = $request->get('sort_direction', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortDirection);
                break;
            default:
                $query->orderBy('title', $sortDirection);
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        $products->getCollection()->transform(
            function ($product) {
                $product->total_stock = $product->warehouseStock->sum('quantity');

                $product->total_threshold     = $product->totalThreshold();
                $product->allocated_to_orders = $product->allocatedToOrders();
                $product->physical_quantity   = $product->physicalQuantity();
                $product->immediate_despatch  = $product->immediateDespatch();

                $product->available_for_sale  = max(0, $product->total_stock - $product->allocated_to_orders);
                $product->stock_status        = $this->getStockStatus($product);
                $product->stock_status_label  = $this->getStockStatusLabel($product->stock_status);
                $product->warehouse_count     = $product->warehouseStock->count();
                $product->warehouse_breakdown = $product->getWarehouseStockBreakdown();

                $product->formatted_price = '£' . number_format((float) $product->price, 2);

                return $product;
            }
        );

        return view('products.index', compact('products'));
    }

    private function getStockStatus(Product $product): string
    {
        if ($product->total_stock <= 0) {
            return 'out_of_stock';
        }

        if ($product->total_stock <= $product->total_threshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    private function getStockStatusLabel(string $status): string
    {
        return match ($status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
            default => 'Unknown',
        };
    }

    private function formatWarehouseLocation($warehouse): string
    {
        $location = [];

        if ($warehouse->town) {
            $location[] = $warehouse->town;
        }

        if ($warehouse->county) {
            $location[] = $warehouse->county;
        }

        if ($warehouse->postcode) {
            $location[] = $warehouse->postcode;
        }

        return implode(', ', $location) ?: 'Location not specified';
    }
}
