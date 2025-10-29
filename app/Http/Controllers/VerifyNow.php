<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Auth;

class VerifyNow extends Controller
{
    public function index()
    {
        return inertia('settings/Verify');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_number' => 'required|numeric',
            'picture_id' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $data = [
            'id_number' => $request->id_number,
        ];

        // ✅ Cloudinary upload for picture ID
        if ($request->hasFile('picture_id')) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true],
            ]);

            $file = $request->file('picture_id');
            $response = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'user_ids',
                'resource_type' => 'image',
            ]);
            $data['picture_id'] = $response['secure_url'];
        }

        $user->update($data);

        return back()->with('success', 'Verification details submitted successfully!');
    }
}
