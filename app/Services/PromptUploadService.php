<?php

namespace App\Services;

use Image;
use App\Helper;
use App\Models\Stock;
use App\Models\Images;
use App\Models\AdminSettings;
use App\Models\ImageExample;
use Illuminate\Support\Facades\Storage;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use League\ColorExtractor\ColorExtractor;

class PromptUploadService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = AdminSettings::first();
    }

    /**
     * Create a new prompt record and process associated images.
     *
     * @param array $data Form/CSV attributes for the prompt
     * @param mixed $photoFile File path string or UploadedFile object for main photo
     * @param int|null $userId User ID owner (defaults to auth()->id())
     * @param array $examplePhotos Array of example photo files (optional)
     * @return Images
     * @throws \Exception
     */
    public function createPrompt(array $data, $photoFile, ?int $userId = null, array $examplePhotos = []): Images
    {
        $userId = $userId ?: auth()->id();
        $pathFiles      = config('path.files');
        $pathLarge      = config('path.large');
        $pathPreview    = config('path.preview');
        $pathMedium     = config('path.medium');
        $pathSmall      = config('path.small');
        $pathThumbnail  = config('path.thumbnail');

        // File metadata
        $realPath = is_string($photoFile) ? $photoFile : $photoFile->getRealPath();
        if (!file_exists($realPath)) {
            throw new \Exception("Source photo file does not exist: {$realPath}");
        }

        $originalName = is_object($photoFile) && method_exists($photoFile, 'getClientOriginalName')
            ? Helper::fileNameOriginal($photoFile->getClientOriginalName())
            : basename($realPath);

        $extension = is_object($photoFile) && method_exists($photoFile, 'getClientOriginalExtension')
            ? strtolower($photoFile->getClientOriginalExtension())
            : strtolower(pathinfo($realPath, PATHINFO_EXTENSION));

        if (empty($extension)) {
            $extension = 'jpg';
        }

        // EXIF Data
        $exif_data = @exif_read_data($realPath, 0, true);
        $ApertureFNumber = $exif_data['COMPUTED']['ApertureFNumber'] ?? '';

        if (isset($exif_data['EXIF']['ISOSpeedRatings'][0])) {
            $ISO = 'ISO ' . $exif_data['EXIF']['ISOSpeedRatings'][0];
        } elseif (isset($exif_data['EXIF']['ISOSpeedRatings'])) {
            $ISO = 'ISO ' . $exif_data['EXIF']['ISOSpeedRatings'];
        } else {
            $ISO = '';
        }

        $ExposureTime     = $exif_data['EXIF']['ExposureTime'] ?? '';
        $FocalLength      = $exif_data['EXIF']['FocalLength'] ?? '';
        $camera           = $exif_data['IFD0']['Model'] ?? '';
        $dateTimeOriginal = $exif_data['EXIF']['DateTimeOriginal'] ?? null;
        $exif             = trim($FocalLength . ' ' . $ApertureFNumber . ' ' . $ExposureTime . ' ' . $ISO);

        // Generated Filenames
        $title     = trim($data['title'] ?? '');
        $titleSlug = \Illuminate\Support\Str::slug($title ?: 'prompt', '-');
        $large     = strtolower($userId . time() . str_random(100) . '.' . $extension);
        $medium    = strtolower($userId . time() . str_random(100) . '.' . $extension);
        $small     = strtolower($userId . time() . str_random(100) . '.' . $extension);
        $preview   = strtolower($titleSlug . '-' . $userId . time() . str_random(10) . '.' . $extension);
        $thumbnail = strtolower($titleSlug . '-' . $userId . time() . str_random(10) . '.' . $extension);

        // Dimensions and Scaled Resizing
        $widthHeight = @getimagesize($realPath);
        if (!$widthHeight) {
            throw new \Exception("Invalid image file format or corrupted image.");
        }

        $width  = $widthHeight[0];
        $height = $widthHeight[1];

        if ($width > $height) {
            $_scale = ($width > 1280) ? 1280 : 900;
            $previewWidth   = 850 / $width;
            $mediumWidth    = $_scale / $width;
            $smallWidth     = 640 / $width;
            $thumbnailWidth = 280 / $width;
        } else {
            $_scale = ($width > 1280) ? 960 : 800;
            $previewWidth   = 480 / $width;
            $mediumWidth    = $_scale / $width;
            $smallWidth     = 480 / $width;
            $thumbnailWidth = 190 / $width;
        }

        // Preview Image
        $widthPreview = ceil($width * $previewWidth);
        $imgPreview = Image::make($realPath)->orientate()->resize($widthPreview, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension);

        // Small Image
        $widthSmall = ceil($width * $smallWidth);
        $imgSmall = Image::make($realPath)->orientate()->resize($widthSmall, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension);

        // Thumbnail Image
        $widthThumbnail = ceil($width * $thumbnailWidth);
        $imgThumbnail = Image::make($realPath)->orientate()->resize($widthThumbnail, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension);

        // Save to storage
        Storage::put($pathPreview . $preview, $imgPreview, 'public');
        $imagePathSmall = $pathSmall . $small;
        Storage::put($imagePathSmall, $imgSmall, 'public');
        Storage::put($pathThumbnail . $thumbnail, $imgThumbnail, 'public');

        // Free memory
        if (is_object($imgPreview) && method_exists($imgPreview, 'destroy')) {
            $imgPreview->destroy();
        }
        if (is_object($imgSmall) && method_exists($imgSmall, 'destroy')) {
            $imgSmall->destroy();
        }
        if (is_object($imgThumbnail) && method_exists($imgThumbnail, 'destroy')) {
            $imgThumbnail->destroy();
        }

        // Dominant Colors Extraction
        $colors_image = '';
        try {
            $sourceColorPath = (isset($realPath) && file_exists($realPath))
                ? $realPath
                : public_path('uploads/small/' . $small);

            if (file_exists($sourceColorPath)) {
                $palette   = Palette::fromFilename($sourceColorPath);
                $extractor = new ColorExtractor($palette);
                $colors    = $extractor->extract(5);
                $_color    = [];
                foreach ($colors as $color) {
                    $_color[] = trim(Color::fromIntToHex($color), '#');
                }
                $colors_image = implode(',', $_color);
            }
        } catch (\Exception $e) {
            $colors_image = '';
        }

        // Description and Status
        $description = !empty($data['description']) ? Helper::checkTextDb($data['description']) : '';
        $status = ($this->settings->auto_approve_images == 'on') ? 'active' : 'pending';
        $token_id = str_random(200);

        // IPTC Data Check
        $_dataIPTC = false;
        $dataIPTC = Helper::dataIPTC($realPath);
        if ($dataIPTC) {
            if (!empty($dataIPTC['title']) && empty($data['title'])) {
                $title = $dataIPTC['title'];
            }
            if (!empty($dataIPTC['tags']) && empty($data['tags'])) {
                $data['tags'] = implode(', ', $dataIPTC['tags']);
                $_dataIPTC = true;
            }
        }

        // Slug Resolution
        $customSlug = $data['slug'] ?? null;
        $slug = Helper::createImageSlug($customSlug ?: $title);

        // Tags Formatting
        $rawTags = $data['tags'] ?? '';
        $cleanTags = mb_strtolower(Helper::cleanStr($rawTags));

        // Price and sale options
        $price = $data['price'] ?? 0;
        $defaultPrice = $this->settings->default_price_photos ?: $price;

        // Insert Images Record
        $sql                       = new Images();
        $sql->thumbnail            = $thumbnail;
        $sql->preview              = $preview;
        $sql->title                = trim($title);
        $sql->slug                 = $slug;
        $sql->meta_title           = !empty($data['meta_title']) ? trim($data['meta_title']) : null;
        $sql->meta_description     = !empty($data['meta_description']) ? trim($data['meta_description']) : null;
        $sql->meta_keywords        = !empty($data['meta_keywords']) ? trim($data['meta_keywords']) : null;
        $sql->prompt               = trim($data['prompt'] ?? '');
        $sql->ai_model             = $data['ai_model'] ?? 'Other';
        $sql->description          = trim($description);
        $sql->categories_id        = $data['categories_id'] ?? 0;
        $sql->subcategories_id     = !empty($data['subcategories_id']) ? $data['subcategories_id'] : (!empty($data['subcategory']) ? $data['subcategory'] : 0);
        $sql->photoshoot_id        = !empty($data['photoshoot_id']) ? $data['photoshoot_id'] : null;
        $sql->user_id              = $userId;
        $sql->status               = $status;
        $sql->token_id             = $token_id;
        $sql->tags                 = $cleanTags;
        $sql->extension            = strtolower($extension);
        $sql->colors               = $colors_image;
        $sql->exif                 = trim($exif);
        $sql->camera               = $camera;
        $sql->how_use_image        = $data['how_use_image'] ?? 'free';
        $sql->attribution_required = $data['attribution_required'] ?? 'no';
        $sql->original_name        = $originalName;
        $sql->price                = $defaultPrice;
        $sql->item_for_sale        = !empty($data['item_for_sale']) ? $data['item_for_sale'] : 'free';
        $sql->vector               = $data['vector'] ?? '';
        $sql->data_iptc            = $_dataIPTC;
        $sql->date_time_original   = $dateTimeOriginal;
        $sql->save();

        $imageID = $sql->id;

        // Save Stock Record (Small resolution)
        $smallSize = Helper::formatBytes(Storage::disk()->exists($imagePathSmall) ? Storage::disk()->size($imagePathSmall) : 0, 1);
        $stock             = new Stock();
        $stock->images_id  = $imageID;
        $stock->name       = $small;
        $stock->type       = 'small';
        $stock->extension  = $extension;
        $stock->resolution = $widthSmall . 'x' . ceil($height * $smallWidth);
        $stock->size       = $smallSize;
        $stock->token      = $token_id;
        $stock->save();

        // Save Optional Example Photos
        if (!empty($examplePhotos)) {
            $pathExamples = config('path.examples');
            $exCount = 0;
            foreach ($examplePhotos as $exPhoto) {
                if ($exCount >= 5) break;
                $exRealPath = is_string($exPhoto) ? $exPhoto : (is_object($exPhoto) && method_exists($exPhoto, 'getRealPath') ? $exPhoto->getRealPath() : null);
                if ($exRealPath && file_exists($exRealPath)) {
                    $exExt = is_object($exPhoto) && method_exists($exPhoto, 'getClientOriginalExtension')
                        ? strtolower($exPhoto->getClientOriginalExtension())
                        : strtolower(pathinfo($exRealPath, PATHINFO_EXTENSION));
                    if (empty($exExt)) $exExt = 'jpg';

                    $exFileName = strtolower('ex-' . $imageID . '-' . time() . '-' . str_random(15) . '.' . $exExt);
                    $exDimensions = @getimagesize($exRealPath);
                    $exW = $exDimensions[0] ?? 800;
                    $exScale = (850 / $exW);
                    $exPreviewWidth = ceil($exW * min(1, $exScale));

                    $imgExPreview = Image::make($exRealPath)->orientate()->resize($exPreviewWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode($exExt);

                    Storage::put($pathExamples . $exFileName, $imgExPreview, 'public');

                    if (is_object($imgExPreview) && method_exists($imgExPreview, 'destroy')) {
                        $imgExPreview->destroy();
                    }

                    ImageExample::create([
                        'images_id' => $imageID,
                        'file' => $exFileName,
                    ]);
                    $exCount++;
                }
            }
        }

        return $sql;
    }
}
