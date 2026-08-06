<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role_id' => function () {
                return \App\Models\Role::where('slug', 'sales')->first()?->id ?? 1;
            },
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
