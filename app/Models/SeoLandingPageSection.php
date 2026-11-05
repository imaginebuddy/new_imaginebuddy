<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoLandingPageSection extends Model
{
    protected $table = 'seo_landing_page_sections';

    protected $touches = ['landingPage'];

    protected $fillable = [
        'landing_page_id',
        'type',
        'badge',
        'heading',
        'subheading',
        'content',
        'settings',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Parent landing page.
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(SeoLandingPage::class, 'landing_page_id');
    }

    /**
     * Items associated directly with this section.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SeoLandingPageItem::class, 'section_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Active items associated with this section.
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    /**
     * Structured settings data.
     */
    public function getSettingsDataAttribute(): array
    {
        return is_array($this->settings) ? $this->settings : [];
    }

    /**
     * Structured cards (for prompt grid, benefit grid, persona grid, feature blocks).
     */
    public function getCardsAttribute(): array
    {
        $settings = $this->settings_data;
        if (isset($settings['cards']) && is_array($settings['cards'])) {
            return $settings['cards'];
        }
        if (!empty($settings) && array_is_list($settings)) {
            return $settings;
        }
        return [];
    }

    /**
     * Structured workflow steps.
     */
    public function getStepsAttribute(): array
    {
        $settings = $this->settings_data;
        if (isset($settings['steps']) && is_array($settings['steps'])) {
            return $settings['steps'];
        }
        if (!empty($settings) && array_is_list($settings)) {
            return $settings;
        }
        return [];
    }

    /**
     * Structured technical & compliance specs matrix.
     */
    public function getSpecsAttribute(): array
    {
        $settings = $this->settings_data;
        if (isset($settings['specs']) && is_array($settings['specs'])) {
            return $settings['specs'];
        }
        if (!empty($settings) && array_is_list($settings)) {
            return $settings;
        }
        return [];
    }

    /**
     * Structured stats / metric counters.
     */
    public function getStatsAttribute(): array
    {
        $settings = $this->settings_data;
        if (isset($settings['stats']) && is_array($settings['stats'])) {
            return $settings['stats'];
        }
        if (!empty($settings) && array_is_list($settings)) {
            return $settings;
        }
        return [];
    }

    /**
     * Structured FAQs list.
     */
    public function getFaqsAttribute(): array
    {
        $settings = $this->settings_data;
        if (isset($settings['faqs']) && is_array($settings['faqs'])) {
            return $settings['faqs'];
        }
        if (!empty($settings) && array_is_list($settings)) {
            return $settings;
        }
        return [];
    }
}
