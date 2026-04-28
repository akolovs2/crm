<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement([
                'Technology', 'Finance', 'Healthcare', 'Retail',
                'Manufacturing', 'Education', 'Real Estate', 'Consulting',
            ]),
            'website' => fake()->optional()->url(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->companyEmail(),
            'address' => fake()->optional()->streetAddress(),
            'city' => fake()->optional()->city(),
            'country' => fake()->optional()->country(),
            'owner_id' => User::factory(),
        ];
    }
}
