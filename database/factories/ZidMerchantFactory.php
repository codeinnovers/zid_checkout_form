<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ZidMerchant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZidMerchantFactory extends Factory
{
    protected $model = ZidMerchant::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'username' => $this->faker->unique()->userName(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $user->email,
            'token' => $this->faker->sha256(),
            'xmanager_token' => $this->faker->sha256(),
            'refresh_token' => $this->faker->sha256(),
            'token_expiration' => $this->faker->dateTimeBetween('now', '+1 year'),
            'store_reference' => $this->faker->uuid(),
        ];
    }
}
