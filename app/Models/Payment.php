<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property PaymentStatusEnum $status
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'Payment';

    protected $fillable = ['order_id', 'amount', 'status', 'meta'];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatusEnum::class,
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
