<?php

namespace App\Enums;

enum FormSubmissionStatusEnum: string
{
    case Pending   = 'pending';
    case Processed = 'processed';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Processed => 'Processed',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $status) => ['id' => $status->value, 'label' => $status->label()],
            self::cases()
        );
    }
}
