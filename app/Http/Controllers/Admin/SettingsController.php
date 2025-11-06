<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password as PasswordReset;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function getTwoFactor()
    {
        $enabled = Cache::get('logout_2fa_enabled', true);
        return response()->json(['enabled' => (bool)$enabled]);
    }

    public function setTwoFactor(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        Cache::forever('logout_2fa_enabled', (bool)$request->boolean('enabled'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'enabled' => (bool)$request->boolean('enabled')]);
        }

        return back()->with('status', 'Logout 2FA setting updated');
    }

    public function profile()
    {
        $admin = Auth::user(); // Use the default guard since admin middleware checks usertype
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        Log::info('UpdateProfile called', $request->all());

        $admin = Auth::user();

        // Check if this is a profile picture only update (only profile_picture and _token present)
        if ($request->hasFile('profile_picture') && !$request->has('name') && !$request->has('email')) {
            Log::info('Profile picture only update detected');

            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle profile picture upload
            if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
                Storage::disk('public')->delete($admin->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            Log::info('New profile picture path: ' . $path);

            $admin->profile_picture = $path;
            $admin->save();

            Log::info('Profile picture saved successfully');
            return back()->with('success', 'Profile picture updated successfully!');
        }

        // Full profile update
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
                Storage::disk('public')->delete($admin->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $admin->fill($data);
        $admin->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Password changed successfully!');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $admin = Auth::user();

        // Ensure the email matches the authenticated user's email
        if ($admin->email !== $request->email) {
            return back()->withErrors(['email' => 'The email address does not match your account.']);
        }

        // Send password reset link using admin broker
        $status = PasswordReset::broker('admins')->sendResetLink(
            $request->only('email')
        );

        if ($status === PasswordReset::RESET_LINK_SENT) {
            return back()->with('success', 'Password reset link sent to your email!');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
