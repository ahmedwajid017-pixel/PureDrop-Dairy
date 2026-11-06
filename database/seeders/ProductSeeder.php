<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::exists()) {
            $this->command->info('Products already exist. Skipping seeding.');

            return;
        }

        Product::factory()->count(10)->create();

        $this->command->info('10 products created successfully.');
    }
}