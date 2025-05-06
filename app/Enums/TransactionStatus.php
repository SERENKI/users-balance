<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING    = 'pending';
    case COMPLETED  = 'completed';
    case FAILED     = 'failed';

    public function color(): string
    {
        return match($this) {
            self::COMPLETED => 'success',
            self::PENDING   => 'warning',
            self::FAILED    => 'danger',
        };
    }
}
