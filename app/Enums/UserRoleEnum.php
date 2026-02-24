<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case Admin    = 'admin';
    case Merchant = 'merchant';

    public function label(): string
    {
        return match ($this) {
            self::Admin    => 'Admin',
            self::Merchant => 'Merchant',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $role) => ['id' => $role->value, 'label' => $role->label()],
            self::cases()
        );
    }
}
