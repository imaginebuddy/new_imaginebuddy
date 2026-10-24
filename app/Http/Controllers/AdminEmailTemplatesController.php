<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminEmailTemplatesController extends Controller
{
    /**
     * Display a listing of email templates grouped by category + branding tab.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'templates');
        $category = $request->input('category', 'all');
        $q = trim($request->input('q', ''));

        $query = EmailTemplate::query();

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('subject', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        $templates = $query->orderBy('category')->orderBy('name')->get();

        $counts = [
            'all' => EmailTemplate::count(),
            'authentication' => EmailTemplate::where('category', 'authentication')->count(),
            'security' => EmailTemplate::where('category', 'security')->count(),
            'billing' => EmailTemplate::where('category', 'billing')->count(),
            'notifications' => EmailTemplate::where('category', 'notifications')->count(),
        ];

        $settings = AdminSettings::first();

        return view('admin.email-templates.index', [
            'tab' => $tab,
            'category' => $category,
            'q' => $q,
            'templates' => $templates,
            'counts' => $counts,
            'settings' => $settings,
        ]);
    }

    /**
     * Show the editor for a specific template.
     */
    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $settings = AdminSettings::first();
        $sampleVariables = $template->getSampleVariables();
        $requiredVars = EmailTemplate::requiredVariables()[$template->slug] ?? [];

        return view('admin.email-templates.edit', [
            'template' => $template,
            'settings' => $settings,
            'sampleVariables' => $sampleVariables,
            'requiredVars' => $requiredVars,
        ]);
    }

    /**
     * Update the template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'content_html' => 'required|string',
            'sender_name' => 'nullable|string|max:128',
            'sender_email' => 'nullable|email:filter|max:128',
            'reply_to' => 'nullable|email:filter|max:128',
            'content_plain' => 'nullable|string',
        ]);

        $contentHtml = $request->input('content_html');

        // Safety check: ensure required tokens are present so auth emails are never broken
        $requiredVars = EmailTemplate::requiredVariables()[$template->slug] ?? [];
        foreach ($requiredVars as $token) {
            if (!str_contains($contentHtml, $token)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error_message', "The required dynamic variable '{$token}' is missing from the email content. It must be included for this template to function safely.");
            }
        }

        $template->subject = $request->input('subject');
        $template->content_html = $contentHtml;
        $template->content_plain = $request->input('content_plain') ?: strip_tags($contentHtml);
        $template->sender_name = $request->input('sender_name');
        $template->sender_email = $request->input('sender_email');
        $template->reply_to = $request->input('reply_to');

        // Critical system templates (2FA, reset, verify) cannot be disabled
        if ($template->is_system && in_array($template->slug, ['two-factor-code', 'verify-account', 'password-reset'])) {
            $template->is_enabled = true;
        } else {
            $template->is_enabled = $request->has('is_enabled');
        }

        $template->save();

        return redirect()->route('admin.email_templates')
            ->with('success_message', "Template '{$template->name}' updated successfully!");
    }

    /**
     * Update global email branding and sender defaults.
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'email_primary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_button_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_sender_name_auth' => 'required|string|max:128',
            'email_sender_address_auth' => 'required|email:filter|max:128',
            'email_footer_text' => 'nullable|string|max:2000',
            'email_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $settings = AdminSettings::first();

        // Handle logo upload
        if ($request->hasFile('email_logo')) {
            $file = $request->file('email_logo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'email_logo_' . time() . '.' . $extension;
            $file->move(public_path('img'), $filename);
            $settings->email_logo = 'public/img/' . $filename;
        }

        $settings->email_primary_color = $request->filled('email_primary_color') ? trim($request->input('email_primary_color')) : '#4f46e5';
        $settings->email_button_color = $request->filled('email_button_color') ? trim($request->input('email_button_color')) : '#0f172a';
        $settings->email_sender_name_auth = $request->input('email_sender_name_auth', 'Imagine Buddy Security');
        $settings->email_sender_address_auth = $request->input('email_sender_address_auth', 'noreply@auth.imaginebuddy.com');
        $settings->email_footer_text = $request->input('email_footer_text');
        $settings->save();

        return redirect()->route('admin.email_templates', ['tab' => 'branding'])
            ->with('success_message', 'Email branding & sender defaults updated successfully!');
    }

    /**
     * Live preview endpoint for iframe or modal.
     */
    public function preview($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $html = EmailTemplateService::preview($template);

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Send a real test email through Amazon SES (ses-transactional).
     */
    public function sendTest(Request $request, $id)
    {
        $request->validate([
            'test_email' => 'required|email:filter|max:255',
        ]);

        $template = EmailTemplate::findOrFail($id);
        $testEmail = strtolower(trim($request->input('test_email')));

        $rendered = EmailTemplateService::render($template->slug, [
            '{name}' => 'Admin Tester',
        ]);

        try {
            Mail::mailer('ses-transactional')->send('emails.raw', ['content' => $rendered['html']], function ($message) use ($rendered, $testEmail) {
                $message->from($rendered['sender_email'], $rendered['sender_name'])
                    ->to($testEmail)
                    ->subject('[TEST PREVIEW] ' . $rendered['subject']);

                if (!empty($rendered['reply_to'])) {
                    $message->replyTo($rendered['reply_to']);
                }
            });

            return redirect()->back()
                ->with('success_message', "Real test preview for '{$template->name}' dispatched to {$testEmail} via Amazon SES (auth.imaginebuddy.com)!");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error_message', "Failed to send test email: " . $e->getMessage());
        }
    }
}
