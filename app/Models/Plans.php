<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
  		return $this->belongsTo(User::class)->first();
  	}

    public function getLocalizedPriceAttribute()
    {
        return \App\Helper::isIndia() ? ($this->price_inr ?: 284.00) : ($this->price ?: 3.00);
    }

    public function getLocalizedPriceYearAttribute()
    {
        return \App\Helper::isIndia() ? ($this->price_year_inr ?: 2550.00) : ($this->price_year ?: 27.00);
    }

    public function getLocalizedCurrencySymbolAttribute()
    {
        return \App\Helper::isIndia() ? '₹' : '$';
    }

    public function getLocalizedCurrencyCodeAttribute()
    {
        return \App\Helper::isIndia() ? 'INR' : 'USD';
    }
}
