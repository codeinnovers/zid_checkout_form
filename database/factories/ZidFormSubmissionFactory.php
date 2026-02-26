<?php

namespace Database\Factories;

use App\Enums\FormSubmissionStatusEnum;
use App\Models\ZidFormSubmission;
use App\Models\ZidMerchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ZidFormSubmission>
 */
class ZidFormSubmissionFactory extends Factory
{
    protected $model = ZidFormSubmission::class;

    public function definition(): array
    {
        return [
            'zid_merchant_id' => ZidMerchant::factory(),
            'order_number'    => 'ORD-' . fake()->unique()->numerify('######'),
            'status'          => fake()->randomElement(FormSubmissionStatusEnum::cases()),
            'form_data'       => [
                'customer_name'  => fake()->name(),
                'customer_phone' => '+966' . fake()->numerify('5########'),
            ],
            'attachments'     => null,
            'ip_address'      => fake()->ipv4(),
            'created_at'      => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => FormSubmissionStatusEnum::Pending]);
    }

    public function processed(): static
    {
        return $this->state(['status' => FormSubmissionStatusEnum::Processed]);
    }
}
