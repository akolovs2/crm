<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['call', 'email', 'meeting', 'task', 'note']);
        $dueAt = fake()->optional(0.7)->dateTimeBetween('-1 month', '+1 month');

        return [
            'type' => $type,
            'subject' => match ($type) {
                'call' => 'Call with ' . fake()->name(),
                'email' => 'Follow up: ' . fake()->bs(),
                'meeting' => 'Meeting: ' . fake()->bs(),
                'task' => fake()->sentence(4),
                'note' => 'Note: ' . fake()->sentence(4),
            },
            'description' => fake()->optional()->paragraph(),
            'due_at' => $dueAt,
            'completed_at' => $dueAt && $dueAt < new \DateTime() && fake()->boolean(40)
                ? fake()->dateTimeBetween($dueAt, 'now')
                : null,
            'owner_id' => User::factory(),
        ];
    }
}
