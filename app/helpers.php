<?php

if (!function_exists('seo')) {
    /**
     * Get the SEO service singleton instance.
     *
     * @return \App\Services\SeoService
     */
    function seo(): \App\Services\SeoService
    {
        return app(\App\Services\SeoService::class);
    }
}
