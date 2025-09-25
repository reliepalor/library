<?php

namespace App\Http\Controllers;

use App\Models\CampusNews;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with campus news.
     */
    public function index()
    {
        $campusNews = CampusNews::where('status', 'published')
            ->orderBy('publish_date', 'desc')
            ->get();

        return view('welcome', compact('campusNews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Display the specified campus news item.
     */
    public function showNews(CampusNews $campusNews)
    {
        // Only show published news
        if ($campusNews->status !== 'published') {
            abort(404);
        }

        // Increment view count
        $campusNews->incrementViews();

        return view('campus-news.show', compact('campusNews'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
