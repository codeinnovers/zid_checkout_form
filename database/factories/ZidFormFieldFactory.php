<?php

namespace Database\Factories;

use App\Enums\FormFieldStatusEnum;
use App\Models\ZidFormField;
use App\Models\ZidMerchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ZidFormField>
 */
class ZidFormFieldFactory extends Factory
{
    protected $model = ZidFormField::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['text', 'email', 'phone', 'number', 'textarea', 'select', 'radio', 'checkbox', 'date']);

        return [
            'zid_merchant_id' => ZidMerchant::factory(),
            'field_type'      => $type,
            'name'            => fake()->unique()->regexify('[a-z]{4,8}_[a-z]{3,6}'),
            'label'           => fake()->words(2, true),
            'label_ar'        => fake()->word(),
            'placeholder'     => fake()->sentence(3),
            'placeholder_ar'  => null,
            'options'         => in_array($type, ['select', 'radio']) ? $this->makeOptions() : null,
            'default_value'   => null,
            'validation_rules' => null,
            'sort_order'      => fake()->numberBetween(1, 20),
            'is_required'     => fake()->boolean(40),
            'status'          => FormFieldStatusEnum::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => FormFieldStatusEnum::Active]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => FormFieldStatusEnum::Inactive]);
    }

    public function required(): static
    {
        return $this->state(['is_required' => true]);
    }

    private function makeOptions(): array
    {
        return collect(range(1, fake()->numberBetween(2, 4)))->map(fn ($i) => [
            'value' => fake()->slug(1),
            'label' => fake()->word(),
        ])->toArray();
    }
}
