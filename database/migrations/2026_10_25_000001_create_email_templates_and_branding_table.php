<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create email_templates table
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 64)->unique();
                $table->string('category', 32)->index(); // authentication, security, billing, notifications
                $table->string('name', 128);
                $table->text('description')->nullable();
                $table->string('subject', 255);
                $table->string('sender_name', 128)->nullable();
                $table->string('sender_email', 128)->nullable();
                $table->string('reply_to', 128)->nullable();
                $table->longText('content_html');
                $table->text('content_plain')->nullable();
                $table->json('variables')->nullable();
                $table->boolean('is_enabled')->default(true);
                $table->boolean('is_system')->default(true);
                $table->timestamps();
            });
        }

        // 2. Add email branding columns to admin_settings
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'email_logo')) {
                $table->string('email_logo', 255)->nullable()->after('email_admin');
            }
            if (!Schema::hasColumn('admin_settings', 'email_primary_color')) {
                $table->string('email_primary_color', 32)->default('#4f46e5')->after('email_logo');
            }
            if (!Schema::hasColumn('admin_settings', 'email_button_color')) {
                $table->string('email_button_color', 32)->default('#0f172a')->after('email_primary_color');
            }
            if (!Schema::hasColumn('admin_settings', 'email_footer_text')) {
                $table->text('email_footer_text')->nullable()->after('email_button_color');
            }
            if (!Schema::hasColumn('admin_settings', 'email_sender_name_auth')) {
                $table->string('email_sender_name_auth', 128)->default('Imagine Buddy Security')->after('email_footer_text');
            }
            if (!Schema::hasColumn('admin_settings', 'email_sender_address_auth')) {
                $table->string('email_sender_address_auth', 128)->default('noreply@auth.imaginebuddy.com')->after('email_sender_name_auth');
            }
        });

        // 3. Seed Default Templates with Clean Light-Theme Layouts
        $now = now();
        $templates = [
            [
                'slug' => 'two-factor-code',
                'category' => 'security',
                'name' => 'Two-Factor Authentication (2FA OTP)',
                'description' => 'Dispatches a 6-digit one-time security login code when 2FA-enabled users sign in.',
                'subject' => 'Your Imagine Buddy Security Code: {code}',
                'sender_name' => 'Imagine Buddy Security',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>Your One-Time Security Code</h2>
<p>Hi {name},</p>
<p>We received a sign-in attempt for your Imagine Buddy account. Use the following 6-digit verification code to complete your login:</p>
<div style="background-color: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; margin: 24px 0;">
    <span style="font-family: \'Courier New\', Courier, monospace; font-size: 32px; font-weight: 700; letter-spacing: 6px; color: #0f172a;">{code}</span>
</div>
<p style="color: #64748b; font-size: 13px;">This security code expires in <strong>{minutes} minutes</strong> and can only be used once. If you did not attempt to log in, please secure your account immediately or contact support.</p>',
                'content_plain' => "Hi {name},\n\nYour Imagine Buddy verification code is: {code}\n\nThis code expires in {minutes} minutes.\nIf you did not request this, please secure your account.",
                'variables' => json_encode(['{name}', '{code}', '{minutes}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'verify-account',
                'category' => 'authentication',
                'name' => 'Account Email Verification',
                'description' => 'Sent to newly registered users who need to verify their email address before accessing the studio.',
                'subject' => 'Verify your email address - {app_name}',
                'sender_name' => 'Imagine Buddy',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>Welcome to {app_name}! 👋</h2>
<p>Hi {name},</p>
<p>Thank you for joining Imagine Buddy! To activate your account and start generating commercial AI photoshoots and prompts, please confirm your email address by clicking the button below:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Verify Email Address</a>
</div>
<p style="color: #64748b; font-size: 13px;">If the button above does not work, copy and paste this link into your browser:<br><a href="{action_url}" style="color: {primary_color}; word-break: break-all;">{action_url}</a></p>',
                'content_plain' => "Welcome to {app_name}!\n\nHi {name},\n\nPlease verify your email address by visiting this link:\n{action_url}\n\nThank you!",
                'variables' => json_encode(['{name}', '{action_url}', '{app_name}', '{support_email}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'password-reset',
                'category' => 'authentication',
                'name' => 'Password Reset Request',
                'description' => 'Sent when a user requests a password reset link from the login page.',
                'subject' => 'Reset Your Password - {app_name}',
                'sender_name' => 'Imagine Buddy Security',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>Reset Your Password</h2>
<p>Hi {name},</p>
<p>We received a request to reset the password for your {app_name} account. Click the button below to choose a new password:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Reset Password</a>
</div>
<p style="color: #64748b; font-size: 13px;">This password reset link will expire in <strong>{expire_minutes} minutes</strong>. If you did not request a password reset, no further action is required and your account remains safe.</p>
<p style="color: #94a3b8; font-size: 12px;">Button not working? Copy and paste this URL:<br><a href="{action_url}" style="color: {primary_color}; word-break: break-all;">{action_url}</a></p>',
                'content_plain' => "Reset Your Password\n\nHi {name},\n\nClick the link below to reset your password:\n{action_url}\n\nThis link will expire in {expire_minutes} minutes.\nIf you did not request this, you can safely ignore this email.",
                'variables' => json_encode(['{name}', '{action_url}', '{expire_minutes}', '{app_name}', '{ip_address}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'subscription-expiring',
                'category' => 'billing',
                'name' => 'Subscription Expiring (3-Day Alert)',
                'description' => 'Warns active subscribers 72 to 48 hours before their plan expires so they can renew.',
                'subject' => 'Notice: Your {app_name} subscription expires in 3 days',
                'sender_name' => 'Imagine Buddy',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>Your Subscription is Expiring Soon</h2>
<p>Hi {name},</p>
<p>This is a friendly reminder that your <strong>{plan_name}</strong> subscription on {app_name} is set to expire on <strong>{expires_at}</strong>.</p>
<p>To ensure uninterrupted access to unlimited prompt generations, commercial photoshoots, and your downloaded assets, please renew or update your subscription before your term ends:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Renew Subscription &rarr;</a>
</div>
<p style="color: #64748b; font-size: 13px;">If you have any questions or need assistance, feel free to reply directly to our support team.</p>',
                'content_plain' => "Your Subscription is Expiring Soon\n\nHi {name},\n\nYour {plan_name} subscription expires on {expires_at}.\n\nRenew your plan here:\n{action_url}",
                'variables' => json_encode(['{name}', '{plan_name}', '{expires_at}', '{action_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'payment-receipt',
                'category' => 'billing',
                'name' => 'Payment & Deposit Receipt',
                'description' => 'Dispatched when a customer successfully completes a wallet deposit or subscription checkout.',
                'subject' => 'Payment Receipt for your transaction #{transaction_id} - {app_name}',
                'sender_name' => 'Imagine Buddy Billing',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>Payment Confirmed! 🎉</h2>
<p>Hi {name},</p>
<p>Thank you for your business! Your payment has been successfully processed. Here is your receipt summary:</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Transaction ID:</td>
            <td style="padding: 6px 0; font-weight: 600; color: #0f172a; text-align: right;">#{transaction_id}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Amount Paid:</td>
            <td style="padding: 6px 0; font-weight: 700; color: #059669; text-align: right;">{amount}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Payment Method:</td>
            <td style="padding: 6px 0; color: #0f172a; text-align: right;">{payment_gateway}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Date:</td>
            <td style="padding: 6px 0; color: #0f172a; text-align: right;">{date}</td>
        </tr>
    </table>
</div>
<div style="text-align: center; margin: 24px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">View In My Account</a>
</div>',
                'content_plain' => "Payment Confirmed!\n\nHi {name},\n\nTransaction ID: #{transaction_id}\nAmount Paid: {amount}\nPayment Method: {payment_gateway}\nDate: {date}\n\nView details: {action_url}",
                'variables' => json_encode(['{name}', '{transaction_id}', '{amount}', '{payment_gateway}', '{date}', '{action_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'admin-deposit-pending',
                'category' => 'notifications',
                'name' => 'Admin Alert: Pending Deposit Verification',
                'description' => 'Sent to site administrators when a user submits a manual bank wire or deposit proof.',
                'subject' => 'Action Required: New Deposit Pending Verification (#{transaction_id})',
                'sender_name' => 'Imagine Buddy System',
                'sender_email' => 'noreply@auth.imaginebuddy.com',
                'reply_to' => null,
                'content_html' => '<h2>New Deposit Awaiting Verification ⚠️</h2>
<p>Hello Admin,</p>
<p>A customer has submitted a new manual deposit that requires verification and approval:</p>
<div style="background-color: #fefce8; border: 1px solid #fef08a; border-radius: 8px; padding: 18px; margin: 20px 0;">
    <p style="margin: 4px 0; font-size: 14px;"><strong>Customer:</strong> {user_name} ({user_email})</p>
    <p style="margin: 4px 0; font-size: 14px;"><strong>Amount:</strong> {amount}</p>
    <p style="margin: 4px 0; font-size: 14px;"><strong>Transaction ID:</strong> #{transaction_id}</p>
</div>
<div style="text-align: center; margin: 24px 0;">
    <a href="{admin_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">Open Admin Deposits &rarr;</a>
</div>',
                'content_plain' => "New Deposit Awaiting Verification\n\nCustomer: {user_name} ({user_email})\nAmount: {amount}\nTransaction ID: #{transaction_id}\n\nReview in Admin Panel: {admin_url}",
                'variables' => json_encode(['{user_name}', '{user_email}', '{amount}', '{transaction_id}', '{admin_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($templates as $t) {
            DB::table('email_templates')->updateOrInsert(['slug' => $t['slug']], $t);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = ['email_logo', 'email_primary_color', 'email_button_color', 'email_footer_text', 'email_sender_name_auth', 'email_sender_address_auth'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
