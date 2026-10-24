<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminSettings extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Check if bulk email emergency kill-switch is active.
     */
    public static function isBulkEmailKillSwitchActive(): bool
    {
        return (bool) Cache::remember('bulk_email_kill_switch_state', 60, function () {
            $settings = static::select('bulk_email_kill_switch')->first();
            return (bool) ($settings->bulk_email_kill_switch ?? false);
        });
    }

    /**
     * Set the state of the bulk email kill-switch.
     */
    public static function setBulkEmailKillSwitch(bool $active): void
    {
        static::query()->update(['bulk_email_kill_switch' => $active ? 1 : 0]);
        Cache::forget('bulk_email_kill_switch_state');
    }
}
