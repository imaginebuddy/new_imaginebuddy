<?php

namespace App\Http\Controllers;

use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminHomepageContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user has permission to manage homepage content.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (
            $user->isSuperAdmin()
            || $user->hasPermission('page_manager')
            || $user->hasPermission('general_settings')
            || $user->hasPermission('full_access')
        ) {
            return;
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Show the homepage content settings form.
     */
    public function edit()
    {
        $this->checkPermission();

        $settings = HomepageSetting::getSettings();

        return view('admin.homepage-content', compact('settings'));
    }

    /**
     * Update homepage content settings.
     */
    public function update(Request $request)
    {
        $this->checkPermission();

        $rules = [
            'hero_title' => 'nullable|string|max:500',
            'hero_subtitle' => 'nullable|string|max:1000',
            'search_placeholder' => 'nullable|string|max:255',

            'diff_eyebrow' => 'nullable|string|max:255',
            'diff_heading' => 'nullable|string|max:500',
            'diff_description_1' => 'nullable|string|max:1000',
            'diff_description_2' => 'nullable|string|max:1000',
            'diff_card_generic_title' => 'nullable|string|max:255',
            'diff_card_adaptive_title' => 'nullable|string|max:255',
            'diff_cta_text' => 'nullable|string|max:255',
            'diff_cta_url' => 'nullable|string|max:255',
            'diff_disclaimer' => 'nullable|string|max:1000',

            'photoshoots_heading' => 'nullable|string|max:255',
            'photoshoots_subheading' => 'nullable|string|max:1000',
            'photoshoots_cta_text' => 'nullable|string|max:255',
            'photoshoots_cta_url' => 'nullable|string|max:255',

            'how_it_works_eyebrow' => 'nullable|string|max:255',
            'how_it_works_heading' => 'nullable|string|max:500',
            'how_it_works_cta_text' => 'nullable|string|max:255',
            'how_it_works_cta_url' => 'nullable|string|max:255',
            'how_it_works_subtext' => 'nullable|string|max:500',

            'who_is_it_for_heading' => 'nullable|string|max:255',
            'who_is_it_for_subheading' => 'nullable|string|max:1000',
            'who_is_it_for_cta_text' => 'nullable|string|max:255',
            'who_is_it_for_cta_url' => 'nullable|string|max:255',

            'free_pro_eyebrow' => 'nullable|string|max:255',
            'free_pro_heading' => 'nullable|string|max:500',
            'free_pro_subheading' => 'nullable|string|max:1000',
            'free_card_title' => 'nullable|string|max:255',
            'free_card_desc' => 'nullable|string|max:1000',
            'free_card_cta_text' => 'nullable|string|max:255',
            'free_card_cta_url' => 'nullable|string|max:255',
            'pro_card_title' => 'nullable|string|max:255',
            'pro_card_desc' => 'nullable|string|max:1000',
            'pro_card_cta_text' => 'nullable|string|max:255',
            'pro_card_cta_url' => 'nullable|string|max:255',
            'free_pro_footnote' => 'nullable|string|max:500',

            'promo_eyebrow' => 'nullable|string|max:255',
            'promo_heading' => 'nullable|string|max:500',
            'promo_subheading' => 'nullable|string|max:1000',
            'promo_note' => 'nullable|string|max:1000',
            'promo_cta_text' => 'nullable|string|max:255',
            'promo_cta_url' => 'nullable|string|max:255',

            'transformation_eyebrow' => 'nullable|string|max:255',
            'transformation_heading' => 'nullable|string|max:500',
            'transformation_subheading' => 'nullable|string|max:1000',
            'transformation_before_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'transformation_after_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'transformation_before_image' => 'nullable|string|max:500',
            'transformation_after_image' => 'nullable|string|max:500',
            'transformation_before_label' => 'nullable|string|max:255',
            'transformation_after_label' => 'nullable|string|max:255',
            'transformation_right_badge' => 'nullable|string|max:255',
            'transformation_right_badge_sub' => 'nullable|string|max:255',
            'transformation_right_headline' => 'nullable|string|max:500',
            'transformation_right_description' => 'nullable|string|max:1000',
            'transformation_traditional_price_inr' => 'nullable|string|max:255',
            'transformation_traditional_price_usd' => 'nullable|string|max:255',
            'transformation_library_price_inr' => 'nullable|string|max:255',
            'transformation_library_price_usd' => 'nullable|string|max:255',
            'transformation_cta_title' => 'nullable|string|max:255',
            'transformation_cta_subtitle' => 'nullable|string|max:1000',
            'transformation_cta_btn_text' => 'nullable|string|max:255',
            'transformation_cta_btn_url' => 'nullable|string|max:255',

            'faq_badge' => 'nullable|string|max:255',
            'faq_title' => 'nullable|string|max:255',
            'faq_subtitle' => 'nullable|string|max:1000',
            'faq_contact_text' => 'nullable|string|max:255',
            'faq_contact_url' => 'nullable|string|max:255',
            'faq_view_all_text' => 'nullable|string|max:255',
            'faq_view_all_url' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = HomepageSetting::getSettings();
        $data = $request->except([
            '_token',
            'transformation_before_image_file',
            'transformation_after_image_file',
        ]);

        // Visibility Toggles
        $data['diff_enabled'] = $request->boolean('diff_enabled');
        $data['photoshoots_enabled'] = $request->boolean('photoshoots_enabled');
        $data['how_it_works_enabled'] = $request->boolean('how_it_works_enabled');
        $data['who_is_it_for_enabled'] = $request->boolean('who_is_it_for_enabled');
        $data['free_vs_pro_enabled'] = $request->boolean('free_vs_pro_enabled');
        $data['promo_section_enabled'] = $request->boolean('promo_section_enabled');
        $data['transformation_enabled'] = $request->boolean('transformation_enabled');
        $data['faq_enabled'] = $request->boolean('faq_enabled');

        // Handle File Uploads for Transformation images
        if ($request->hasFile('transformation_before_image_file')) {
            $file = $request->file('transformation_before_image_file');
            $filename = 'home_before_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            $data['transformation_before_image'] = 'public/img/' . $filename;
        }

        if ($request->hasFile('transformation_after_image_file')) {
            $file = $request->file('transformation_after_image_file');
            $filename = 'home_after_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            $data['transformation_after_image'] = 'public/img/' . $filename;
        }

        // Clean & process JSON arrays
        // 1. Generic bullet points
        if ($request->has('diff_card_generic_items')) {
            $items = is_array($request->diff_card_generic_items) ? $request->diff_card_generic_items : explode("\n", (string) $request->diff_card_generic_items);
            $data['diff_card_generic_items'] = array_values(array_filter(array_map('trim', $items)));
        }

        // 2. Adaptive bullet points
        if ($request->has('diff_card_adaptive_items')) {
            $items = is_array($request->diff_card_adaptive_items) ? $request->diff_card_adaptive_items : explode("\n", (string) $request->diff_card_adaptive_items);
            $data['diff_card_adaptive_items'] = array_values(array_filter(array_map('trim', $items)));
        }

        // 3. How It Works steps
        if ($request->has('how_it_works_steps') && is_array($request->how_it_works_steps)) {
            $cleanSteps = [];
            foreach ($request->how_it_works_steps as $step) {
                if (!empty($step['title']) || !empty($step['desc'])) {
                    $cleanSteps[] = [
                        'badge' => trim($step['badge'] ?? ''),
                        'icon' => trim($step['icon'] ?? 'bi-stars'),
                        'title' => trim($step['title'] ?? ''),
                        'desc' => trim($step['desc'] ?? ''),
                    ];
                }
            }
            $data['how_it_works_steps'] = $cleanSteps;
        }

        // 4. Who is it for personas
        if ($request->has('who_is_it_for_items') && is_array($request->who_is_it_for_items)) {
            $cleanPersonas = [];
            foreach ($request->who_is_it_for_items as $persona) {
                if (!empty($persona['title']) || !empty($persona['desc'])) {
                    $cleanPersonas[] = [
                        'title' => trim($persona['title'] ?? ''),
                        'desc' => trim($persona['desc'] ?? ''),
                    ];
                }
            }
            $data['who_is_it_for_items'] = $cleanPersonas;
        }

        // 5. Promo concept items
        if ($request->has('promo_items') && is_array($request->promo_items)) {
            $cleanPromo = [];
            foreach ($request->promo_items as $item) {
                if (!empty($item['title']) || !empty($item['desc'])) {
                    $cleanPromo[] = [
                        'title' => trim($item['title'] ?? ''),
                        'desc' => trim($item['desc'] ?? ''),
                    ];
                }
            }
            $data['promo_items'] = $cleanPromo;
        }

        // 6. Traditional items bullets
        if ($request->has('transformation_traditional_items')) {
            $items = is_array($request->transformation_traditional_items) ? $request->transformation_traditional_items : explode("\n", (string) $request->transformation_traditional_items);
            $data['transformation_traditional_items'] = array_values(array_filter(array_map('trim', $items)));
        }

        // 7. Library items bullets
        if ($request->has('transformation_library_items')) {
            $items = is_array($request->transformation_library_items) ? $request->transformation_library_items : explode("\n", (string) $request->transformation_library_items);
            $data['transformation_library_items'] = array_values(array_filter(array_map('trim', $items)));
        }

        // 8. FAQ items
        if ($request->has('faq_items') && is_array($request->faq_items)) {
            $cleanFaqs = [];
            foreach ($request->faq_items as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $cleanFaqs[] = [
                        'question' => trim($faq['question']),
                        'answer' => trim($faq['answer']),
                    ];
                }
            }
            $data['faq_items'] = $cleanFaqs;
        }

        $settings->update($data);

        return redirect('panel/admin/homepage-content')
            ->withSuccessMessage(__('admin.success_update'));
    }
}
