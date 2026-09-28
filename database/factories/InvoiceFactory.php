<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => Str::headline(fake()->words(3, true)),
            'date' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'status' => InvoiceStatus::UNPAID,
        ];
    }

    public function withItems(int $count = 3): static
    {
        return $this->has(
            InvoiceItem::factory()->count($count),
            'invoiceItems'
        );
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::PAID,
        ]);
    }
}
