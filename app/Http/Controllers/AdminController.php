<?php

namespace App\Http\Controllers;

use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Inertia\Inertia;
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

    // Include the owner's shop and all feedback + user info
    $owners = CarWashOwner::with(['shop.feedback.user'])->get();

    return Inertia::render('settings/AdminDashboard', [
        'users' => $users,
        'owners' => $owners,
    ]);
}

    public function approve($id)
    {
        $owner = CarWashOwner::findOrFail($id);
        $owner->status = 'Approved';
        $owner->save();

        try {
            // Render the approved email Blade file
            $htmlContent = view('emails.approved', ['owner' => $owner])->render();

            // Send with Brevo API
            Http::withHeaders([
                'api-key' => env('SENDINBLUE_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.sendinblue.com/v3/smtp/email', [
                'sender' => [
                    'name'  => 'WashWise',
                    'email' => env('MAIL_FROM_ADDRESS'),
                ],
                'to' => [
                    ['email' => $owner->email, 'name' => $owner->name],
                ],
                'subject' => 'Your WashWise Account Has Been Approved',
                'htmlContent' => $htmlContent,
            ]);

        } catch (\Throwable $e) {
            Log::error('Owner approval email failed', [
                'owner_id' => $owner->id,
                'email' => $owner->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('success', 'Owner account approved. (Email failed — see logs.)');
        }

        return redirect()->back()->with('success', 'Owner account approved and email sent.');
    }

public function decline(Request $request, $id)
{
    $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    $owner = CarWashOwner::findOrFail($id);
    $reason = $request->reason;

    try {
        // Prepare email content
        $htmlContent = view('emails.declined', [
            'owner' => $owner,
            'reason' => $reason,
        ])->render();

        // Send email via SendinBlue
        Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', [
            'sender' => [
                'name'  => 'WashWise',
                'email' => env('MAIL_FROM_ADDRESS'),
            ],
            'to' => [
                ['email' => $owner->email, 'name' => $owner->name],
            ],
            'subject' => 'Your WashWise Account Has Been Declined',
            'htmlContent' => $htmlContent,
        ]);

        // Log decline reason before deletion
        Log::info('Owner declined and deleted', [
            'owner_id' => $owner->id,
            'email' => $owner->email,
            'reason' => $reason,
        ]);

        // Delete the declined account
        $owner->delete();

        return back()->with('success', 'Owner declined, email sent, and account deleted successfully.');

    } catch (\Throwable $e) {
        Log::error('Decline email failed or deletion issue', [
            'owner_id' => $owner->id,
            'email' => $owner->email,
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to send decline email or delete account.');
    }
}

public function approveCustomer($id)
{
    $user = User::findOrFail($id);
    $user->customer_status = 'approved';
    $user->save();

    try {
        $htmlContent = view('emails.approved', ['owner' => $user])->render();

        Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', [
            'sender' => [
                'name'  => 'WashWise',
                'email' => env('MAIL_FROM_ADDRESS'),
            ],
            'to' => [
                ['email' => $user->email, 'name' => $user->name],
            ],
            'subject' => 'Your WashWise Account Has Been Approved',
            'htmlContent' => $htmlContent,
        ]);

    } catch (\Throwable $e) {
        Log::error('Customer approval email failed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'error' => $e->getMessage(),
        ]);

        return back()->with('success', 'Customer approved, but email failed to send.');
    }

    return back()->with('success', 'Customer approved and email sent.');
}


public function declineCustomer(Request $request, $id)
{
    $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    $user = User::findOrFail($id);
    $reason = $request->reason;

    try {
        $htmlContent = view('emails.declined', [
            'owner' => $user, // reusing same email layout
            'reason' => $reason,
        ])->render();

        Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', [
            'sender' => [
                'name'  => 'WashWise',
                'email' => env('MAIL_FROM_ADDRESS'),
            ],
            'to' => [
                ['email' => $user->email, 'name' => $user->name],
            ],
            'subject' => 'Your WashWise Account Has Been Declined',
            'htmlContent' => $htmlContent,
        ]);

        Log::info('Customer declined and deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reason' => $reason,
        ]);

        $user->delete();

        return back()->with('success', 'Customer declined, email sent, and account deleted successfully.');

    } catch (\Throwable $e) {
        Log::error('Customer decline email failed or deletion issue', [
            'user_id' => $user->id,
            'email' => $user->email,
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to send decline email or delete account.');
    }
}
}
