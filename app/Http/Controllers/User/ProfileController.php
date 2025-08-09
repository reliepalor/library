<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        Log::info('Profile update request initiated', [
            'user_id' => $user->id,
            'has_file' => $request->hasFile('profile_picture'),
            'file_details' => $request->hasFile('profile_picture') ? [
                'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                'file_size' => $request->file('profile_picture')->getSize(),
                'file_type' => $request->file('profile_picture')->getMimeType(),
                'file_extension' => $request->file('profile_picture')->getClientOriginalExtension(),
            ] : null,
        ]);

        try {
            // Use database transaction to ensure atomicity
            return DB::transaction(function () use ($request, $user) {
                // Handle profile picture upload
                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                    
                    // Validate file size and type
                    if ($file->getSize() > 2048 * 1024) { // 2MB
                        throw new \Exception('Profile picture must be less than 2MB.');
                    }
                    
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        throw new \Exception('Only JPG, JPEG, and PNG files are allowed.');
                    }
                    
                    // Create image manager and optimize image
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($file->getRealPath());
                    $image->scale(300, 300); // Resize to manageable dimensions
                    $imageName = 'profile_' . $user->id . '_' . time() . '.' . $extension;
                    $imagePath = 'profile_pictures/' . $imageName;
                    
                    // Store the optimized image
                    Storage::disk('public')->put($imagePath, (string) $image->encode());
                    
                    // Store the old profile picture path
                    $oldProfilePicture = $user->profile_picture;
                    
                    // Update the user's profile picture path
                    $user->profile_picture = $imagePath;
                    
                    // Delete the old profile picture if it exists
                    if ($oldProfilePicture && $oldProfilePicture !== $imagePath && Storage::disk('public')->exists($oldProfilePicture)) {
                        Storage::disk('public')->delete($oldProfilePicture);
                    }
                }

                // Update other user fields if provided
                $user->fill($request->except('profile_picture'));

                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }

                // Save the user
                if (!$user->save()) {
                    throw new \Exception('Failed to save profile changes to database.');
                }

                Log::info('Profile update successful', [
                    'user_id' => $user->id,
                    'profile_picture' => $user->profile_picture ?? 'No new picture uploaded',
                ]);

                return Redirect::route('user.profile.edit')->with('status', 'profile-updated');
            });
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Redirect::route('user.profile.edit')->withErrors(['profile_picture' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }
}