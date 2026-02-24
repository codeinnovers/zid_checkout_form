<?php

namespace App\Enums;

enum FormFieldStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Inactive => 'Inactive',
        };
    }

    /**
     * Get all options for dropdowns/selects.
     *
     * @return array<int, array{id: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status) => [
                'id'    => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
