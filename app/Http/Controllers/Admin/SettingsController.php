<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
}
