<?php

namespace App\Http\Controllers;

use App\Models\HireSetting;
use App\Models\HireInquiry;
use App\Models\Photoshoot;
use App\Models\Images;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HireController extends Controller
{
    /**
     * Display the Hire landing page.
     */
    public function index()
    {
        $hireSettings = HireSetting::getSettings();

        // Detect country and currency
        $isIndia = Helper::isIndia();
        $curr = Helper::currentCurrency(); // ['code' => 'INR'|'USD', 'symbol' => '₹'|'$', 'name' => ...]

        // Curated portfolio photoshoots with images and category
        $portfolioPhotoshoots = Photoshoot::with(['images' => function ($query) {
            $query->where('status', 'active')->orderBy('id', 'asc');
        }, 'category'])
        ->whereHas('images', function ($query) {
            $query->where('status', 'active');
        })
        ->orderBy('id', 'desc')
        ->limit(12)
        ->get();

        // Sample showcase images for portfolio grid & hero
        $showcaseImages = Images::where('status', 'active')
            ->whereNotNull('photoshoot_id')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();

        if ($showcaseImages->isEmpty()) {
            $showcaseImages = Images::where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(12)
                ->get();
        }

        $budgetsUsd = is_array($hireSettings->form_budgets_usd) && count($hireSettings->form_budgets_usd) > 0 ? $hireSettings->form_budgets_usd : [
            'Under $500', '$500–$1,000', '$1,000–$2,500', '$2,500–$5,000', '$5,000+', 'Not sure yet'
        ];
        $budgetsInr = is_array($hireSettings->form_budgets_inr) && count($hireSettings->form_budgets_inr) > 0 ? $hireSettings->form_budgets_inr : [
            'Under ₹40,000', '₹40,000–₹80,000', '₹80,000–₹2,00,000', '₹2,00,000–₹4,00,000', '₹4,00,000+', 'Not sure yet'
        ];

        return view('hire.index', compact('hireSettings', 'isIndia', 'curr', 'portfolioPhotoshoots', 'showcaseImages', 'budgetsUsd', 'budgetsInr'));
    }

    /**
     * Handle project inquiry submission.
     */
    public function storeInquiry(Request $request)
    {
        // Simple honeypot anti-spam check
        if ($request->filled('website_hp')) {
            return response()->json([
                'success' => false,
                'message' => 'Spam submission detected.'
            ], 422);
        }

        $rules = [
            'full_name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:150',
            'company_name' => 'required|string|min:2|max:150',
            'website_url' => 'nullable|url|max:255',
            'product_category' => 'required|string|max:100',
            'sku_count' => 'required|string|max:50',
            'photoshoot_types' => 'required|array|min:1',
            'photoshoot_types.*' => 'string|max:100',
            'intended_use' => 'nullable|array',
            'intended_use.*' => 'string|max:100',
            'preferred_direction' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'currency' => 'nullable|string|in:USD,INR',
            'desired_delivery_date' => 'nullable|date|after_or_equal:today',
            'project_description' => 'required|string|min:10|max:5000',
            'reference_links' => 'nullable|string|max:1000',
            'additional_notes' => 'nullable|string|max:2000',
        ];

        $messages = [
            'full_name.required' => 'Please provide your full name.',
            'email.required' => 'A valid work email is required.',
            'company_name.required' => 'Please enter your brand or company name.',
            'product_category.required' => 'Please select a product category.',
            'sku_count.required' => 'Please specify the number of products/SKUs.',
            'photoshoot_types.required' => 'Please select at least one photoshoot type.',
            'project_description.required' => 'Please describe your project and creative goals.',
            'project_description.min' => 'Please provide at least a few sentences describing your product and visual goals.',
            'desired_delivery_date.after_or_equal' => 'Desired delivery date must be today or in the future.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $userCountry = session('user_country') ?: Helper::resolveUserCountry();
        $currency = $request->input('currency') ?: (Helper::isIndia() ? 'INR' : 'USD');

        $inquiry = HireInquiry::create([
            'full_name' => strip_tags(trim($request->full_name)),
            'email' => trim(strtolower($request->email)),
            'company_name' => strip_tags(trim($request->company_name)),
            'website_url' => $request->website_url ? strip_tags(trim($request->website_url)) : null,
            'product_category' => strip_tags(trim($request->product_category)),
            'sku_count' => strip_tags(trim($request->sku_count)),
            'photoshoot_types' => $request->photoshoot_types,
            'intended_use' => $request->intended_use ?: [],
            'preferred_direction' => $request->preferred_direction ? strip_tags(trim($request->preferred_direction)) : null,
            'budget_range' => $request->budget_range ? strip_tags(trim($request->budget_range)) : null,
            'currency' => $currency,
            'desired_delivery_date' => $request->desired_delivery_date ?: null,
            'project_description' => strip_tags(trim($request->project_description)),
            'reference_links' => $request->reference_links ? strip_tags(trim($request->reference_links)) : null,
            'additional_notes' => $request->additional_notes ? strip_tags(trim($request->additional_notes)) : null,
            'country_code' => $userCountry,
            'ip_address' => $request->ip(),
            'status' => 'new'
        ]);

        // Attempt email notification to admin if mail is configured
        try {
            $settings = HireSetting::getSettings();
            $adminEmail = $settings->notification_email ?: config('settings.email_admin');

            if (!empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $emailData = [
                    'inquiry' => $inquiry,
                    'title_site' => config('settings.title', 'ImagineBuddy')
                ];

                Mail::send('emails.hire-inquiry', $emailData, function ($message) use ($adminEmail, $inquiry) {
                    $message->to($adminEmail)
                            ->from(config('mail.from.address'), config('mail.from.name', 'ImagineBuddy'))
                            ->replyTo($inquiry->email, $inquiry->full_name)
                            ->subject('New Custom Photoshoot Inquiry from ' . $inquiry->company_name . ' (' . $inquiry->full_name . ')');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Hire Inquiry notification email: ' . $e->getMessage());
        }

        $successMessage = 'Thanks for sharing your project. Your inquiry has been submitted successfully. We’ll review your brief and follow up using the contact information you provided.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'inquiry_id' => $inquiry->id
            ]);
        }

        return back()->with('success_message', $successMessage);
    }
}
