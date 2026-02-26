<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ZidMerchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ZidMerchant>
 */
class ZidMerchantFactory extends Factory
{
    protected $model = ZidMerchant::class;

    public function definition(): array
    {
        $storeName = fake()->company() . ' Store';

        return [
            'user_id'          => User::factory()->merchant(),
            'name'             => $storeName,
            'username'         => fake()->unique()->regexify('[a-z]{4,8}_[a-z]{3,6}'),
            'phone'            => '+966' . fake()->numerify('5########'),
            'email'            => fake()->unique()->companyEmail(),
            'token'            => fake()->sha256(),
            'xmanager_token'   => fake()->sha256(),
            'refresh_token'    => fake()->sha256(),
            'token_expiration' => fake()->dateTimeBetween('now', '+1 year'),
            'store_reference'  => 'store-' . fake()->unique()->numerify('######'),
        ];
    }

    /**
     * Merchant with an expired token.
     */
    public function expiredToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'token_expiration' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }
}
