<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => Str::headline(fake()->words(2, true)),
            'quantity' => fake()->numberBetween(1, 10),
            'price' => fake()->randomFloat(2, 50_000, 5_000_000),
            'invoice_id' => Invoice::factory(),
        ];
    }
}
