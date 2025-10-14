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
        $owner->status = 'approved';
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
    $owner->decline_reason = $request->reason;
    $owner->status = 'declined';
    $owner->save();

    try {
        $htmlContent = view('emails.declined', [
            'owner' => $owner,
            'reason' => $request->reason,
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
                ['email' => $owner->email, 'name' => $owner->name],
            ],
            'subject' => 'Your WashWise Account Has Been Declined',
            'htmlContent' => $htmlContent,
        ]);

    } catch (\Throwable $e) {
        Log::error('Owner declined email failed', [
            'owner_id' => $owner->id,
            'email' => $owner->email,
            'error' => $e->getMessage(),
        ]);
    }

    return back()->with('success', 'Owner declined and email sent with reason.');
}
}
