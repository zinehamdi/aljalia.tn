<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'country_id', 'city_id',
        'name', 'description', 'price', 'currency',
        'condition', 'image_url', 'is_sold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_sold' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getFormattedPriceAttribute()
    {
        if (! $this->price) {
            return null; // يتفاهم
        }

        return number_format((float) $this->price, 2).' '.$this->currency;
    }
}
