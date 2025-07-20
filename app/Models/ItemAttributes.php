<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAttributes extends Model
{
    use HasFactory;
    protected $guarded = [];
        protected $table = 'item_attributes';

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->id = self::max('id') + 1;
        });
    }
}
