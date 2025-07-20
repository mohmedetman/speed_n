<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\ArrayToJsonCast;

class Cart extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'module_id' => 'integer',
        'item_id' => 'integer',
        'is_guest' => 'boolean',
        'price' => 'float',
        'quantity' => 'integer',
        'add_on_ids' => 'array',
        'add_on_qtys' => 'array',
        'variation' => 'array',
        'components'=> 'array',
    ];

    protected $fillable = [
        'user_id',
        'module_id',
        'item_id',
        'is_guest',
        'add_on_ids',
        'add_on_qtys',
        'item_type',
        'price',
        'quantity',
        'variation',
        'components'
    ];

    public function item()
    {
        return $this->morphTo();
    }
}
