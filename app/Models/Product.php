<?php

namespace App\Models;

use App\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $stock
 * @property int $category_id
 *
 * @property-read Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|Cart[] $carts
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[] $orders
 */
class Product extends Model
{
    use HasFactory, SoftDeletes, CommonQueryScopes;

    protected $table = 'Product';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Category ilişki (N:1)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Cart ilişki (1:N)
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Order ilişki (M:N - pivot: OrderProduct)
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'OrderProduct')
            ->using(OrderProduct::class)
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }
}
