<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Warehouse Stock System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { margin-bottom: 30px; }
        .search-filters { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .search-filters form { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .search-filters input, .search-filters select { padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .search-filters button { background: #007cba; color: white; padding: 8px 15px; border: none; border-radius: 3px; cursor: pointer; }
        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .products-table th, .products-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .products-table th { background: #f8f9fa; font-weight: bold; }
        .stock-status { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .stock-status.in_stock { background: #d4edda; color: #155724; }
        .stock-status.low_stock { background: #fff3cd; color: #856404; }
        .stock-status.out_of_stock { background: #f8d7da; color: #721c24; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a { padding: 8px 12px; margin: 0 2px; text-decoration: none; border: 1px solid #ddd; }
        .pagination .current { background: #007cba; color: white; }
        .metric-highlight { font-weight: bold; color: #007cba; }
        .warehouse-count { font-size: 12px; color: #666; }
        .breakdown-toggle { background: none; border: 1px solid #007cba; color: #007cba; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px; }
        .breakdown-toggle:hover { background: #007cba; color: white; }
        .warehouse-breakdown { margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 3px; }
        .warehouse-item { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid #dee2e6; }
        .warehouse-item:last-child { border-bottom: none; }
        .warehouse-info { flex: 1; }
        .warehouse-name { font-weight: bold; color: #333; }
        .warehouse-location { font-size: 11px; color: #666; }
        .warehouse-stock { text-align: right; }
        .warehouse-stock-status { padding: 2px 6px; border-radius: 2px; font-size: 10px; font-weight: bold; }
        .warehouse-stock-status.good { background: #d4edda; color: #155724; }
        .warehouse-stock-status.low { background: #fff3cd; color: #856404; }
        .warehouse-stock-status.empty { background: #f8d7da; color: #721c24; }
        .warehouse-breakdown-inline { margin-bottom: 8px; }
        .warehouse-item-inline { margin-bottom: 4px; font-size: 12px; line-height: 1.4; }
        .warehouse-item-inline strong { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Products Inventory</h1>
            <p>Manage and view product stock levels across all warehouses</p>
        </div>

        <div class="search-filters">
            <form method="GET" action="{{ route('products.index') }}">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}">

                <select name="stock_status">
                    <option value="">All Stock Levels</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <select name="sort_by">
                    <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Sort by Title</option>
                    <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Sort by Price</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                </select>

                <select name="sort_direction">
                    <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit">Filter</button>
                <a href="{{ route('products.index') }}" style="margin-left: 10px;">Clear</a>
            </form>
        </div>

        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Total Quantity</th>
                    <th>Allocated to Orders</th>
                    <th>Physical Quantity</th>
                    <th>Total Threshold</th>
                    <th>Immediate Despatch</th>
                    <th>Status</th>
                    <th>Warehouse Breakdown</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->title }}</strong>
                            @if($product->description)
                                <br><small style="color: #666;">{{ Str::limit($product->description, 60) }}</small>
                            @endif
                        </td>
                        <td>{{ $product->formatted_price }}</td>
                        <td class="metric-highlight">{{ number_format($product->total_stock) }}</td>
                        <td class="metric-highlight">{{ number_format($product->allocated_to_orders) }}</td>
                        <td class="metric-highlight">{{ number_format($product->physical_quantity) }}</td>
                        <td class="metric-highlight">{{ number_format($product->total_threshold) }}</td>
                        <td class="metric-highlight">{{ number_format($product->immediate_despatch) }}</td>
                        <td>
                            <span class="stock-status {{ $product->stock_status }}">
                                {{ $product->stock_status_label }}
                            </span>
                        </td>
                        <td>
                            @if(count($product->warehouse_breakdown) > 0)
                                <div class="warehouse-breakdown-inline">
                                    @foreach($product->warehouse_breakdown as $warehouse)
                                        <div class="warehouse-item-inline">
                                            <strong>{{ $warehouse['warehouse_name'] }}:</strong>
                                            {{ number_format($warehouse['quantity']) }} units
                                            <span class="warehouse-stock-status {{ $warehouse['status'] }}">
                                                ({{ ucfirst($warehouse['status']) }})
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="breakdown-toggle" onclick="toggleBreakdown({{ $product->id }})">
                                    View Details
                                </button>
                            @else
                                <span style="color: #999; font-style: italic;">No stock</span>
                            @endif
                        </td>
                    </tr>

                    @if(count($product->warehouse_breakdown) > 0)
                        <tr id="breakdown-{{ $product->id }}" class="warehouse-breakdown-row" style="display: none;">
                            <td colspan="9">
                                <div class="warehouse-breakdown">
                                    <h4 style="margin: 0 0 15px 0; color: #333;">
                                        Detailed Warehouse Stock Breakdown for "{{ $product->title }}"
                                    </h4>

                                    <div style="background: #e9ecef; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                                        <strong>Summary:</strong>
                                        Total Quantity: {{ number_format($product->total_stock) }} |
                                        Allocated to Orders: {{ number_format($product->allocated_to_orders) }} |
                                        Physical Quantity: {{ number_format($product->physical_quantity) }} |
                                        Immediate Despatch: {{ number_format($product->immediate_despatch) }}
                                    </div>

                                    @foreach($product->warehouse_breakdown as $warehouse)
                                        <div class="warehouse-item">
                                            <div class="warehouse-info">
                                                <div class="warehouse-name">{{ $warehouse['warehouse_name'] }}</div>
                                                <div class="warehouse-location">📍 {{ $warehouse['location'] }}</div>
                                            </div>
                                            <div class="warehouse-stock">
                                                <div>
                                                    <strong>{{ number_format($warehouse['quantity']) }}</strong> units
                                                    <span class="warehouse-stock-status {{ $warehouse['status'] }}">
                                                        {{ ucfirst($warehouse['status']) }}
                                                    </span>
                                                </div>
                                                <div style="font-size: 11px; color: #666; margin-top: 2px;">
                                                    Threshold: {{ number_format($warehouse['threshold']) }} |
                                                    Available: {{ number_format($warehouse['available']) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                            No products found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    </div>

    <script>
        function toggleBreakdown(productId) {
            console.log('Toggling breakdown for product ID:', productId);
            const breakdownRow = document.getElementById('breakdown-' + productId);
            const button = event.target;

            console.log('Breakdown row element:', breakdownRow);

            if (!breakdownRow) {
                console.error('Breakdown row not found for product ID:', productId);
                return;
            }

            if (breakdownRow.style.display === 'none' || breakdownRow.style.display === '') {
                breakdownRow.style.display = 'table-row';
                button.textContent = 'Hide Details';
                console.log('Showing breakdown');
            } else {
                breakdownRow.style.display = 'none';
                button.textContent = 'View Details';
                console.log('Hiding breakdown');
            }
        }
    </script>
</body>
</html>
