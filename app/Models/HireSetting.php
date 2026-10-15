<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HireSetting extends Model
{
    protected $table = 'hire_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'form_categories' => 'array',
        'form_shoot_types' => 'array',
        'form_intended_uses' => 'array',
        'form_budgets_usd' => 'array',
        'form_budgets_inr' => 'array',
        'price_1_sku_usd' => 'float',
        'price_3_5_sku_usd' => 'float',
        'price_6_plus_sku_usd' => 'float',
        'price_1_sku_inr' => 'float',
        'price_3_5_sku_inr' => 'float',
        'price_6_plus_sku_inr' => 'float',
    ];

    /**
     * Get the active settings record with singleton caching
     */
    public static function getSettings()
    {
        return cache()->remember('hire_settings_data', 86400, function () {
            return self::first() ?: new self();
        });
    }

    /**
     * Clear the cache when settings are saved
     */
    protected static function booted()
    {
        static::saved(function () {
            cache()->forget('hire_settings_data');
        });
    }
}
