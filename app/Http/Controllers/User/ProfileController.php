<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('student.attendanceHistories');
        return view('user.profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Log the request data for debugging
        \Log::info('Profile update request data', [
            'all_fields' => $request->all(),
            'has_file' => $request->hasFile('profile_picture'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        $user->fill($request->except('profile_picture'));

        if ($request->hasFile('profile_picture')) {
            try {
                // Store the old profile picture path for deletion later
                $oldProfilePicture = $user->profile_picture;
                
                // Log the file upload for debugging
                \Log::info('Profile picture uploaded', [
                    'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                    'file_size' => $request->file('profile_picture')->getSize(),
                    'file_type' => $request->file('profile_picture')->getMimeType(),
                ]);
                
                // Process and optimize the image
                $image = $request->file('profile_picture');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                
                // Create image manager with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read image from file
                $imageResource = $manager->read($image);
                
                // Resize and optimize the image
                $imageResource->scaleDown(300, 300);
                
                // Save the optimized image
                $imagePath = 'profile_pictures/' . $imageName;
                $imageResource->toJpeg(80)->save(storage_path('app/public/' . $imagePath));
                
                $user->profile_picture = $imagePath;
                
                // Delete the old profile picture if it exists and is different from the new one
                if ($oldProfilePicture && $oldProfilePicture !== $imagePath) {
                    Storage::disk('public')->delete($oldProfilePicture);
                }
                
                // Log the stored path
                \Log::info('Profile picture stored at: ' . $imagePath);
            } catch (\Exception $e) {
                \Log::error('Profile picture upload failed: ' . $e->getMessage());
                return Redirect::route('user.profile.edit')->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        
        // Log successful save
        \Log::info('User profile updated', [
            'user_id' => $user->id, 
            'profile_picture' => $user->profile_picture,
            'all_attributes' => $user->getAttributes()
        ]);

        return Redirect::route('user.profile.edit')->with('status', 'profile-updated');
    }
}
