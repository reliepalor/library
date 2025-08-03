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
        if (config('app.debug')) {
            Log::info('Profile update request data', [
                'all_fields' => $request->all(),
                'has_file' => $request->hasFile('profile_picture'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            try {
                // Store the old profile picture path for deletion later
                $oldProfilePicture = $user->profile_picture;
                
                // Log the file upload for debugging
                if (config('app.debug')) {
                    Log::info('Profile picture uploaded', [
                        'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                        'file_size' => $request->file('profile_picture')->getSize(),
                        'file_type' => $request->file('profile_picture')->getMimeType(),
                    ]);
                }
                
                // Store the file in the public disk
                $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                
                // Log the stored path
                if (config('app.debug')) {
                    Log::info('Profile picture stored at: ' . $imagePath);
                }
                
                // Update the user's profile picture
                $user->profile_picture = $imagePath;
                
                // Log the profile picture value
                if (config('app.debug')) {
                    Log::info('Profile picture value set to: ' . $user->profile_picture);
                }
                
                // Delete the old profile picture if it exists and is different from the new one
                if ($oldProfilePicture && $oldProfilePicture !== $imagePath) {
                    Storage::disk('public')->delete($oldProfilePicture);
                }
                
                // Save the user immediately after updating the profile picture
                $saved = $user->save();
                
                // Log successful save
                if (config('app.debug')) {
                    Log::info('User profile updated with new picture', [
                        'user_id' => $user->id, 
                        'profile_picture' => $user->profile_picture,
                        'all_attributes' => $user->getAttributes(),
                        'save_result' => $saved
                    ]);
                }
                
                return Redirect::route('user.profile.edit')->with('status', 'profile-updated');
            } catch (\Exception $e) {
                if (config('app.debug')) {
                    Log::error('Profile picture upload failed: ' . $e->getMessage());
                }
                return Redirect::route('user.profile.edit')->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
            }
        }

        // Update other user fields if no file is uploaded
        $user->fill($request->except('profile_picture'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Save the user
        $saved = $user->save();
        
        // Log successful save
        if (config('app.debug')) {
            Log::info('User profile updated', [
                'user_id' => $user->id, 
                'profile_picture' => $user->profile_picture,
                'all_attributes' => $user->getAttributes(),
                'save_result' => $saved
            ]);
        }

        return Redirect::route('user.profile.edit')->with('status', 'profile-updated');
    }
}

