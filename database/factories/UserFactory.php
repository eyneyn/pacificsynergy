<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'employee_number' => 'PD' . $this->faker->unique()->numerify('###'),
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'department' => $this->faker->randomElement(['Production Department', 'IT Department', 'HR Department']),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Pacific123.'), // Hashing password
            'remember_token' => Str::random(10),
            'position_id' => rand(0, 4), // Make sure this ID exists or generate dynamically in seeder
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
