<?php

namespace App\Http\Controllers;

use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Inertia\Inertia;
use App\Mail\OwnerDeclinedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\OwnerApprovedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function index()
    {
        if (!Session::get('authenticated')) {
            return redirect()->route('loginAdmin');
        }

        $users = User::all();
        $owners = CarWashOwner::all();

        return Inertia::render('settings/AdminDashboard', [
            'users' => $users,
            'owners' => $owners,
        ]);
    }

    protected function sendSendGridEmail(string $toEmail, string $subject, string $htmlContent): bool
    {
        $apiKey = config('services.sendgrid.api_key') ?? env('SENDGRID_API_KEY');

        if (empty($apiKey)) {
            Log::warning('SendGrid API key not configured.');
            return false;
        }

        $payload = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $toEmail]
                    ],
                    'subject' => $subject,
                ]
            ],
            'from' => [
                'email' => env('MAIL_FROM_ADDRESS', 'no-reply@example.com'),
                'name' => env('MAIL_FROM_NAME', 'WashWise'),
            ],
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $htmlContent
                ]
            ]
        ];

        try {
            // Short timeout so it fails fast instead of hanging your request
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(10)           // 10 seconds max
                ->post('https://api.sendgrid.com/v3/mail/send', $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('SendGrid API returned failure', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $toEmail,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('SendGrid API request failed', [
                'error' => $e->getMessage(),
                'to' => $toEmail,
            ]);
            return false;
        }
    }

    public function approve($id)
    {
        // make sure this action finishes quickly and won't be killed by mail timeouts
        @ini_set('max_execution_time', '120');
        @set_time_limit(120);

        $owner = CarWashOwner::findOrFail($id);
        $owner->status = 'approved';
        $owner->save();

        // Build simple HTML content or use a blade rendered view if you prefer
        $subject = 'Your wash owner account has been approved';
        $htmlContent = "<p>Hi {$owner->name},</p><p>Your owner account has been approved. You can now log in.</p><p>Thank you,<br>WashWise</p>";

        // Try SendGrid API (won't block longer than the timeout above)
        $sent = $this->sendSendGridEmail($owner->email, $subject, $htmlContent);

        if (! $sent) {
            // Optional: fallback to Laravel Mail (but that may block if SMTP is the issue)
            // Log has been written inside sendSendGridEmail. Return success to admin.
            return redirect()->back()->with('success', 'Owner account approved. (Email could not be delivered — check logs.)');
        }

        return redirect()->back()->with('success', 'Owner account approved. Email sent.');
    }

    public function decline($id)
    {
        $owner = CarWashOwner::findOrFail($id);

        $subject = 'Your wash owner application has been declined';
        $htmlContent = "<p>Hi {$owner->name},</p><p>We are sorry to inform you that your owner application has been declined.</p><p>Regards,<br>WashWise</p>";

        $sent = $this->sendSendGridEmail($owner->email, $subject, $htmlContent);

        // Delete owner regardless of email success (adjust if you prefer different behavior)
        $owner->delete();

        if (! $sent) {
            return back()->with('success', 'Owner declined and removed. (Email could not be delivered — check logs.)');
        }

        return back()->with('success', 'Owner declined, email sent, and account removed.');
    }
}
