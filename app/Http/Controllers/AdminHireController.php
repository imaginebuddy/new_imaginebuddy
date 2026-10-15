<?php

namespace App\Http\Controllers;

use App\Models\HireSetting;
use App\Models\HireInquiry;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class AdminHireController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkPermission()
    {
        if (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('hire_service') || auth()->user()->hasPermission('full_access')) {
            return;
        }
        abort(403, 'Unauthorized access.');
    }

    /**
     * Show Hire Settings Form
     */
    public function settings()
    {
        $this->checkPermission();
        $settings = HireSetting::first();
        if (!$settings) {
            $settings = HireSetting::create([
                'status' => 'on',
                'hero_eyebrow' => 'CUSTOM AI PRODUCT PHOTOSHOOTS',
                'hero_headline' => 'Your Product. Your Vision. A Campaign Built Around Your Brand.',
                'hero_subheadline' => 'Need product visuals that go beyond standard catalog photography? Hire us to create custom AI-assisted product photoshoots tailored to your brand, creative direction, and campaign goals.',
                'hero_badge_text' => 'Custom creative direction · AI-assisted production · Human refinement',
                'price_1_sku_usd' => 699.00,
                'price_3_5_sku_usd' => 499.00,
                'price_6_plus_sku_usd' => 349.00,
                'price_1_sku_inr' => 54999.00,
                'price_3_5_sku_inr' => 39999.00,
                'price_6_plus_sku_inr' => 27999.00,
                'custom_quote_text' => 'Custom quote based on your brief, creative direction, deliverables, and project scope.',
                'service_intro_title' => 'More Than Product Images. A Creative Direction Built for Your Brand.',
                'service_intro_subtitle' => 'Every product and brand has a different story to tell. Our custom AI product photoshoots are designed for brands that need a specific visual direction, a distinctive campaign concept, or a more tailored approach to product photography.',
                'meta_title' => 'Hire Us | Custom AI Product Photoshoots',
                'meta_description' => 'Request a custom AI-assisted product photoshoot tailored to your brand, creative direction, and campaign goals. Share your project brief to discuss scope and pricing.',
            ]);
        }

        return view('admin.hire-settings', compact('settings'));
    }

    /**
     * Update Hire Settings
     */
    public function updateSettings(Request $request)
    {
        $this->checkPermission();

        $rules = [
            'status' => 'required|in:on,off',
            'hero_eyebrow' => 'required|max:191',
            'hero_headline' => 'required|max:255',
            'hero_subheadline' => 'nullable|max:1000',
            'hero_badge_text' => 'nullable|max:255',
            'price_1_sku_usd' => 'required|numeric|min:0',
            'price_3_5_sku_usd' => 'required|numeric|min:0',
            'price_6_plus_sku_usd' => 'required|numeric|min:0',
            'price_1_sku_inr' => 'required|numeric|min:0',
            'price_3_5_sku_inr' => 'required|numeric|min:0',
            'price_6_plus_sku_inr' => 'required|numeric|min:0',
            'custom_quote_text' => 'nullable|max:500',
            'service_intro_title' => 'required|max:255',
            'service_intro_subtitle' => 'nullable|max:1000',
            'notification_email' => 'nullable|email|max:191',
            'meta_title' => 'required|max:191',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:500',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = HireSetting::firstOrFail();

        // Handle arrays from textarea / line breaks
        $parseLines = function ($input) {
            if (is_array($input)) return array_values(array_filter($input));
            if (!empty($input)) {
                $lines = preg_split('/\r\n|\r|\n/', $input);
                $filtered = array_filter(array_map('trim', $lines));
                return array_values($filtered);
            }
            return [];
        };

        $data = [
            'status' => $request->status,
            'hero_eyebrow' => $request->hero_eyebrow,
            'hero_headline' => $request->hero_headline,
            'hero_subheadline' => $request->hero_subheadline,
            'hero_badge_text' => $request->hero_badge_text,
            'price_1_sku_usd' => $request->price_1_sku_usd,
            'price_3_5_sku_usd' => $request->price_3_5_sku_usd,
            'price_6_plus_sku_usd' => $request->price_6_plus_sku_usd,
            'price_1_sku_inr' => $request->price_1_sku_inr,
            'price_3_5_sku_inr' => $request->price_3_5_sku_inr,
            'price_6_plus_sku_inr' => $request->price_6_plus_sku_inr,
            'custom_quote_text' => $request->custom_quote_text,
            'service_intro_title' => $request->service_intro_title,
            'service_intro_subtitle' => $request->service_intro_subtitle,
            'notification_email' => $request->notification_email,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        if ($request->has('form_categories')) {
            $data['form_categories'] = $parseLines($request->form_categories);
        }
        if ($request->has('form_shoot_types')) {
            $data['form_shoot_types'] = $parseLines($request->form_shoot_types);
        }
        if ($request->has('form_intended_uses')) {
            $data['form_intended_uses'] = $parseLines($request->form_intended_uses);
        }
        if ($request->has('form_budgets_usd')) {
            $data['form_budgets_usd'] = $parseLines($request->form_budgets_usd);
        }
        if ($request->has('form_budgets_inr')) {
            $data['form_budgets_inr'] = $parseLines($request->form_budgets_inr);
        }

        // Hero image upload if provided
        if ($request->hasFile('hero_image')) {
            $image = $request->file('hero_image');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'hire_hero_' . time() . '.' . $extension;
            $destinationPath = public_path('img');
            $image->move($destinationPath, $imageName);
            $data['hero_image'] = $imageName;
        }

        $settings->update($data);
        cache()->forget('hire_settings_data');

        return back()->with('success_message', 'Hire page settings updated successfully.');
    }

    /**
     * Show Inquiries / Leads List
     */
    public function inquiries(Request $request)
    {
        $this->checkPermission();

        $query = HireInquiry::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sq) use ($q) {
                $sq->where('full_name', 'LIKE', "%{$q}%")
                   ->orWhere('email', 'LIKE', "%{$q}%")
                   ->orWhere('company_name', 'LIKE', "%{$q}%")
                   ->orWhere('product_category', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $inquiries = $query->orderBy('id', 'desc')->paginate(20);

        // Counts for status chips
        $counts = [
            'total' => HireInquiry::count(),
            'new' => HireInquiry::where('status', 'new')->count(),
            'in_review' => HireInquiry::where('status', 'in_review')->count(),
            'contacted' => HireInquiry::where('status', 'contacted')->count(),
            'quoted' => HireInquiry::where('status', 'quoted')->count(),
            'converted' => HireInquiry::where('status', 'converted')->count(),
            'archived' => HireInquiry::where('status', 'archived')->count(),
        ];

        return view('admin.hire-inquiries', compact('inquiries', 'counts'));
    }

    /**
     * Get single inquiry details (returns JSON for modal)
     */
    public function showInquiry($id)
    {
        $this->checkPermission();
        $inquiry = HireInquiry::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $inquiry
        ]);
    }

    /**
     * Update status and notes of inquiry
     */
    public function updateInquiryStatus(Request $request, $id)
    {
        $this->checkPermission();

        $request->validate([
            'status' => 'required|in:new,in_review,contacted,quoted,converted,archived',
            'admin_notes' => 'nullable|string|max:2000'
        ]);

        $inquiry = HireInquiry::findOrFail($id);
        $inquiry->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Inquiry status updated successfully.'
            ]);
        }

        return back()->with('success_message', 'Inquiry updated successfully.');
    }

    /**
     * Delete Inquiry
     */
    public function deleteInquiry($id)
    {
        $this->checkPermission();
        $inquiry = HireInquiry::findOrFail($id);
        $inquiry->delete();

        return back()->with('success_message', 'Inquiry deleted successfully.');
    }

    /**
     * Export inquiries to CSV
     */
    public function exportCsv(Request $request)
    {
        $this->checkPermission();

        $query = HireInquiry::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $list = $query->orderBy('id', 'desc')->get();

        $filename = "hire_inquiries_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Date', 'Full Name', 'Email', 'Company', 'Website',
            'Product Category', 'SKU Count', 'Shoot Types', 'Intended Use',
            'Preferred Direction', 'Budget', 'Currency', 'Desired Delivery Date',
            'Project Description', 'Reference Links', 'Additional Notes',
            'Country', 'IP Address', 'Status', 'Admin Notes'
        ];

        $callback = function () use ($list, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($list as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->created_at->format('Y-m-d H:i'),
                    $item->full_name,
                    $item->email,
                    $item->company_name,
                    $item->website_url,
                    $item->product_category,
                    $item->sku_count,
                    is_array($item->photoshoot_types) ? implode(', ', $item->photoshoot_types) : $item->photoshoot_types,
                    is_array($item->intended_use) ? implode(', ', $item->intended_use) : $item->intended_use,
                    $item->preferred_direction,
                    $item->budget_range,
                    $item->currency,
                    $item->desired_delivery_date ? $item->desired_delivery_date->format('Y-m-d') : '',
                    $item->project_description,
                    $item->reference_links,
                    $item->additional_notes,
                    $item->country_code,
                    $item->ip_address,
                    $item->status,
                    $item->admin_notes
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
