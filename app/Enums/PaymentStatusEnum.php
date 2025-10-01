<?php

namespace App\Enums;

enum PaymentStatusEnum: string {
    case SUCCESS = 'success';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
