<?php

namespace App\Http\Controllers\Traits;

use Image;
use App\Helper;
use App\Models\Stock;
use App\Models\Images;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Services\PromptUploadService;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\ColorExtractor\ColorExtractor;

trait UploadTrait
{
  public function __construct(AdminSettings $settings, Request $request)
  {
    $this->settings = $settings::first();
    $this->request = $request;
  }

  public function create()
  {
    return $this->upload('photo');
  }

  protected function validator(array $data, $type)
  {
    Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
      return !preg_match('/[^x00-x7F\-]/i', $value);
    });

    $sizeAllowed = $this->settings->file_size_allowed * 1024;

    $dimensions = explode('x', $this->settings->min_width_height_image);
    $dimW = (int) ($dimensions[0] ?? 1024);
    $dimH = (int) ($dimensions[1] ?? 768);

    $minDim = min($dimW, $dimH);
    $maxDim = max($dimW, $dimH);

    $photoFile = $data['photo'] ?? null;
    if ($photoFile && is_object($photoFile) && method_exists($photoFile, 'getRealPath') && $photoFile->getRealPath()) {
      $sizes = @getimagesize($photoFile->getRealPath());
      if ($sizes && isset($sizes[0], $sizes[1])) {
        if ($sizes[0] < $sizes[1]) {
          $minWidthRule = $minDim;
          $minHeightRule = $maxDim;
        } else {
          $minWidthRule = $maxDim;
          $minHeightRule = $minDim;
        }
      } else {
        $minWidthRule = $dimW;
        $minHeightRule = $dimH;
      }
    } else {
      $minWidthRule = $dimW;
      $minHeightRule = $dimH;
    }

    if ($this->settings->currency_position == 'right') {
      $currencyPosition =  2;
    } else {
      $currencyPosition =  null;
    }

    $messages = [
      'photo.required' => __('misc.please_select_image'),
      "photo.max"   => __('misc.max_size') . ' ' . Helper::formatBytes($sizeAllowed, 1),
      "price.required_if" => __('misc.price_required'),
      'price.min' => __('misc.price_minimum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'price.max' => __('misc.price_maximum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'categories_id.required' =>  __('misc.please_select_category')
    ];

    // Create Rules
    return Validator::make($data, [
      'photo'       => 'required|mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=' . $minWidthRule . ',min_height=' . $minHeightRule . '|max:' . $this->settings->file_size_allowed . '',
      'title'       => 'required|min:3|max:' . $this->settings->title_length . '',
      'prompt'      => 'required|string|min:5',
      'ai_model'    => 'required|in:' . implode(',', Images::getAiModels()),
      'description' => 'nullable|min:2|max:' . $this->settings->description_length . '',
      'meta_title'       => 'nullable|string|max:255',
      'meta_description' => 'nullable|string|max:500',
      'meta_keywords'    => 'nullable|string|max:255',
      'tags'        => 'required',
      'categories_id' =>  'required',
      'file' => 'max:' . $this->settings->file_size_allowed_vector . '',
    ], $messages);
  }

  // Store Image
  protected function upload($type)
  {
    try {

      if ($this->settings->who_can_upload == 'admin' && ! auth()->user()->isSuperAdmin()) {
        return response()->json([
          'success' => false,
          'errors' => ['error' => __('misc.error_upload')],
        ]);
      }

      $input = $this->request->all();

      if (!$this->request->price) {
        $price = 0;
      } else {
        $price = $input['price'];
      }

      $input['tags'] = Helper::cleanStr($input['tags'] ?? '');
      $tags = $input['tags'];

      if (strlen($tags) == 1) {
        return response()->json([
          'success' => false,
          'errors' => ['error' => __('validation.required', ['attribute' => __('misc.tags')])],
        ]);
      }

      $validator = $this->validator($input, $type);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->getMessageBag()->toArray(),
        ]);
      }

      $photo = $this->request->file('photo');
      $examplePhotos = $this->request->hasFile('example_photos') ? $this->request->file('example_photos') : [];

      /** @var PromptUploadService $service */
      $service = app(PromptUploadService::class);
      $image = $service->createPrompt($input, $photo, auth()->id(), $examplePhotos);

      return response()->json([
        'success' => true,
        'target' => url('prompt', $image->slug),
      ]);
    } catch (\Exception $e) {

      return response()->json([
        'success' => false,
        'errors' => ['error' => __('misc.error') . ' - ' . $e->getMessage()],
      ]);
    }
  }
}
