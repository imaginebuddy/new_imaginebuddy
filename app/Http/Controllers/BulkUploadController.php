<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use App\Models\Categories;
use App\Models\Photoshoot;
use App\Models\Subcategories;
use App\Services\CsvBulkUploadProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BulkUploadController extends Controller
{
    protected $settings;
    protected $processor;

    public function __construct(AdminSettings $settings, CsvBulkUploadProcessor $processor)
    {
        $this->settings = $settings::first();
        $this->processor = $processor;
    }

    /**
     * Display the Bulk Upload page.
     */
    public function bulkUpload()
    {
        if (auth()->user()->authorized_to_upload != 'yes' && !auth()->user()->isSuperAdmin()) {
            return redirect('/');
        }

        $photoshootsQuery = Photoshoot::query();
        if (!auth()->user()->isSuperAdmin()) {
            $photoshootsQuery->where('user_id', auth()->id());
        }
        $photoshoots = $photoshootsQuery->orderBy('title', 'asc')->get(['id', 'title', 'prompts_count', 'categories_id']);

        $categories = Categories::where('mode', 'on')->orderBy('name')->get();
        return view('images.bulk-upload', compact('categories', 'photoshoots'));
    }

    /**
     * Process CSV Bulk Upload request.
     */
    public function bulkUploadStore(Request $request)
    {
        if (auth()->user()->authorized_to_upload != 'yes' && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => __('misc.error_upload')]
            ], 403);
        }

        $request->validate([
            'csv_file'       => 'required|file|mimes:csv,txt|max:10240',
            'categories_id'  => 'required',
            'ai_model'       => 'required',
            'photoshoot_id'  => 'nullable',
            'photoshoot_title' => 'nullable|string|max:255',
        ], [
            'csv_file.required'      => 'Please select a CSV file to upload.',
            'csv_file.mimes'         => 'The CSV file must be a file of type: .csv',
            'categories_id.required' => __('misc.please_select_category'),
            'ai_model.required'      => 'Please select an AI Model.',
        ]);

        if (!$request->hasFile('images') && !$request->hasFile('zip_file')) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => 'Please select image files or a ZIP archive containing your images.']
            ], 422);
        }

        try {
            $csvFile = $request->file('csv_file');
            $zipFile = $request->hasFile('zip_file') ? $request->file('zip_file')->getRealPath() : null;
            $imageFiles = $request->hasFile('images') ? $request->file('images') : [];

            $globalSettings = [
                'categories_id'        => $request->categories_id,
                'subcategories_id'     => $request->subcategory ?: null,
                'photoshoot_id'        => $request->photoshoot_id ?: null,
                'photoshoot_title'     => $request->photoshoot_title ?: null,
                'ai_model'             => $request->ai_model,
                'item_for_sale'        => $request->item_for_sale ?: 'free',
                'how_use_image'        => $request->how_use_image ?: 'free',
                'attribution_required' => $request->attribution_required ?? 'no',
                'type_image'           => $request->type_image ?: 'image',
            ];

            $result = $this->processor->process(
                $csvFile->getRealPath(),
                $globalSettings,
                $imageFiles,
                $zipFile,
                auth()->user()
            );

            return response()->json([
                'success'       => true,
                'total_rows'    => $result['total_rows'],
                'success_count' => $result['success_count'],
                'failed_count'  => $result['failed_count'],
                'logs'          => $result['logs'],
                'failed_rows'   => $result['failed_rows'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors'  => ['error' => $e->getMessage()]
            ], 422);
        }
    }

    /**
     * Download sample CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bulk_upload_template.csv"',
        ];

        $columns = [
            'prompt',
            'file_name',
            'title',
            'keywords',
            'slug',
            'description',
            'meta_title',
            'meta_description',
            'meta_keywords'
        ];

        $sampleRow1 = [
            'High quality product shot of a luxury perfume bottle on marble pedestal with soft lighting',
            'perfume_bottle.jpg',
            'Luxury Perfume Bottles Packaging Shot',
            'perfume, luxury, bottle, cosmetic, product photography, studio light',
            'luxury-perfume-bottle-shot',
            'Detailed prompt for generating commercial perfume product photos using Midjourney or Flux.',
            'Luxury Perfume Bottle Product Shot AI Prompt',
            'Get the ultimate AI prompt for generating hyper-realistic luxury perfume bottle product photography.',
            'perfume prompt, luxury product photo, ai prompt, midjourney prompt'
        ];

        $sampleRow2 = [
            'Minimalist facial cleanser bottle surrounded by fresh green leaves and water splashes',
            'cleanser_leaves.png',
            'Minimalist Skincare Cleanser Shot',
            'skincare, cleanser, cosmetics, nature, water splash, minimalist',
            '',
            '',
            '',
            '',
            ''
        ];

        $callback = function () use ($columns, $sampleRow1, $sampleRow2) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sampleRow1);
            fputcsv($file, $sampleRow2);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export failed rows as a CSV file.
     */
    public function downloadFailedRows(Request $request)
    {
        $failedRows = json_decode($request->input('failed_rows_json', '[]'), true);

        if (empty($failedRows)) {
            return redirect()->back();
        }

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="failed_bulk_rows.csv"',
        ];

        $columns = [
            'prompt',
            'file_name',
            'title',
            'keywords',
            'slug',
            'description',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'error_reason'
        ];

        $callback = function () use ($columns, $failedRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($failedRows as $row) {
                fputcsv($file, [
                    $row['prompt'] ?? '',
                    $row['file_name'] ?? '',
                    $row['title'] ?? '',
                    $row['keywords'] ?? '',
                    $row['slug'] ?? '',
                    $row['description'] ?? '',
                    $row['meta_title'] ?? '',
                    $row['meta_description'] ?? '',
                    $row['meta_keywords'] ?? '',
                    $row['error_reason'] ?? '',
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
