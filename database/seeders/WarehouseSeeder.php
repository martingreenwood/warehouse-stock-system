<?php
namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $predefinedWarehouses = [
            [
                'name'         => 'London Distribution Centre',
                'slug'         => 'london-distribution-centre',
                'latitude'     => 51.5074,
                'longitude'    => -0.1278,
                'address_1'    => '123 Industrial Estate',
                'address_2'    => 'Unit 5A',
                'town'         => 'London',
                'county'       => 'Greater London',
                'postcode'     => 'E14 9SH',
                'state_code'   => null,
                'country_code' => 'GB',
            ],
            [
                'name'         => 'Manchester Logistics Hub',
                'slug'         => 'manchester-logistics-hub',
                'latitude'     => 53.4808,
                'longitude'    => -2.2426,
                'address_1'    => '456 Warehouse Park',
                'address_2'    => null,
                'town'         => 'Manchester',
                'county'       => 'Greater Manchester',
                'postcode'     => 'M17 8AA',
                'state_code'   => null,
                'country_code' => 'GB',
            ],
            [
                'name'         => 'Birmingham Central Warehouse',
                'slug'         => 'birmingham-central-warehouse',
                'latitude'     => 52.4862,
                'longitude'    => -1.8904,
                'address_1'    => '789 Commerce Drive',
                'address_2'    => 'Building C',
                'town'         => 'Birmingham',
                'county'       => 'West Midlands',
                'postcode'     => 'B33 8TH',
                'state_code'   => null,
                'country_code' => 'GB',
            ],
        ];

        foreach ($predefinedWarehouses as $warehouse) {
            Warehouse::create(
                [
                    'uuid' => Str::uuid()->toString(),
                    ...$warehouse,
                ]
            );
        }

        $warehouseTypes = [
            'Distribution Centre',
            'Logistics Hub',
            'Fulfillment Center',
            'Storage Facility',
            'Warehouse',
            'Depot',
            'Processing Center',
        ];

        for ($i = 0; $i < 12; $i++) {
            $city          = fake()->city();
            $warehouseType = fake()->randomElement($warehouseTypes);
            $name          = "{$city} {$warehouseType}";
            $slug          = Str::slug($name);

            Warehouse::create(
                [
                    'uuid'         => Str::uuid()->toString(),
                    'name'         => $name,
                    'slug'         => $slug,
                    'latitude'     => fake()->latitude(49.5, 58.5),
                    'longitude'    => fake()->longitude(-8.0, 2.0),
                    'address_1'    => fake()->streetAddress(),
                    'address_2'    => fake()->optional(0.4)->secondaryAddress(),
                    'town'         => $city,
                    'county'       => fake()->optional(0.8)->county(),
                    'postcode'     => fake()->postcode(),
                    'state_code'   => null,
                    'country_code' => 'GB',
                ]
            );
        }

        $this->command->info('Warehouses seeded successfully with predefined and Faker-generated data.');
    }
}
