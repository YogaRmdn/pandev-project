<?php

namespace Database\Factories;

use App\Models\Ebook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ebook>
 */
class EbookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cover' => '/assets/ebooks/laravel-12-zero-to-production.jpg',
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'url' => 'https://lynk.id/pandev/'.fake()->slug(2),
        ];
    }
}
