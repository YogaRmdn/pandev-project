<?php

namespace Database\Factories;

use App\Enums\PortfolioStatus;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thumbnail' => 'https://res.cloudinary.com/demo/image/upload/sample.jpg',
            'name' => Str::headline(fake()->words(3, true)),
            'category' => 'Web App',
            'description' => fake()->paragraph(),
            'demo_link' => 'https://'.fake()->domainName(),
            'repository_link' => 'https://github.com/pandev/'.Str::slug(fake()->word()),
            'status' => PortfolioStatus::DRAFT,
            'tech_stacks' => fake()->randomElements(
                ['Next.js', 'Laravel', 'MySQL', 'TypeScript', 'Tailwind CSS'],
                3
            ),
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PortfolioStatus::PUBLISHED,
        ]);
    }
}
