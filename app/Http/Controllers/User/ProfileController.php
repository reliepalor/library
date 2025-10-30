<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load(['student.attendanceHistories', 'teacherVisitor']);
        $reservations = $user->reservations()->with('book')->get();
        return view('user.profile.edit', compact('user', 'reservations'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        Log::info('Profile update attempt', [
            'user_id' => $user->id,
            'has_file' => $request->hasFile('profile_picture'),
            'all_data' => $request->all()
        ]);

        try {
            return DB::transaction(function () use ($request, $user) {
                $changesMade = false;

                // Handle profile picture - always treat as change when file is uploaded
                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                    
                    // Validate file
                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file upload');
                    }
                    
                    $extension = strtolower($file->getClientOriginalExtension());
                    $imageName = 'profile_' . $user->id . '_' . time() . '.' . $extension;
                    $imagePath = 'profile_pictures/' . $imageName;

                    // Ensure directory exists
                    if (!Storage::disk('public')->exists('profile_pictures')) {
                        Storage::disk('public')->makeDirectory('profile_pictures');
                    }

                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($file->getRealPath());
                    $image->scale(300, 300);

                    // Save new image
                    $saved = Storage::disk('public')->put($imagePath, (string) $image->encode());
                    
                    if (!$saved) {
                        throw new \Exception('Failed to save image');
                    }

                    // Delete old image if exists
                    if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                        Storage::disk('public')->delete($user->profile_picture);
                    }

                    $user->profile_picture = $imagePath;
                    $changesMade = true;
                    
                    Log::info('Profile picture uploaded', [
                        'user_id' => $user->id,
                        'image_path' => $imagePath,
                        'file_size' => $file->getSize()
                    ]);
                }

                // Handle name change (only if provided)
                if ($request->has('name') && $user->name !== $request->name) {
                    $user->name = $request->name;
                    $changesMade = true;
                }

                // Handle email change (only if provided)
                if ($request->has('email') && $user->email !== $request->email) {
                    $user->email = $request->email;
                    $user->email_verified_at = null;
                    $changesMade = true;
                }

                // Always save if profile picture was uploaded
                if ($request->hasFile('profile_picture')) {
                    $changesMade = true;
                }

                if ($changesMade) {
                    $saved = $user->save();
                    
                    if (!$saved) {
                        throw new \Exception('Failed to save user data');
                    }
                    
                    Log::info('Profile updated successfully', [
                        'user_id' => $user->id,
                        'profile_picture' => $user->profile_picture
                    ]);
                    
                    return Redirect::route('user.profile.edit')->with('status', 'profile-updated');
                }

                // Only show "no changes" if truly no changes
                return Redirect::route('user.profile.edit')
                    ->with('info', 'No changes were made.');
            });
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Redirect::route('user.profile.edit')
                ->withInput()
                ->withErrors(['profile_picture' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }
}
