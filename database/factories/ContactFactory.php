<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->boolean(90) ? fake()->unique()->safeEmail() : null,
            'phone' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'company_id' => fake()->optional(0.8)->randomElement(
                Company::pluck('id')->toArray() ?: [null]
            ),
            'owner_id' => User::factory(),
        ];
    }
}
