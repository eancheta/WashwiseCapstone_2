<?php

namespace App\Http\Controllers;

use App\Models\CarWashOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Mail\OwnerDeclinedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\OwnerApprovedMail;


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

    public function approve($id)
    {
    $owner = CarWashOwner::findOrFail($id);
    $owner->status = 'approved';
    $owner->save();

    Mail::to($owner->email)->send(new OwnerApprovedMail($owner));

    return redirect()->back()->with('success', 'Owner account approved.');
    }

    public function decline($id)
    {
    $owner = CarWashOwner::findOrFail($id);

    Mail::to($owner->email)->send(new OwnerDeclinedMail($owner));

    $owner->delete();

    return back()->with('success', 'Owner declined, email sent, and account removed.');
    }
}
