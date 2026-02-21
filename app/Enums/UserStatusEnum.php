<?php

namespace App\Enums;

enum UserStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
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
                'id' => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
