<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CampusNews;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $campusNews = CampusNews::where('status', 'published')
            ->orderBy('publish_date', 'desc')
            ->get();

        return view('user.dashboard', compact('campusNews'));
    }
}
