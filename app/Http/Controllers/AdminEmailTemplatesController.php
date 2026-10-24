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
     * Display a listing of email templates grouped by channel & category + branding tab.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'templates');
        $channel = $request->input('channel', 'all');
        $category = $request->input('category', 'all');
        $q = trim($request->input('q', ''));

        $query = EmailTemplate::query();

        if ($channel !== 'all') {
            $query->where('channel', $channel);
        }

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

        $templates = $query->orderBy('channel')->orderBy('category')->orderBy('name')->get();

        $counts = [
            'all' => EmailTemplate::count(),
            'auth' => EmailTemplate::where('channel', 'auth')->count(),
            'updates' => EmailTemplate::where('channel', 'updates')->count(),
            'authentication' => EmailTemplate::where('category', 'authentication')->count(),
            'security' => EmailTemplate::where('category', 'security')->count(),
            'billing' => EmailTemplate::where('category', 'billing')->count(),
            'newsletters' => EmailTemplate::where('category', 'newsletters')->count(),
            'marketing' => EmailTemplate::where('category', 'marketing')->count(),
            'lifecycle' => EmailTemplate::where('category', 'lifecycle')->count(),
            'notifications' => EmailTemplate::where('category', 'notifications')->count(),
        ];

        $settings = AdminSettings::first();

        return view('admin.email-templates.index', [
            'tab' => $tab,
            'channel' => $channel,
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
     * Update global email branding and sender defaults for both channels.
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            // Auth Channel
            'email_primary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_button_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_sender_name_auth' => 'nullable|string|max:128',
            'email_sender_address_auth' => 'nullable|email:filter|max:128',
            'email_footer_text' => 'nullable|string|max:2000',
            'email_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            // Updates Channel
            'email_updates_primary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_updates_button_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'email_sender_name_updates' => 'nullable|string|max:128',
            'email_sender_address_updates' => 'nullable|email:filter|max:128',
            'email_reply_to_updates' => 'nullable|email:filter|max:128',
            'email_updates_footer_text' => 'nullable|string|max:2000',
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

        // Auth Channel Defaults
        if ($request->filled('email_primary_color')) {
            $settings->email_primary_color = trim($request->input('email_primary_color'));
        }
        if ($request->filled('email_button_color')) {
            $settings->email_button_color = trim($request->input('email_button_color'));
        }
        if ($request->filled('email_sender_name_auth')) {
            $settings->email_sender_name_auth = trim($request->input('email_sender_name_auth'));
        }
        if ($request->filled('email_sender_address_auth')) {
            $settings->email_sender_address_auth = trim($request->input('email_sender_address_auth'));
        }
        $settings->email_footer_text = $request->input('email_footer_text');

        // Updates Channel Defaults
        if ($request->filled('email_updates_primary_color')) {
            $settings->email_updates_primary_color = trim($request->input('email_updates_primary_color'));
        }
        if ($request->filled('email_updates_button_color')) {
            $settings->email_updates_button_color = trim($request->input('email_updates_button_color'));
        }
        if ($request->filled('email_sender_name_updates')) {
            $settings->email_sender_name_updates = trim($request->input('email_sender_name_updates'));
        }
        if ($request->filled('email_sender_address_updates')) {
            $settings->email_sender_address_updates = trim($request->input('email_sender_address_updates'));
        }
        if ($request->filled('email_reply_to_updates')) {
            $settings->email_reply_to_updates = trim($request->input('email_reply_to_updates'));
        }
        $settings->email_updates_footer_text = $request->input('email_updates_footer_text');

        $settings->save();

        return redirect()->route('admin.email_templates', ['tab' => 'branding'])
            ->with('success_message', 'Email branding & sender defaults updated successfully for both channels!');
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
     * Send a real test email through Amazon SES (ses-transactional or ses-marketing).
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

        $mailer = $rendered['mailer'] ?? (($template->channel ?? 'auth') === 'updates' ? 'ses-marketing' : 'ses-transactional');

        try {
            Mail::mailer($mailer)->send('emails.raw', ['content' => $rendered['html']], function ($message) use ($rendered, $testEmail) {
                $message->from($rendered['sender_email'], $rendered['sender_name'])
                    ->to($testEmail)
                    ->subject('[TEST PREVIEW] ' . $rendered['subject']);

                if (!empty($rendered['reply_to'])) {
                    $message->replyTo($rendered['reply_to']);
                }

                if (!empty($rendered['unsubscribe_url'])) {
                    $message->getHeaders()
                        ->addTextHeader('List-Unsubscribe', '<' . $rendered['unsubscribe_url'] . '>')
                        ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                }
            });

            return redirect()->back()
                ->with('success_message', "Real test preview for '{$template->name}' dispatched to {$testEmail} via Amazon SES ({$rendered['sender_email']})!");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error_message', "Failed to send test email: " . $e->getMessage());
        }
    }
}
