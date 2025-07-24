<?php

namespace Database\Factories\Admin;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    
    public function definition(): array
    {
         // Pick a random image from sample_images folder
        // $sampleImages = Storage::files('public/sample_images');
        // $randomImage = fake()->randomElement($sampleImages);
        // $newImageName = 'products/' . uniqid() . '.jpg';

        // Copy image to products/ folder
        // Storage::copy($randomImage, 'public/' . $newImageName);

        return [
            'name' => fake()->words(2, true),
             'description' => fake()->sentence(),
            'category_id' => \App\Models\Admin\Category::inRandomOrder()->first()->id,
            'restaurant_id' => \App\Models\Admin\Restaurant::inRandomOrder()->first()->id,
            'price' => fake()->randomFloat(2, 100, 500),
            'available' => true,
            'image' => '', 
        ];
    }
}
