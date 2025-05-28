# Warehouse Stock Management System

A comprehensive Laravel-based warehouse stock management system that enables multi-warehouse inventory tracking, order fulfillment, and stock allocation across multiple locations.

## Features

### Core Functionality

-   **Multi-Warehouse Management**: Track inventory across multiple warehouse locations
-   **Product Management**: Comprehensive product catalog with pricing and descriptions
-   **Stock Tracking**: Real-time inventory levels with threshold monitoring
-   **Order Management**: Create and track orders with intelligent warehouse allocation
-   **Stock Allocation**: Automatic allocation of stock to orders with multi-warehouse fulfillment
-   **Inventory Analytics**: Detailed stock breakdowns, physical quantities, and availability metrics

### Key Capabilities

-   **Intelligent Order Fulfillment**: Automatically finds the best warehouse to fulfill orders, with multi-warehouse splitting when necessary
-   **Stock Status Monitoring**: Real-time status tracking (good, low, empty) with threshold-based alerts
-   **Geographic Warehouse Data**: Location tracking with coordinates for logistics optimization
-   **Comprehensive Reporting**: Detailed warehouse breakdowns and stock analytics
-   **Responsive Design**: Modern UI built with Tailwind CSS

## Architecture

### Database Schema

The system uses a robust relational database structure:

-   **Products**: Core product information (title, description, price)
-   **Warehouses**: Physical locations with geographic coordinates
-   **Warehouse Stock**: Junction table tracking quantity and thresholds per warehouse/product
-   **Orders**: Order management with status tracking
-   **Order Items**: Individual line items linking products to orders

### Key Models & Relationships

-   `Product` → `WarehouseStock` (One-to-Many)
-   `Warehouse` → `WarehouseStock` (One-to-Many)
-   `Order` → `OrderItem` (One-to-Many)
-   `Product` → `OrderItem` (One-to-Many)

## Technology Stack

### Backend

-   **Laravel 12**: Modern PHP framework with strict typing
-   **PHP 8.2+**: Latest PHP features and performance improvements
-   **SQLite**: Lightweight database for development (easily configurable for MySQL/PostgreSQL)

### Frontend

-   **Tailwind CSS 4.0**: Utility-first CSS framework
-   **Vite**: Fast build tool and development server
-   **Blade Templates**: Laravel's templating engine

### Development Tools

-   **Laravel Pint**: Code style fixer
-   **PHPUnit**: Testing framework
-   **Faker**: Test data generation
-   **Laravel Sail**: Docker development environment

## Prerequisites

Before setting up the project, ensure you have:

-   **PHP 8.2 or higher**
-   **Composer** (PHP dependency manager)
-   **Node.js 18+ and npm** (for frontend assets)
-   **SQLite** (or MySQL/PostgreSQL if preferred)

## Local Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd warehouse-stock-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

The project is configured to use SQLite by default. The database file will be created automatically:

```bash
# Create the SQLite database file (if it doesn't exist)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 7. Start the Development Server

#### Option A: Using the Built-in Development Command

```bash
composer run dev
```

This command starts multiple services concurrently:

-   Laravel development server (http://localhost:8000)
-   Queue worker
-   Log monitoring (Laravel Pail)
-   Vite development server

#### Option B: Manual Setup

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite for asset compilation
npm run dev

# Terminal 3 (optional): Start queue worker
php artisan queue:work

# Terminal 4 (optional): Monitor logs
php artisan pail
```

### 8. Access the Application

-   **Main Application**: http://localhost:8000
-   **Products Page**: http://localhost:8000/products
-   **Create Order**: http://localhost:8000/orders/create

## Sample Data

The application includes comprehensive seeders that generate realistic test data:

-   **Products**: Various products with different price ranges
-   **Warehouses**: Multiple warehouse locations with geographic data
-   **Stock Levels**: Realistic inventory distributions across warehouses
-   **Orders**: Sample orders with different statuses
-   **Thresholds**: Configurable stock thresholds for reorder alerts

## Usage Examples

### Viewing Product Inventory

Navigate to `/products` to see:

-   Total stock across all warehouses
-   Stock allocated to pending orders
-   Available physical quantity
-   Immediate dispatch availability
-   Per-warehouse breakdown with status indicators

### Creating Orders

1. Go to `/orders/create`
2. Select a product and quantity
3. The system automatically:
    - Finds the best warehouse for fulfillment
    - Splits orders across warehouses if needed
    - Updates stock allocations
    - Creates order records

### Stock Management Features

-   **Real-time Calculations**: Physical quantity = Total Stock - Allocated - Threshold
-   **Multi-warehouse Fulfillment**: Orders can be split across multiple locations
-   **Status Monitoring**: Visual indicators for stock levels (good/low/empty)
-   **Geographic Data**: Warehouse locations with coordinates for logistics

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test files
php artisan test tests/Feature/ProductTest.php
```

## Configuration

### Database Configuration

To use MySQL or PostgreSQL instead of SQLite, update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_stock
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Queue Configuration

For production, configure a proper queue driver:

```env
QUEUE_CONNECTION=redis
# or
QUEUE_CONNECTION=database
```

## Project Structure

```
warehouse-stock-system/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   └── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Data seeders
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript assets
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Web routes
└── tests/                  # Test files
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Troubleshooting

### Common Issues

**Database Connection Error**

-   Ensure the SQLite file exists: `touch database/database.sqlite`
-   Check file permissions
-   Verify `.env` database configuration

**Asset Compilation Issues**

-   Clear npm cache: `npm cache clean --force`
-   Delete node_modules and reinstall: `rm -rf node_modules && npm install`
-   Ensure Node.js version is 18+

**Permission Errors**

-   Set proper permissions: `chmod -R 755 storage bootstrap/cache`
-   Ensure web server can write to storage directories

**Queue Jobs Not Processing**

-   Start the queue worker: `php artisan queue:work`
-   Check queue configuration in `.env`

### Getting Help

-   Check the Laravel documentation: https://laravel.com/docs
-   Review application logs: `tail -f storage/logs/laravel.log`
-   Use Laravel Pail for real-time log monitoring: `php artisan pail`
