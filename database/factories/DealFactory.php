<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->bs() . ' Deal',
            'value' => fake()->randomFloat(2, 500, 100000),
            'currency' => 'USD',
            'stage' => fake()->randomElement(['lead', 'qualified', 'proposal', 'negotiation', 'won', 'lost']),
            'expected_close_date' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'contact_id' => fake()->optional(0.7)->randomElement(
                Contact::pluck('id')->toArray() ?: [null]
            ),
            'company_id' => fake()->optional(0.7)->randomElement(
                Company::pluck('id')->toArray() ?: [null]
            ),
            'owner_id' => User::factory(),
        ];
    }
}
