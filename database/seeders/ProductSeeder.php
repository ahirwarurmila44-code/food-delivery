<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Restaurant;
use App\Models\Admin\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            Product::factory()
                ->count(12)
                ->create([
                    'restaurant_id' => $restaurant->id,
                ]);
        }
    }
}
