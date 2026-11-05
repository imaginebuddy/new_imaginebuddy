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
     * Structured cards (for benefit grid, persona grid, feature blocks).
     */
    public function getCardsAttribute(): array
    {
        $settings = $this->settings_data;
        return isset($settings['cards']) && is_array($settings['cards']) ? $settings['cards'] : [];
    }

    /**
     * Structured workflow steps.
     */
    public function getStepsAttribute(): array
    {
        $settings = $this->settings_data;
        return isset($settings['steps']) && is_array($settings['steps']) ? $settings['steps'] : [];
    }

    /**
     * Structured technical & compliance specs matrix.
     */
    public function getSpecsAttribute(): array
    {
        $settings = $this->settings_data;
        return isset($settings['specs']) && is_array($settings['specs']) ? $settings['specs'] : [];
    }

    /**
     * Structured stats / metric counters.
     */
    public function getStatsAttribute(): array
    {
        $settings = $this->settings_data;
        return isset($settings['stats']) && is_array($settings['stats']) ? $settings['stats'] : [];
    }

    /**
     * Structured FAQs list.
     */
    public function getFaqsAttribute(): array
    {
        $settings = $this->settings_data;
        return isset($settings['faqs']) && is_array($settings['faqs']) ? $settings['faqs'] : [];
    }
}
