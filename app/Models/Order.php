<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property float $total_amount
 * @property OrderStatusEnum $status
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'Order';

    protected $fillable = ['user_id', 'total_amount', 'status'];

    protected $casts = [
        'total_amount' => 'float',
        'status' => OrderStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'OrderProduct')
            ->using(OrderProduct::class)
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }
}
