<?php

namespace App\Services;

use App\Helper;
use App\Models\Images;
use App\Models\AdminSettings;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class CsvBulkUploadProcessor
{
    protected $promptUploadService;
    protected $settings;

    public function __construct(PromptUploadService $promptUploadService)
    {
        $this->promptUploadService = $promptUploadService;
        $this->settings = AdminSettings::first();
    }

    /**
     * Process a CSV Bulk Upload batch.
     *
     * @param string $csvFilePath Path to the uploaded CSV file
     * @param array $globalSettings Global upload options from the page UI
     * @param array $imageFiles Array of UploadedFile objects (optional if ZIP provided)
     * @param string|null $zipFilePath Path to uploaded ZIP file (optional)
     * @param \App\Models\User|null $user
     * @return array
     * @throws \Exception
     */
    public function process(string $csvFilePath, array $globalSettings, array $imageFiles = [], ?string $zipFilePath = null, $user = null): array
    {
        $user = $user ?: auth()->user();

        // Daily upload limit check for non-admin users
        if ($this->settings->limit_upload_user != 0 && !$user->isSuperAdmin()) {
            $dailyUploads = $user->dailyUploads();
            if ($dailyUploads >= $this->settings->limit_upload_user) {
                throw new \Exception(__('misc.limit_uploads_user'));
            }
        }

        // 1. Index Image Files Pool
        $imagePool = []; // lowercase_filename => file_path
        $tempExtractDir = null;

        // If ZIP file provided, extract it
        if ($zipFilePath && file_exists($zipFilePath)) {
            if (!class_exists('ZipArchive')) {
                throw new \Exception("The PHP ZipArchive extension is not enabled on server. Please upload individual image files instead.");
            }
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                $tempExtractDir = storage_path('app/tmp/bulk_' . uniqid());
                if (!file_exists($tempExtractDir)) {
                    mkdir($tempExtractDir, 0755, true);
                }
                $zip->extractTo($tempExtractDir);
                $zip->close();

                // Recursively collect extracted image files
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempExtractDir));
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $ext = strtolower($file->getExtension());
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'jpe'])) {
                            $fileName = strtolower(basename($file->getPathname()));
                            $imagePool[$fileName] = $file->getPathname();
                        }
                    }
                }
            } else {
                throw new \Exception("Failed to open ZIP archive.");
            }
        }

        // Add multi-file uploaded images if any
        if (!empty($imageFiles)) {
            foreach ($imageFiles as $img) {
                if ($img && is_object($img) && method_exists($img, 'getClientOriginalName') && $img->isValid()) {
                    $fileName = strtolower(basename($img->getClientOriginalName()));
                    $imagePool[$fileName] = $img;
                }
            }
        }

        if (empty($imagePool)) {
            throw new \Exception("No valid image files provided. Please attach image files or a ZIP archive.");
        }

        // 2. Open and Parse CSV
        if (!file_exists($csvFilePath) || !is_readable($csvFilePath)) {
            throw new \Exception("CSV file could not be read.");
        }

        $handle = fopen($csvFilePath, 'r');
        if (!$handle) {
            throw new \Exception("Unable to open CSV file.");
        }

        // Remove BOM if present
        $firstLine = fgets($handle);
        if ($firstLine !== false) {
            $bom = pack('H*', 'EFBBBF');
            $firstLine = preg_replace("/^$bom/", '', $firstLine);
            fseek($handle, 0);
        }

        // Read header
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            throw new \Exception("CSV file is empty or corrupted.");
        }

        // Standardize header column names
        $headerMap = [];
        foreach ($header as $index => $col) {
            $cleanCol = strtolower(trim($col));
            $headerMap[$cleanCol] = $index;
        }

        // Validate required header columns
        $requiredHeaders = ['prompt', 'file_name', 'title', 'keywords'];
        $missingHeaders = [];
        foreach ($requiredHeaders as $req) {
            if (!array_key_exists($req, $headerMap)) {
                $missingHeaders[] = $req;
            }
        }

        if (!empty($missingHeaders)) {
            fclose($handle);
            throw new \Exception("Missing required CSV header columns: " . implode(', ', $missingHeaders));
        }

        // Dimension rules
        $dimensions = explode('x', $this->settings->min_width_height_image);
        $dimW = (int) ($dimensions[0] ?? 1024);
        $dimH = (int) ($dimensions[1] ?? 768);
        $minDim = min($dimW, $dimH);
        $maxDim = max($dimW, $dimH);

        // Create/Identify Photoshoot for Batch
        $photoshoot = null;
        $photoshootTitle = trim($globalSettings['photoshoot_title'] ?? '');
        if (!empty($photoshootTitle)) {
            $photoshootSlug = \Illuminate\Support\Str::slug($photoshootTitle);
            $originalSlug = $photoshootSlug;
            $count = 1;
            while (\App\Models\Photoshoot::where('slug', $photoshootSlug)->exists()) {
                $photoshootSlug = $originalSlug . '-' . $count;
                $count++;
            }
            $photoshoot = \App\Models\Photoshoot::create([
                'uuid' => 'batch_' . uniqid(),
                'title' => $photoshootTitle,
                'slug' => $photoshootSlug,
                'user_id' => $user->id,
                'categories_id' => $globalSettings['categories_id'] ?? null,
            ]);
        }

        // Process CSV Rows
        $rowNum = 0;
        $successCount = 0;
        $failedCount = 0;
        $logs = [];
        $failedRowsData = [];

        try {
            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty lines
                if (empty(array_filter($row))) {
                    continue;
                }

                $rowNum++;

                if ($rowNum > 100) {
                    // Maximum row cap per batch
                    $logs[] = [
                        'row' => $rowNum,
                        'file_name' => 'N/A',
                        'title' => 'N/A',
                        'status' => 'failed',
                        'error' => "Reached maximum batch limit of 100 rows per import."
                    ];
                    break;
                }

                // Daily upload limit check per row
                if ($this->settings->limit_upload_user != 0 && !$user->isSuperAdmin()) {
                    if ($user->dailyUploads() >= $this->settings->limit_upload_user) {
                        $logs[] = [
                            'row' => $rowNum,
                            'file_name' => 'N/A',
                            'title' => 'N/A',
                            'status' => 'failed',
                            'error' => __('misc.limit_uploads_user')
                        ];
                        $failedCount++;
                        continue;
                    }
                }

                // Extract row values safely
                $getRow = function ($colName) use ($headerMap, $row) {
                    $idx = $headerMap[$colName] ?? null;
                    return ($idx !== null && isset($row[$idx])) ? trim($row[$idx]) : '';
                };

                $prompt         = $getRow('prompt');
                $fileName       = basename($getRow('file_name'));
                $title          = $getRow('title');
                $keywords       = $getRow('keywords');
                $slug           = $getRow('slug');
                $description    = $getRow('description');
                $metaTitle      = $getRow('meta_title');
                $metaDescription= $getRow('meta_description');
                $metaKeywords   = $getRow('meta_keywords');

                $lookupName = strtolower($fileName);

                // Row Level Validation
                $errors = [];

                if (empty($fileName)) {
                    $errors[] = "file_name is required.";
                } elseif (!array_key_exists($lookupName, $imagePool)) {
                    $errors[] = "Image file '{$fileName}' not found in uploaded files or ZIP archive.";
                }

                if (empty($prompt) || strlen($prompt) < 5) {
                    $errors[] = "prompt must be at least 5 characters.";
                }

                if (empty($title) || strlen($title) < 3) {
                    $errors[] = "title must be at least 3 characters.";
                } elseif (strlen($title) > $this->settings->title_length) {
                    $errors[] = "title exceeds maximum length of {$this->settings->title_length} characters.";
                }

                if (empty($keywords)) {
                    $errors[] = "keywords (tags) are required.";
                } else {
                    $tagsArray = array_filter(explode(',', $keywords));
                    if (count($tagsArray) > $this->settings->tags_limit) {
                        $errors[] = "keywords exceeds maximum allowed tags limit of {$this->settings->tags_limit}.";
                    }
                }

                // Validate image file dimensions if file exists
                if (empty($errors) && isset($imagePool[$lookupName])) {
                    $targetFile = $imagePool[$lookupName];
                    $targetPath = is_object($targetFile) ? $targetFile->getRealPath() : $targetFile;

                    if (!file_exists($targetPath)) {
                        $errors[] = "Image file '{$fileName}' is missing or unreadable.";
                    } else {
                        $sizes = @getimagesize($targetPath);
                        if (!$sizes || !isset($sizes[0], $sizes[1])) {
                            $errors[] = "Image file '{$fileName}' is not a valid image format.";
                        } else {
                            $imgMin = min($sizes[0], $sizes[1]);
                            $imgMax = max($sizes[0], $sizes[1]);
                            if ($imgMin < $minDim || $imgMax < $maxDim) {
                                $errors[] = "Image dimensions for '{$fileName}' ({$sizes[0]}x{$sizes[1]}) do not meet minimum requirements ({$dimW}x{$dimH}).";
                            }
                        }
                    }
                }

                // If errors exist for this row
                if (!empty($errors)) {
                    $failedCount++;
                    $errorMsg = implode(' ', $errors);
                    $logs[] = [
                        'row' => $rowNum,
                        'file_name' => $fileName ?: 'N/A',
                        'title' => $title ?: 'N/A',
                        'status' => 'failed',
                        'error' => $errorMsg
                    ];
                    $failedRowsData[] = [
                        'prompt' => $prompt,
                        'file_name' => $fileName,
                        'title' => $title,
                        'keywords' => $keywords,
                        'slug' => $slug,
                        'description' => $description,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => $metaKeywords,
                        'error_reason' => $errorMsg
                    ];
                    continue;
                }

                // Process Row & Create Prompt Record
                try {
                    $rowPromptData = array_merge($globalSettings, [
                        'prompt'           => $prompt,
                        'title'            => $title,
                        'tags'             => $keywords,
                        'slug'             => $slug,
                        'description'      => $description,
                        'meta_title'       => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords'    => $metaKeywords,
                        'photoshoot_id'    => $photoshoot ? $photoshoot->id : null,
                    ]);

                    $matchedImage = $imagePool[$lookupName];
                    $createdImage = $this->promptUploadService->createPrompt($rowPromptData, $matchedImage, $user->id);

                    if ($photoshoot) {
                        $photoshoot->increment('prompts_count');
                    }

                    $successCount++;
                    $logs[] = [
                        'row' => $rowNum,
                        'file_name' => $fileName,
                        'title' => $createdImage->title,
                        'status' => 'success',
                        'prompt_id' => $createdImage->id,
                        'slug' => $createdImage->slug,
                        'url' => url('prompt/' . $createdImage->slug),
                        'error' => null
                    ];
                } catch (\Exception $ex) {
                    $failedCount++;
                    $errorMsg = "Error saving prompt: " . $ex->getMessage();
                    $logs[] = [
                        'row' => $rowNum,
                        'file_name' => $fileName,
                        'title' => $title,
                        'status' => 'failed',
                        'error' => $errorMsg
                    ];
                    $failedRowsData[] = [
                        'prompt' => $prompt,
                        'file_name' => $fileName,
                        'title' => $title,
                        'keywords' => $keywords,
                        'slug' => $slug,
                        'description' => $description,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => $metaKeywords,
                        'error_reason' => $errorMsg
                    ];
                }
            }
        } finally {
            fclose($handle);

            // Clean up temporary unzipped folder if created
            if ($tempExtractDir && file_exists($tempExtractDir)) {
                $this->deleteDirectory($tempExtractDir);
            }
        }

        return [
            'total_rows' => $rowNum,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'logs' => $logs,
            'failed_rows' => $failedRowsData
        ];
    }

    /**
     * Recursively delete directory.
     */
    protected function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($dir);
    }
}
