<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Images;

class NormalizeSearchMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:normalize-metadata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean and normalize prompt titles and tags for high-precision deterministic search';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting prompt metadata normalization...');

        $images = Images::all();
        $updated = 0;

        foreach ($images as $img) {
            $changed = false;

            // 1. Clean Title
            $cleanTitle = trim(preg_replace('/\s+/', ' ', $img->title));
            if ($cleanTitle !== $img->title) {
                $img->title = $cleanTitle;
                $changed = true;
            }

            // 2. Clean and Normalize Tags
            if (!empty($img->tags)) {
                $rawTags = explode(',', strtolower($img->tags));
                $cleanTags = [];

                foreach ($rawTags as $t) {
                    $t = trim($t);
                    $t = str_replace(['_'], ' ', $t);
                    $t = preg_replace('/\s+/', ' ', $t);

                    if (!empty($t) && !in_array($t, $cleanTags)) {
                        $cleanTags[] = $t;
                    }
                }

                $normalizedTags = implode(', ', array_slice($cleanTags, 0, 20));
                if ($normalizedTags !== $img->tags) {
                    $img->tags = $normalizedTags;
                    $changed = true;
                }
            }

            if ($changed) {
                $img->save();
                $updated++;
            }
        }

        $this->info("Metadata normalization complete! Updated {$updated} prompts.");
        return 0;
    }
}
