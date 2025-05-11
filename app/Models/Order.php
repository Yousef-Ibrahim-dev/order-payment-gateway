<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'uuid','order_number','user_id','status',
        'sub_total','tax','discount','currency','total',
        'shipping_address','billing_address','metadata',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'metadata'         => 'array',
        'sub_total'        => 'decimal:2',
        'tax'              => 'decimal:2',
        'discount'         => 'decimal:2',
        'total'            => 'decimal:2',
    ];

/*    public function getRouteKeyName(): string
    {
        return 'uuid';
    }*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
